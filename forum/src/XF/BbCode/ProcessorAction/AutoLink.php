<?php

namespace XF\BbCode\ProcessorAction;

use XF\BbCode\Processor;
use XF\Http\Metadata;

class AutoLink implements FiltererInterface
{
	/**
	 * @var \XF\App
	 */
	protected $app;

	protected $autoEmbed = true;
	protected $autoEmbedLink = '';
	protected $maxEmbed = PHP_INT_MAX;
	protected $embedSites = [];

	protected $embedRemaining = PHP_INT_MAX;

	protected $urlToPageTitle = false;
	protected $urlToTitleFormat = '';
	protected $urlToTitleTimeLimit = 10;

	protected $urlToRichPreview = true;

	/**
	 * @var Metadata[]
	 */
	protected $unfurlCache = [];

	protected $startTime;

	public function __construct(\XF\App $app, array $config = [])
	{
		$this->app = $app;

		$baseConfig = [
			'autoEmbed' => true,
			'autoEmbedLink' => '',
			'maxEmbed' => PHP_INT_MAX,
			'embedSites' => [],
			'urlToPageTitle' => false,
			'urlToTitleFormat' => '',
			'urlToTitleTimeLimit' => 10,
			'urlToRichPreview' => true,
			'urlUnfurl' => true
		];
		$config = array_replace($baseConfig, $config);

		$this->autoEmbed = $config['autoEmbed'];
		$this->autoEmbedLink = $config['autoEmbedLink'];
		$this->maxEmbed = $config['maxEmbed'];
		$this->embedSites = $config['embedSites'];

		$this->urlToPageTitle = $config['urlToPageTitle'];
		$this->urlToTitleFormat = $config['urlToTitleFormat'];
		$this->urlToTitleTimeLimit = $config['urlToTitleTimeLimit'];

		$this->urlToRichPreview = $config['urlToRichPreview'];
		$this->urlUnfurl = $config['urlUnfurl'];

		$this->startTime = microtime(true);
	}

	public function addFiltererHooks(FiltererHooks $hooks)
	{
		$hooks->addSetupHook('filterSetup')
			->addStringHook('filterString')
			->addTagHook('url', 'filterUrlTag');
	}

	public function enableUnfurling($enable = true)
	{
		$this->urlToRichPreview = (bool)$enable;

		return $this;
	}

	public function filterSetup(array $ast)
	{
		$this->embedRemaining = $this->maxEmbed;

		$mediaTotal = 0;
		$f = function(array $tree) use (&$mediaTotal, &$f)
		{
			foreach ($tree AS $entry)
			{
				if (is_array($entry))
				{
					if ($entry['tag'] == 'media')
					{
						$mediaTotal++;
					}

					$f($entry['children']);
				}
			}
		};

		$f($ast);

		$this->embedRemaining -= $mediaTotal;
	}

	public function filterUrlTag(array $tag, array $options, Processor $processor)
	{
		if ($this->autoEmbed)
		{
			$url = $processor->renderSubTreePlain($tag['children']);
			if (empty($tag['option']) || $tag['option'] == $url)
			{
				$output = $this->autoLinkUrl($url);

				if ($output)
				{
					return $output;
				}
			}
		}

		return null;
	}

	public function filterString($string, array $options, Processor $processor)
	{
		if (!empty($options['stopAutoLink']))
		{
			return $string;
		}

		$autoLinkRegex = '(?<=[^a-z0-9@/\.-]|^)(?<!\]\(|url=(?:"|\')|url\]|url\sunfurl=(?:"|\')true(?:"|\')\]|img\])(https?://|www\.)[^\s"<>{}`]+';
		$unfurlLinkRegex = '^' . $autoLinkRegex . '$';

		if ($this->urlToRichPreview)
		{
			// attempt to unfurl URLs if enabled
			$string = preg_replace_callback(
				'#' . $unfurlLinkRegex . '#ium',
				function ($match) use ($processor)
				{
					$output = $this->unfurlLinkUrl($match[0]);

					if (!$output)
					{
						// cannot be unfurled, will be auto linked as normal.
						return $match[0];
					}

					$this->incrementMatchedTag($processor, $output);
					return $output;
				},
				$string
			);
		}

		$string = preg_replace_callback(
			'#' . $autoLinkRegex . '#iu',
			function ($match) use ($processor)
			{
				$output = $this->preAutoLinkUrl($match[0], $processor);
				if (!$output)
				{
					return $match[0];
				}
				$this->incrementMatchedTag($processor, $output);
				return $output;
			},
			$string
		);

		if (strpos($string, '@') !== false)
		{
			// assertion to prevent matching email in url matched above (user:pass@example.com)
			$string = preg_replace_callback(
				'#[a-z0-9.+_-]+@[a-z0-9-]+(\.[a-z]+)+(?![^\s"]*\[/url\])#iu',
				function ($match) use ($processor)
				{
					$this->incrementTagUsageCount($processor, 'email');
					return '[email]' . $match[0] . '[/email]';
				},
				$string
			);
		}

		return $string;
	}

	public function preAutoLinkUrl($url, Processor $processor)
	{
		// if we have a limit tags filterer and auto embed is enabled, disable
		// auto embedding if the media tag is disabled, otherwise auto linking
		// may bypass the limiting of the tag.
		$limit = $processor->getFilterer('limit');

		if ($limit && $limit instanceof LimitTags && $this->autoEmbed)
		{
			if ($limit->isTagDisabled('media'))
			{
				$this->autoEmbed = false;
			}
		}

		return $this->autoLinkUrl($url);
	}

	public function autoLinkUrl($url)
	{
		$link = $this->app->stringFormatter()->prepareAutoLinkedUrl($url);

		if (!$link['url'])
		{
			return null;
		}

		if ($link['url'] === $link['linkText'])
		{
			$mediaTag = $this->getMediaTagIfPermitted($link['url']);

			if ($mediaTag)
			{
				$tag = $mediaTag;
				$this->embedRemaining--;
			}
			else
			{
				$tag = $this->getUrlBbCode($link['url']);
			}
		}
		else
		{
			$tag = '[URL="' . $link['url'] . '"]' . $link['linkText'] . '[/URL]';
		}

		return $tag . $link['suffixText'];
	}

	protected function getMediaTagIfPermitted($url)
	{
		if (!$this->autoEmbed || !$this->embedRemaining)
		{
			return false;
		}

		return $this->getEmbedBbCode($url);
	}

	protected function getUrlBbCode($url)
	{
		if ($this->urlToPageTitle)
		{
			$title = $this->getUrlTitle($url);
			if ($title)
			{
				$format = $this->urlToTitleFormat ?: '{title}';
				$tokens = [
					'{title}' => $title,
					'{url}' => $url
				];
				$linkTitle = strtr($format, $tokens);

				$tag = '[URL="' . $url . '"]' . $linkTitle . '[/URL]';
			}
			else
			{
				$tag = '[URL]' . $url . '[/URL]';
			}
		}
		else
		{
			$tag = '[URL]' . $url . '[/URL]';
		}

		return $tag;
	}

	/**
	 * @deprecated use MetadataFetcher and Metadata objects instead
	 *
	 * @param $url
	 *
	 * @return bool|mixed|null|string|string[]
	 */
	protected function getValidRequestUrl($url)
	{
		$requestUrl = preg_replace('/#.*$/', '', $url);
		if (preg_match_all('/[^A-Za-z0-9._~:\/?#\[\]@!$&\'()*+,;=%-]/', $requestUrl, $matches))
		{
			foreach ($matches[0] AS $match)
			{
				$requestUrl = str_replace($match[0], '%' . strtoupper(dechex(ord($match[0]))), $requestUrl);
			}
		}

		if ($this->canFetchUrlHtml($requestUrl))
		{
			return $requestUrl;
		}

		return false;
	}

	/**
	 * @deprecated use MetadataFetcher and Metadata objects instead
	 *
	 * @param $requestUrl
	 *
	 * @return bool
	 */
	protected function canFetchUrlHtml($requestUrl)
	{
		if ($requestUrl != $this->app->stringFormatter()->censorText($requestUrl))
		{
			return false;
		}

		if ($this->urlToTitleTimeLimit && microtime(true) - $this->startTime > $this->urlToTitleTimeLimit)
		{
			return false;
		}

		return true;
	}

	protected function fetchMetadataFromUrl($requestUrl)
	{
		if (array_key_exists($requestUrl, $this->unfurlCache))
		{
			return $this->unfurlCache[$requestUrl];
		}

		$fetcher = $this->app->http()->metadataFetcher();

		$metadata = $fetcher->fetch($requestUrl, $null, $this->startTime, $this->urlToTitleTimeLimit);
		if (!$metadata)
		{
			return null;
		}

		$this->unfurlCache[$requestUrl] = $metadata;

		return $metadata;
	}

	/**
	 * @deprecated use MetadataFetcher and Metadata objects instead
	 *
	 * @param $requestUrl
	 *
	 * @return array|bool
	 */
	protected function fetchUrlHtml($requestUrl)
	{
		$response = $this->app->http()->reader()->getUntrusted(
			$requestUrl,
			[
				'time' => 5,
				'bytes' => 1.5 * 1024 * 1024
			]
		);
		if (!$response || $response->getStatusCode() != 200)
		{
			return false;
		}

		$contentType = $response->getHeader('Content-type');
		$charset = $this->app->http()->reader()->getCharset($contentType);

		return [
			'body' => $response->getBody()->read(50 * 1024),
			'charset' => $charset
		];
	}

	protected function getUrlTitle($url)
	{
		$metadata = $this->fetchMetadataFromUrl($url);

		if (!$metadata)
		{
			return false;
		}

		$title = $metadata->getTitle();

		if (!strlen($title))
		{
			return false;
		}

		$bbCodeContainer = $this->app->bbCode();

		/** @var \XF\BbCode\ProcessorAction\AnalyzeUsage $usage */
		$usage = $bbCodeContainer->processorAction('usage');

		$bbCodeContainer->processor()
			->addProcessorAction('usage', $usage)
			->render($title, $bbCodeContainer->parser(), $bbCodeContainer->rules('base'));

		if ($usage->getSmilieCount() || $usage->getTotalTagCount())
		{
			$title = "[PLAIN]{$title}[/PLAIN]";
		}

		return $title;
	}

	public function unfurlLinkUrl($url)
	{
		$link = $this->app->stringFormatter()->prepareAutoLinkedUrl($url);

		if ($link['url'] !== $link['linkText'])
		{
			return false;
		}

		$mediaTag = $this->getMediaTagIfPermitted($link['url']);
		if ($mediaTag)
		{
			// can't unfurl as matches as media, autolinking will pick it up
			return false;
		}

		return $this->getUnfurlBbCode($link['url']) . $link['suffixText'];
	}

	protected function getUnfurlBbCode($url)
	{
		/** @var \XF\Repository\Unfurl $unfurlRepo */
		$unfurlRepo = $this->app->repository('XF:Unfurl');
		$result = $unfurlRepo->logPendingUnfurl($url);

		return '[URL unfurl="true"]' . $result->url . '[/URL]';
	}

	protected function getUnfurlData($url)
	{
		$metadata = $this->fetchMetadataFromUrl($url);

		$title = $metadata->getTitle();
		if (!strlen($title))
		{
			// if we can't get a title, there's not much point in looking for anything else
			return false;
		}

		return [
			'title' => $title,
			'description' => $metadata->getDescription(),
			'image_url' => $metadata->getImage(),
			'favicon_url' => $metadata->getFavicon()
		];
	}

	protected function getEmbedBbCode($url)
	{
		$match = $this->app->repository('XF:BbCodeMediaSite')->urlMatchesMediaSiteList($url, $this->embedSites);
		if (!$match)
		{
			return null;
		}

		$matchBbCode = '[MEDIA=' . $match['media_site_id'] . ']' . $match['media_id'] . '[/MEDIA]';

		if (!empty($match['site']->oembed_enabled))
		{
			$this->cacheOembedResponse($match['site'], $match['media_id']);
		}

		if ($this->autoEmbedLink)
		{
			$matchBbCode .= "\n" . str_replace('{$url}', "{$url}", $this->autoEmbedLink) . "\n";
		}

		return $matchBbCode;
	}

	protected function cacheOembedResponse($site, $mediaId)
	{
		/** @var \XF\Service\Oembed $oEmbedService */
		$oEmbedService = $this->app->service('XF:Oembed');
		$oEmbedService->getOembed($site->media_site_id, $mediaId);
	}

	protected function incrementMatchedTag(Processor $processor, $output)
	{
		if (preg_match('#^\[(\w+)#i', $output, $match))
		{
			$this->incrementTagUsageCount($processor, strtolower($match[1]));
		}
	}

	protected function incrementTagUsageCount(Processor $processor, $tag)
	{
		$this->adjustTagUsageCount($processor, $tag, 1);
	}

	protected function adjustTagUsageCount(Processor $processor, $tag, $adjust)
	{
		$usage = $processor->getAnalyzer('usage');
		if ($usage && $usage instanceof AnalyzeUsage)
		{
			$usage->adjustTagCount($tag, $adjust);
		}
	}

	public static function factory(\XF\App $app, array $config = [])
	{
		$options = $app->options();

		$autoEmbed = $options->autoEmbedMedia;

		$baseConfig = [
			'autoEmbed' => (bool)$autoEmbed['embedType'], // 0 is false, otherwise true
			'autoEmbedLink' => $autoEmbed['embedType'] == 2 ? $autoEmbed['linkBbCode'] : '',
			'maxEmbed' => ($options->messageMaxMedia ? $options->messageMaxMedia : PHP_INT_MAX),
			'embedSites' => null,
			'urlToPageTitle' => $options->urlToPageTitle['enabled'],
			'urlToTitleFormat' => $options->urlToPageTitle['format'],
			'urlToRichPreview' => $options->urlToRichPreview
		];

		$config = array_replace($baseConfig, $config);
		if ($config['embedSites'] === null)
		{
			$config['embedSites'] = $app->repository('XF:BbCodeMediaSite')->findActiveMediaSites()->fetch();
		}

		return new static($app, $config);
	}
}