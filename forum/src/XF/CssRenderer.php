<?php

namespace XF;

use XF\Template\Templater;

class CssRenderer
{
	/**
	 * @var App
	 */
	protected $app;

	/**
	 * @var Templater
	 */
	protected $templater;

	/**
	 * @var Style
	 */
	protected $style;

	protected $renderParams = [];

	/**
	 * @var \Doctrine\Common\Cache\CacheProvider|null
	 */
	protected $cache;

	protected $allowCached = true;
	protected $allowFinalCacheUpdate = true;
	protected $useDevModeCache = false;
	protected $cacheModifierKey = '';

	protected $includeExtraParams = true;

	protected $lastRenderedTemplate = null;

	/**
	 * @var \Less_Parser|null
	 */
	protected $lessParser = null;
	protected $lessPrepend = null;

	public function __construct(App $app, Templater $templater, \Doctrine\Common\Cache\CacheProvider $cache = null)
	{
		$this->app = $app;
		$this->templater = $templater;
		$this->cache = $cache;

		$config = $app->config();
		if ($config['development']['enabled'] || $config['designer']['enabled'])
		{
			$this->allowCached = false;
			$this->useDevModeCache = true;
		}

		if (\XF::$debugMode)
		{
			$this->cacheModifierKey = md5('debug');
		}

		$style = $templater->getStyle();
		if (!$style)
		{
			$style = $this->app->get('style.fallback');
		}
		$this->setStyle($style);
	}

	public function setStyle(\XF\Style $style)
	{
		if ($style->getId() === 0)
		{
			$hueShift = $this->app->config('adminColorHueShift');
			if ($hueShift)
			{
				$properties = $this->app->service('XF:StyleProperty\Rebuild')->getMasterPropertiesWithHueShift($hueShift);
				$style->setProperties($properties);

				$this->cacheModifierKey = md5($this->cacheModifierKey . 'hueShift=' . $hueShift);
			}
		}

		$this->style = $style;
		$this->templater->setStyle($style);
	}

	public function render($templates, $includeExtraParams = true)
	{
		if (!is_array($templates))
		{
			$templates = [$templates];
		}
		$templates = $this->filterValidTemplates($templates);
		if (!$templates)
		{
			return '';
		}

		$this->includeExtraParams = $includeExtraParams;

		$output = $this->getFinalCachedOutput($templates);
		if (!$output)
		{
			if ($this->allowCached)
			{
				$cached = $this->getIndividualCachedTemplates($templates);
			}
			else
			{
				$cached = [];
			}

			$output = $this->renderTemplates($templates, $cached, $errors);
			$output = $this->prepareErrorOutput($errors) . $output;
			$this->cacheFinalOutput($templates, $output);
		}

		return $output;
	}

	protected function filterValidTemplates(array $templates)
	{
		foreach ($templates AS $key => $template)
		{
			if (!preg_match('/^[a-z0-9_]+:[a-z0-9_.]+$/i', $template))
			{
				unset($templates[$key]);
				continue;
			}

			switch (strrchr($template, '.'))
			{
				case '.css':
				case '.less':
					break;

				default:
					unset($templates[$key]);
			}
		}

		return $templates;
	}

	protected function getFinalCachedOutput(array $templates)
	{
		if (!$this->allowCached || !$this->cache || !$this->includeExtraParams)
		{
			return false;
		}

		$key = $this->getFinalCacheKey($templates);
		return $this->cache->fetch($key);
	}

	protected function cacheFinalOutput(array $templates, $output)
	{
		if (!is_string($output) || !strlen($output))
		{
			return;
		}

		if ($this->allowCached && $this->allowFinalCacheUpdate && $this->cache && $this->includeExtraParams)
		{
			$this->cache->save($this->getFinalCacheKey($templates), $output, 3600);
		}
	}

	protected function getFinalCacheKey(array $templates)
	{
		$elements = $this->getCacheKeyElements();

		$templates = array_unique($templates);
		sort($templates);

		return 'xfCssCache_' . md5(
			'templates=' . implode(',', $templates)
			. 'style=' . $elements['style_id']
			. 'modified=' . $elements['style_last_modified']
			. 'language=' . $elements['language_id']
			. $elements['modifier']
		);
	}

	protected function getCacheKeyElements()
	{
		$style = $this->style;
		$modified = $style ? $style->getLastModified() : 0;

		return [
			'style_id' => $this->templater->getStyleId(),
			'style_last_modified' => $modified,
			'language_id' => $this->templater->getLanguage()->getId(),
			'modifier' => $this->cacheModifierKey
		];
	}

	protected function getIndividualCachedTemplates(array $templates)
	{
		if (!$templates)
		{
			return [];
		}

		$elements = $this->getCacheKeyElements();
		$db = $this->app->db();

		return $db->fetchPairs("
			SELECT title, output
			FROM xf_css_cache
			WHERE title IN (" . $db->quote($templates) . ")
				AND style_id = ?
				AND language_id = ?
				AND modifier_key = ?
				AND cache_date >= ?
		", [
				$elements['style_id'],
				$elements['language_id'],
				$elements['modifier'],
				$elements['style_last_modified']
			]
		);
	}

	protected function getIndividualCachedTemplate($template)
	{
		$output = $this->getIndividualCachedTemplates([$template]);
		return isset($output[$template]) ? $output[$template] : null;
	}

	protected function renderTemplates(array $templates, array $cached = [], array &$errors = null)
	{
		$output = [];
		$errors = [];
		$this->renderParams = $this->getRenderParams();

		foreach ($templates AS $template)
		{
			$templateIdentifier = "/********* $template ********/\n";

			if (isset($cached[$template]))
			{
				$output[$template] = $templateIdentifier .  $cached[$template];
			}
			else
			{
				$rendered = $this->renderTemplate($template, $error);
				if (is_string($rendered))
				{
					if ($this->includeExtraParams && ($this->allowCached || $this->useDevModeCache))
					{
						$this->cacheTemplate($template, $rendered);
					}

					$output[$template] = $templateIdentifier . $rendered;
				}
				else if ($error)
				{
					$errors[$template] = $error;
				}
			}
		}

		return implode("\n\n", $output);
	}

	protected function getRenderParams()
	{
		$style = $this->templater->getStyle();
		$language = $this->templater->getLanguage();

		$params = [
			'xf' =>	[
				'versionId' => \XF::$versionId,
				'version' => \XF::$version,
				'app' => $this->app,
				'time' => \XF::$time,
				'debug' => \XF::$debugMode,
				'language' => $language,
				'style' => $style,
				'isRtl' => $language->isRtl(),
				'options' => $this->app->options()
			]
		];

		if ($this->includeExtraParams)
		{
			try
			{
				$params['reactionColors'] = $this->app->container('reactionColors');
				$params['reactionSprites'] = $this->app->container('reactionSprites');
			}
			catch (\Exception $e)
			{
				$params['reactionColors'] = [];
				$params['reactionSprites'] = [];
			}

			$params['smilieSprites'] = $this->app->container('smilieSprites');
			$params['displayStyles'] = $this->app->container('displayStyles');
		}

		$this->templater->addDefaultParams($params);

		return $params;
	}

	public function renderTemplate($template, &$error = null, &$updateCache = true)
	{
		if (!$this->templater->isKnownTemplate($template))
		{
			return false;
		}

		try
		{
			$this->lastRenderedTemplate = $template;

			$error = null;
			$output = $this->templater->renderTemplate($template, $this->renderParams, false);
			$updateCache = true;

			if (
				$this->includeExtraParams
				&& !$this->allowCached
				&& $this->useDevModeCache
				&& !$this->templater->hasWatcherActionedTemplates()
			)
			{
				// if we haven't touched any templates in this pipeline, we can look for a cached template
				$rendered = $this->getIndividualCachedTemplate($template);
				if (is_string($rendered))
				{
					$updateCache = false;
					return $rendered;
				}
			}

			return trim($this->renderToCss($template, $output));
		}
		catch (CssRenderException $e)
		{
			\XF::logException($e, false, "Error rendering template $template: ");
			$error = $e->getMessage() . "\n" . implode("\n", $e->getContextLinesPrintable());
			return false;
		}
		catch (\Exception $e)
		{
			\XF::logException($e, false, "Error rendering template $template: ");
			$error = $e->getMessage();
			return false;
		}
	}

	/**
	 * When given a color value which may contain a mix of XF and Less functions test and return the parsed color.
	 * If the provided Less is invalid, or no valid color found, returns null.
	 *
	 * @param $contents
	 * @return null|string
	 */
	public function parseLessColorValue($value)
	{
		$parser = $this->getFreshLessParser();

		$value = '@someVar: ' . $value . '; #test { color: @someVar; }';
		$value = $this->prepareLessForRendering($value);

		try
		{
			$css = $parser->parse($value)->getCss();
		}
		catch (\Exception $e)
		{
			return null;
		}

		preg_match('/color:\s*(#(?:[0-9a-f]{2}){2,4}|(#[0-9a-f]{3})|(rgb|hsl)a?\((-?\d+%?[,\s]+){2,3}\s*[\d\.]+%?\))/i', $css, $matches);

		if (!$matches || !isset($matches[1]))
		{
			return null;
		}

		return $matches[1];
	}

	protected function renderToCss($template, $output)
	{
		switch (strrchr($template, '.'))
		{
			case '.less':
				$parser = $this->getFreshLessParser();

				$output = $this->prepareLessForRendering($output);
				$output = $this->getLessPrepend() . $output;
				$renderContents = $output;

				try
				{
					$output = $parser->parse($output)->getCss();
				}
				catch (\Less_Exception_Parser $e)
				{
					throw CssRenderException::createFromLessException($e, $template, $renderContents);
				}
				break;

			default:
				$output = $this->prepareCssForRendering($output);
		}

		return $this->processRenderedCss($output);
	}

	protected function prepareLessForRendering($contents)
	{
		$contents = $this->processPropertyComments($contents);
		$contents = $this->processStylePropertyShorthand($contents);
		$contents = $this->processColorAdjustFunctions($contents);
		$contents = $this->processColorFunctionSafety($contents);
		$contents = $this->processAdditionalFunctions($contents);

		return $contents;
	}

	protected function prepareCssForRendering($contents)
	{
		// note that we don't parse properties in CSS as they're likely to require LESS functions
		return $contents;
	}

	protected function processRenderedCss($css)
	{
		$css = preg_replace_callback(
			'/-xf-select-gadget:([^;}]*)(;|})/i',
			function ($match)
			{
				$color = trim($match[1]);
				if (!$color)
				{
					return '';
				}

				$color = \XF::escapeString($color, 'datauri');
				$url = "data:image/svg+xml,"
					. "%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 4.9 10'%3E"
					. "%3Cstyle%3E._xfG%7Bfill:" . $color . ";%7D%3C/style%3E"
					. "%3Cpath class='_xfG' d='M1.4 4.7l1.1-1.5 1 1.5m0 .6l-1 1.5-1.1-1.5'/%3E%3C/svg%3E";

				return 'background-image: url("' . $url . '") !important' . $match[2];
			},
			$css
		);

		return $css;
	}

	protected function processPropertyComments($contents)
	{
		$contents = preg_replace_callback('#//([^\n]+)#', function($match)
		{
			$comment = preg_replace('#(?<=[^a-z0-9-])xf-#i', '_xf-', $match[0]);

			return $comment;
		}, $contents);

		return $contents;
	}

	protected function processStylePropertyShorthand($contents)
	{
		$contents = preg_replace_callback(
			'/
				(?<=[^a-z0-9-])
				xf-default\(\s*
				@xf-(?P<prop> [a-z0-9_]+(?!-[a-z0-9_]) (\--[a-z0-9_-]+)? )
				\s*,\s*
				(?P<fallback> (?> [^()]* (\( (?P>fallback) \))? )+ )
				\s*\)
			/ix',
			function($match)
			{
				$prop = $this->style->getProperty($match['prop'], '');
				if (!is_scalar($prop))
				{
					$prop = '';
				}
				else
				{
					$prop = strval($prop);
				}

				return strlen($prop) > 0 ? $prop : $match['fallback'];
			},
			$contents
		);

		$contents = preg_replace_callback('/@xf-([a-z0-9_]+(?!-[a-z0-9_])(\--[a-z0-9_-]+)?)/i', function($match)
		{
			$value = $this->style->getProperty($match[1], '');
			return is_scalar($value) ? $value : '';
		}, $contents);

		$contents = preg_replace_callback('/@{xf-([a-z0-9_]+(\--[a-z0-9_-]+)?)}/i', function($match)
		{
			$value = $this->style->getProperty($match[1], '');
			return is_scalar($value) ? $value : '';
		}, $contents);

		$contents = preg_replace_callback('/\.xf-([a-z0-9_]+)(\(([^)]+)*\))?(\s+?!important)?;?/i', function($match)
		{
			$filters = isset($match[3]) ? trim($match[3]) : null;
			$retVal = $this->style->getCssProperty($match[1], $filters);
			if (isset($match[4]))
			{
				$retVal .= "/* !important directive is not supported in CSS style properties! */";
			}
			return $retVal;
		}, $contents);

		return $contents;
	}

	protected function processColorAdjustFunctions($contents)
	{
		if ($this->style->getProperty('styleType', 'light') == 'light')
		{
			$intensify = 'darken';
			$diminish = 'lighten';
		}
		else
		{
			$intensify = 'lighten';
			$diminish = 'darken';
		}

		$contents = preg_replace('/(?<=[^a-z0-9-])xf-intensify\(/i', "{$intensify}(", $contents);
		$contents = preg_replace('/(?<=[^a-z0-9-])xf-diminish\(/i', "{$diminish}(", $contents);

		return $contents;
	}

	protected $adjustColorFunctions = [
		'saturate',
		'desaturate',
		'lighten',
		'darken',
		'fadein',
		'fadeout',
		'fade',
		'spin',
		'tint',
		'shade'
	];

	protected function processColorFunctionSafety($contents)
	{
		$fns = implode('|', $this->adjustColorFunctions);

		$contents = preg_replace('/(?<=[^a-z0-9-])(' . $fns . ')\s*\(\s*,/i', "\\1(transparent,", $contents);

		return $contents;
	}

	protected function processAdditionalFunctions($contents)
	{
		$language = $this->templater->getLanguage();

		$contents = preg_replace_callback(
			'/(?<=[^a-z0-9-])xf-option\(\'([a-z0-9_.]+)\'(,\s*([^\\)]+)\s*)?\)/i',
			function (array $match)
			{
				$options = \XF::options();

				if (strpos($match[1], '.') !== false)
				{
					$keys = explode('.', $match[1], 2);
					$result = isset($options[$keys[0]][$keys[1]]) ? $options[$keys[0]][$keys[1]] : null;
				}
				else
				{
					$result = isset($options[$match[1]]) ? $options[$match[1]] : null;
				}

				if ($result && isset($match[2], $match[3]))
				{
					$result .= trim($match[3]);
				}

				return $result;

			},
			$contents
		);

		$contents = preg_replace_callback(
			'/(?<=[^a-z0-9-])xf-is(Rtl|Ltr)/i',
			function (array $match) use ($language)
			{
				if (strtolower($match[1]) == 'rtl')
				{
					return ($language->isRtl() ? 'true' : 'false');
				}
				else
				{
					return ($language->isRtl() ? 'false' : 'true');
				}
			},
			$contents
		);

		return $contents;
	}

	/**
	 * @return \Less_Parser
	 */
	protected function getLessParser()
	{
		if (!$this->lessParser)
		{
			// TODO: option to enable source maps (has potential issues)

			$isRtl = $this->templater->getLanguage()->isRtl();

			$options = [
				'import_callback' => [$this, 'handleLessImport'],
				'compress' => \XF::$debugMode ? false : true,
				'plugins' => [
					new \XF\Less\RtlVisitorPre(),
					new \XF\Less\RtlVisitor($isRtl)
				]
			];
			$this->lessParser = new \Less_Parser($options);
		}

		return $this->lessParser;
	}

	/**
	 * @return \Less_Parser
	 */
	protected function getFreshLessParser()
	{
		if ($this->lessParser)
		{
			$this->lessParser->Reset();
		}

		return $this->getLessParser();
	}

	protected function getLessPrepend()
	{
		if ($this->lessPrepend === null)
		{
			// this method will be more useful if we do parsed caching or source maps
			//$this->lessPrepend = '@import "public:setup.less";' . "\n\n";

			$prepend = trim($this->templater->renderTemplate('public:setup.less', $this->renderParams)) . "\n\n";
			$prepend = $this->prepareLessForRendering($prepend);
			$this->lessPrepend = $prepend;
		}

		return $this->lessPrepend;
	}

	public function handleLessImport(\Less_Tree_Import $import)
	{
		$template = $import->getPath();
		$parts = explode(':', $template, 2);
		if (!isset($parts[1]))
		{
			if ($this->lastRenderedTemplate)
			{
				$lastTemplate = explode(':', $this->lastRenderedTemplate, 2);
				if (isset($lastTemplate[1]))
				{
					$template = "$lastTemplate[0]:$template";
				}
			}
			else
			{
				throw new \Exception("Cannot process LESS import '$template' without a template type");
			}
		}

		$tempFile = \XF\Util\File::getTempFile();
		file_put_contents($tempFile, $this->prepareLessForRendering($this->templater->renderTemplate($template, $this->renderParams)));

		return [$tempFile, $template];
	}

	public function cacheTemplate($title, $output)
	{
		$this->app->db()->insert('xf_css_cache', [
			'style_id' => $this->style->getId(),
			'language_id' => $this->getLanguageId(),
			'title' => $title,
			'modifier_key' => $this->cacheModifierKey,
			'output' => $output,
			'cache_date' => $this->style->getLastModified()
		], false, 'output = VALUES(output), cache_date = VALUES(cache_date)');
	}

	public function prepareErrorOutput(array $errors)
	{
		if (!$errors || !\XF::$debugMode)
		{
			return '';
		}

		$errorOutput = [];
		foreach ($errors AS $template => $error)
		{
			$errorOutput[] = strtr("* $template: $error", [
				'\\' => '\\\\',
				'\'' => '',
				"\r" => '',
				"\n" => '\A '
			]);
		}

		return 'body:before { display: block; content: \'Errors occurred when rendering CSS:\A ' . implode('\A \A ', $errorOutput)
			. '\'; background: yellow; color: black; padding: 10px; margin: 10px; white-space: pre-wrap; }'
			. "\n\n";
	}

	public function getTemplater()
	{
		return $this->templater;
	}

	public function setTemplater(Templater $templater)
	{
		$this->templater = $templater;
	}

	public function getStyleId()
	{
		return $this->templater->getStyleId();
	}

	public function getLanguageId()
	{
		return $this->templater->getLanguage()->getId();
	}

	public function getLastModifiedDate()
	{
		if ($this->style)
		{
			return $this->style->getLastModified();
		}
		else
		{
			return \XF::$time;
		}
	}

	public function setAllowCached($value)
	{
		$this->allowCached = (bool)$value;
		if (!$this->allowCached)
		{
			// if we're explicitly setting this, then don't allow the dev mode cache either
			$this->useDevModeCache = false;
		}
	}

	public function getAllowCached()
	{
		return $this->allowCached;
	}

	public function setAllowFinalCacheUpdate($value)
	{
		$this->allowFinalCacheUpdate = (bool)$value;
	}

	public function getAllowFinalCacheUpdate()
	{
		return $this->allowFinalCacheUpdate;
	}
}