<?php

namespace XF\Html\Renderer;

use XF\Html\Parser;
use XF\Html\Tag;
use XF\Html\Text;

class BbCode
{
	protected $_options = [
		'baseUrl' => ''
	];

	const BR_SUBSTITUTE = "\x1A";

	/**
	 * A map of tag handlers. Tag names are in lower case. Possible keys:
	 * 		* wrap - wraps tag content in some text; used %s for text (eg, [b]%s[/b])
	 * 		* filterCallback - callback to process tag; given tag content (string) and tag (Tag)
	 *
	 * @var array Key is tag name in lower case
	 */
	protected $_handlers = [
		'b'          => ['wrap' => '[B]%s[/B]'],
		'strong'     => ['wrap' => '[B]%s[/B]'],

		'i'          => ['wrap' => '[I]%s[/I]'],
		'em'         => ['wrap' => '[I]%s[/I]'],

		'u'          => ['wrap' => '[U]%s[/U]'],
		's'          => ['wrap' => '[S]%s[/S]'],
		'strike'     => ['wrap' => '[S]%s[/S]'],

		'font'       => ['filterCallback' => ['$this', 'handleTagFont']],
		'a'          => ['filterCallback' => ['$this', 'handleTagA']],
		'img'        => ['filterCallback' => ['$this', 'handleTagImg']],
		'video'      => ['filterCallback' => ['$this', 'handleTagVideo']],

		'ul'         => ['filterCallback' => ['$this', 'handleTagUl'], 'skipCss' => true],
		'ol'         => ['filterCallback' => ['$this', 'handleTagOl'], 'skipCss' => true],
		'li'         => ['filterCallback' => ['$this', 'handleTagLi']],

		'blockquote' => ['wrap' => '[QUOTE]%s[/QUOTE]'],

		'h1'         => ['filterCallback' => ['$this', 'handleTagH']],
		'h2'         => ['filterCallback' => ['$this', 'handleTagH']],
		'h3'         => ['filterCallback' => ['$this', 'handleTagH']],
		'h4'         => ['filterCallback' => ['$this', 'handleTagH']],
		'h5'         => ['filterCallback' => ['$this', 'handleTagH']],
		'h6'         => ['filterCallback' => ['$this', 'handleTagH']],

		'table'      => ['filterCallback' => ['$this', 'handleTagTable'], 'skipCss' => true],
		'tr'         => ['wrap' => "[TR]\n%s\n[/TR]"],
		'th'         => ['wrap' => "[TH]%s[/TH]\n"],
		'td'         => ['wrap' => "[TD]%s[/TD]\n"],
	];

	/**
	 * Handlers for specific CSS rules. Value is a callback function name.
	 *
	 * @var array Key is the CSS rule name
	 */
	protected $_cssHandlers = [
		'color'           => ['$this', 'handleCssColor'],
		'float'           => ['$this', 'handleCssFloat'],
		'font-family'     => ['$this', 'handleCssFontFamily'],
		'font-size'       => ['$this', 'handleCssFontSize'],
		'font-style'      => ['$this', 'handleCssFontStyle'],
		'font-weight'     => ['$this', 'handleCssFontWeight'],
		'margin-left'     => ['$this', 'handleCssIndentLeft'], // editor implements LTR indent this way
		'margin-right'    => ['$this', 'handleCssIndentRight'], // editor implements RTL indent this way
		'padding-left'    => ['$this', 'handleCssIndentLeft'], // editor implements LTR indent this way
		'padding-right'   => ['$this', 'handleCssIndentRight'], // editor implements RTL indent this way
		'text-align'      => ['$this', 'handleCssTextAlign'],
		'text-decoration' => ['$this', 'handleCssTextDecoration'],
	];

	public static function renderFromHtml($html, array $options = [])
	{
		//echo '<pre>' . htmlspecialchars($html) . '</pre>'; exit;

		$class = \XF::app()->extendClass(__CLASS__);
		/** @var $renderer BbCode */
		$renderer = new $class($options);

		$html = $renderer->preFilter($html);

		$parser = new Parser($html);
		$parsed = $parser->parse();

		//$parser->printTags($parsed);

		$rendered = $renderer->render($parsed);
		//echo '<pre>' . htmlspecialchars($rendered) . '</pre>'; exit;

		return $rendered;
	}

	/**
	 * Constructor.
	 *
	 * @param array $options
	 */
	public function __construct(array $options = [])
	{
		$this->_options['baseUrl'] = \XF::app()->request()->getFullBasePath();

		$this->_options = array_merge($this->_options, $options);
	}

	public function preFilter($html)
	{
		// IE bug (#25781)
		$html = preg_replace(
			'#(<a[^>]+href="([^"]+)"[^>]*>)\\2(\[/?[a-z0-9_-]+)(</a>)\]#siU',
			'$1$2$4$3]',
			$html
		);

		// issue where URLs have been auto linked inside manually entered BB code options
		$html = preg_replace(
			'#(\[[a-z0-9_-]+=("|\'|))<a[^>]+href="([^"]+)"[^>]*>\\3</a>#siU',
			'$1$3',
			$html
		);

		$html = preg_replace_callback(
			'#(\[(code|php|html|img|plain)\])(.*)(\[/\\2\])#siU',
			[$this, '_stripStylingHtmlMatch'],
			$html
		);

		$html = preg_replace(
			'#<div class="bbCodeBlock bbCodeBlock--unfurl[^"]+".*data-url="(.*)"[^>]*>#miU',
			'[URL unfurl="true"]$1[/URL]',
			$html
		);

		$html = preg_replace_callback('/^<li\s?(?:data-xf-list-type="(ul|ol)")?>.*<\/li>$/is', function(array $match)
		{
			$type = isset($match[1]) ? $match[1] : 'ul';
			return "<$type>$match[0]</$type>";
		}, $html);

		// discard outer span.fr-video tag and apply some properties directly to video
		$html = preg_replace_callback('/<span[^>]*class="(fr-video[^"]*)"[^>]*>(<video.*)<\/span>/U', function(array $match)
		{
			$classes = $match[1];
			$video = $match[2];

			$video = str_replace('class="', 'class="' . $classes . ' ', $video);

			return $video;
		}, $html);

		return $html;
	}

	protected function _stripStylingHtmlMatch(array $match)
	{
		$content = $match[3];
		$tags = 'b|i|u|s|strong|em|strike|a|span|font';

		$content = preg_replace('#<(' . $tags . ')(\s[^>]*)?>#i', '', $content);
		$content = preg_replace('#</(' . $tags . ')>#i', '', $content);

		return $match[1] . $content . $match[4];
	}

	/**
	 * Renders the specified tag and all children.
	 *
	 * @param Tag $tag
	 *
	 * @return string
	 */
	public function render(Tag $tag)
	{
		$output = $this->renderTag($tag);
		return $this->_postRender($output->text());
	}

	protected function _postRender($text)
	{
		$text = \XF::cleanString($text);

		$text = preg_replace('#\[img\]\[url\]([^\[]+)\[/url\]\[/img\]#i', '[IMG]$1[/IMG]', $text);

		do
		{
			$newText = preg_replace('#\[/(b|i|u|s|left|center|right)\]([\r\n]*)\[\\1\]#i', '\\2', $text);
			if ($newText === null || $newText == $text)
			{
				break;
			}
			$text = $newText;
		}
		while (true);

		do
		{
			$newText = preg_replace('#(\[(font|color|size|url|email)=[^\]]*\])((?:(?>[^\[]+?)|\[(?!\\2))*)\[/\\2\]([\r\n]*)\\1#siU', '\\1\\3\\4', $text);
			if ($newText === null || $newText == $text)
			{
				break;
			}
			$text = $newText;
		}
		while (true);

		// redo this as the color/size clean up may have exposed this
		do
		{
			$newText = preg_replace('#\[/(b|i|u|s|left|center|right)\]([\r\n]*)\[\\1\]#i', '\\2', $text);
			if ($newText === null || $newText == $text)
			{
				break;
			}
			$text = $newText;
		}
		while (true);

		return \XF::cleanString($text);
	}

	public function renderTag(Tag $tag, array $state = [])
	{
		if ($tag->tagName() == 'br')
		{
			$output = new BbCode_Element('block', self::BR_SUBSTITUTE);
			$output->incrementTrailingLines();
			return $output;
		}

		$state = array_merge($state, $this->_setTagStates($tag, $state));

		if (!empty($state['hidden']))
		{
			// ignore all under this
			return new BbCode_Element('text', '');
		}

		$isPreFormatted = !empty($state['preFormatted']);

		$children = $this->renderChildren($tag, $state);

		if ($tag->isBlock() && !$isPreFormatted)
		{
			// ignore leading/trailing whitespace-only nodes on blocks
			$firstChild = reset($children);
			if ($firstChild && $firstChild->isWhiteSpace())
			{
				array_shift($children);
			}

			$lastChild = end($children);
			if ($lastChild && $lastChild->isWhiteSpace())
			{
				array_pop($children);
			}
		}

		$children = array_values($children); // need this to be contiguous
		$lastChild = count($children) - 1;
		$outputText = '';

		$output = new BbCode_Element($tag->isBlock() ? 'block' : 'inline');
		$previousTrailing = 0;
		$initialLeading = 0;

		for ($i = 0; $i <= $lastChild; $i++)
		{
			$child = $children[$i]; /* @var $child BbCode_Element */
			$previous = ($i > 0 ? $children[$i - 1] : false); /* @var $previous BbCode_Element */
			$next = ($i < $lastChild ? $children[$i + 1] : false); /* @var $next BbCode_Element */

			if ($child->isBr())
			{
				$previousTrailing++;
				continue;
			}

			if (!$isPreFormatted && $child->isWhiteSpace()
				&& $previous && $previous->isBlock()
				&& $next && $next->isBlock())
			{
				// whitespace node between 2 blocks - skip it
				continue;
			}

			$text = $child->text();
			if (!$isPreFormatted && $previousTrailing && $child->isText())
			{
				// follows a block
				$text = ltrim($text);
			}

			if ($outputText === '')
			{
				// no output so far, so push this up
				$initialLeading += $child->leadingLines();
			}
			else if ($child->leadingLines())
			{
				// this behaves like a block tag in terms of line spacing
				if ($previousTrailing && $child->leadingLines())
				{
					$previousTrailing -= 1; // a new block tag "merges" with the last line the previous
				}
				$previousTrailing += $child->leadingLines();

				if (!$isPreFormatted)
				{
					$outputText = strrev(preg_replace('/^( )+/', '', strrev($outputText)));
				}
			}

			if ($previousTrailing && $text !== '')
			{
				// covers previous trailing and my leading
				$outputText .= str_repeat("\n", $previousTrailing);
				$previousTrailing = 0;
			}

			$outputText .= $text;
			$previousTrailing += $child->trailingLines();
		}

		if ($output->isBlock() && !$isPreFormatted)
		{
			$outputText = trim($outputText);
		}

		if ($outputText !== '' || $tag->isAllowedEmpty())
		{
			// only prepare this tag if we actually have text or it's never going to have text
			$tagName = $tag->tagName();

			$handler = (isset($this->_handlers[$tagName]) ? $this->_handlers[$tagName] : false);

			$preCssOutput = $outputText;
			if ($tagName && (!$handler || empty($handler['skipCss'])))
			{
				$outputText = $this->renderCss($tag, $outputText);
			}

			if ($handler)
			{
				if (!empty($handler['filterCallback']))
				{
					$callback = $handler['filterCallback'];
					if (is_array($callback) && $callback[0] == '$this')
					{
						$callback[0] = $this;
					}
					$outputText = call_user_func($callback, $outputText, $tag, $preCssOutput);
				}
				else if (isset($handler['wrap']))
				{
					$outputText = sprintf($handler['wrap'], $outputText);
				}
			}

			$output->append($outputText);
		}

		if ($output->isBlock() && !$output->isEmpty())
		{
			// add an extra line break before/after if we have something to output
			// note that tags without could've already incremented these
			$output->incrementLeadingLines();
			$output->incrementTrailingLines();

			if ($initialLeading)
			{
				// merge 1 of the initial leading lines with this
				$initialLeading--;
			}

			if ($previousTrailing)
			{
				// merge 1 of the left over trailing lines with this
				$previousTrailing--;
			}
		}

		if ($initialLeading)
		{
			// push initial leading lines up
			$output->incrementLeadingLines($initialLeading);
		}

		if ($previousTrailing)
		{
			$output->incrementTrailingLines($previousTrailing);
		}

		if ($output->leadingLines() || $output->trailingLines())
		{
			//$output->setType('block');
		}

		return $output;
	}

	protected function _setTagStates(Tag $tag, array $existingStates)
	{
		$states = [];

		switch ($tag->tagName())
		{
			case 'pre':
				$states['preFormatted'] = true;
				break;

			case 'script':
			case 'title':
			case 'style':
			case 'embed':
			case 'object':
			case 'iframe':
				$states['hidden'] = true;
				break;
		}

		return $states;
	}

	public function renderChildren(Tag $tag, array $state)
	{
		$output = [];
		foreach ($tag->children() AS $child)
		{
			if ($child instanceof Tag)
			{
				$output[] = $this->renderTag($child, $state);
			}
			else if ($child instanceof Text)
			{
				$output[] = $this->renderText($child, $state);
			}
		}

		return $output;
	}

	public function renderText(Text $text, array $state)
	{
		$text = $text->text();
		if (empty($state['preFormatted']))
		{
			$text = preg_replace('/[\r\n\t ]+/', ' ', $text);
		}

		return new BbCode_Element('text', $text);
	}

	/**
	 * Renders the CSS for a given tag.
	 *
	 * @param Tag $tag
	 * @param string $stringOutput
	 *
	 * @return string BB code output
	 */
	public function renderCss(Tag $tag, $stringOutput)
	{
		$css = $tag->attribute('style');
		if ($css)
		{
			foreach ($css AS $cssRule => $cssValue)
			{
				if (strtolower($cssRule) == 'display' && strtolower($cssValue) == 'none')
				{
					return '';
				}

				if (!empty($this->_cssHandlers[$cssRule]))
				{
					$callback = $this->_cssHandlers[$cssRule];
					if (is_array($callback) && $callback[0] == '$this')
					{
						$callback[0] = $this;
					}
					$stringOutput = call_user_func($callback, $stringOutput, $cssValue, $tag);
				}
			}

			// images aligned on their own are done this way
			$alignRules = array_merge([
				'display' => '',
				'margin-left' => '',
				'margin-right' => ''
			], $css);
			if ($alignRules['display'] == 'block' && (!$tag->isVoid() || $stringOutput !== ''))
			{
				if ($alignRules['margin-left'] == 'auto' && $alignRules['margin-right'] == 'auto')
				{
					$stringOutput = '[CENTER]' . $stringOutput . '[/CENTER]';
				}
				else if ($alignRules['margin-left'] == 'auto' && substr($alignRules['margin-right'], 0, 1) == '0')
				{
					$stringOutput = '[RIGHT]' . $stringOutput . '[/RIGHT]';
				}
				else if (substr($alignRules['margin-left'], 0, 1) == '0' && $alignRules['margin-right'] == 'auto')
				{
					$stringOutput = '[LEFT]' . $stringOutput . '[/LEFT]';
				}
			}
		}

		$align = $tag->attribute('align');
		if ($align && (!$css || empty($css['text-align'])))
		{
			$stringOutput = $this->handleCssTextAlign($stringOutput, $align, $tag);
		}

		return $stringOutput;
	}

	public function convertUrlToAbsolute($url)
	{
		if (preg_match('#^(https?|ftp)://#i', $url))
		{
			return $url;
		}

		if (!$this->_options['baseUrl'])
		{
			return $url;
		}

		if ($url === '')
		{
			return $this->_options['baseUrl'];
		}

		preg_match('#^(?P<protocolHost>(?P<protocol>https?|ftp)://[^/]+)(?P<path>.*)$#i',
			$this->_options['baseUrl'], $baseParts
		);
		if (!$baseParts)
		{
			return $url;
		}

		if (substr($url, 0, 2) == '//')
		{
			return $baseParts['protocol'] . ':' . $url;
		}

		if ($url[0] == '/')
		{
			return $baseParts['protocolHost'] . $url;
		}

		if (preg_match('#^((\.\./)+)#', $url, $upMatch))
		{
			$count = strlen($upMatch[1]) / strlen($upMatch[2]);

			for ($i = 1; $i <= $count; $i++)
			{
				$baseParts['path'] = dirname($baseParts['path']);
			}

			$url = substr($url, strlen($upMatch[0]));
		}

		$baseParts['path'] = str_replace('\\', '/', $baseParts['path']);

		if (substr($baseParts['path'], -1) != '/')
		{
			$baseParts['path'] .= '/';
		}
		if ($url[0] == '/')
		{
			// path has trailing slash
			$url = substr($url, 1);
		}

		return $baseParts['protocolHost'] . $baseParts['path'] . $url;
	}

	public function handleTagFont($text, Tag $tag)
	{
		$color = trim($tag->attribute('color'));
		if ($color)
		{
			$text = "[COLOR={$color}]{$text}[/COLOR]";
		}

		$size = trim($tag->attribute('size'));
		if ($size && preg_match('/^[0-9]+(px)?$/i', $size))
		{
			$text = "[SIZE={$size}]{$text}[/SIZE]";
		}

		$face = trim($tag->attribute('face'));
		if ($face)
		{
			$text = "[FONT={$face}]{$text}[/FONT]";
		}

		return $text;
	}

	/**
	 * Handles A tags. Can generate URL or EMAIL tags in BB code.
	 *
	 * @param string $text Child text of the tag
	 * @param Tag $tag HTML tag triggering call
	 *
	 * @return string
	 */
	public function handleTagA($text, Tag $tag)
	{
		$href = trim($tag->attribute('href'));
		if (!$href)
		{
			return $text;
		}

		if (preg_match('#^(data:|blob:|tel:|sms:|webkit-fake-url:|x-apple-data-detectors:)#i', $href))
		{
			return $text;
		}

		$userId = intval($tag->attribute('data-user-id'));
		if ($userId)
		{
			return "[USER={$userId}]{$text}[/USER]";
		}

		if (preg_match('/^mailto:(.+)$/i', $href, $match))
		{
			$target = $match[1];
			$type = 'EMAIL';
		}
		else
		{
			$target = $this->convertUrlToAbsolute($href);
			$type = 'URL';
		}

		if ($target == $text)
		{
			// look for part of a BB code at the end that may have been swallowed up
			if (preg_match('#\[/?([a-z0-9_-]+)$#i', $text, $match))
			{
				$append = $match[0];
				$text = substr($text, 0, -strlen($match[0]));
			}
			else
			{
				$append = '';
			}

			return "[$type]{$text}[/$type]$append";
		}
		else
		{
			return "[$type='$target']{$text}[/$type]";
		}
	}

	/**
	 * Handles IMG tags.
	 *
	 * @param string $text Child text of the tag (probably none)
	 * @param Tag $tag HTML tag triggering call
	 *
	 * @return string
	 */
	public function handleTagImg($text, Tag $tag)
	{
		if (($tag->hasClass('smilie') || $tag->attribute('data-smilie')) && $tag->attribute('alt'))
		{
			// regular image smilie / emoji image
			$output = trim($tag->attribute('alt'));
		}
		else if ($this->tagIsAttachment($tag, $match))
		{
			$output = sprintf('[ATTACH%1$s%2$s]%3$s[/ATTACH]',
				($match[1] == 'full' ? ' type="full"' : ''),
				$this->getAttachAttributes($tag),
				$match[2]
			);
		}
		else
		{
			$src = $tag->attribute('src');
			$proxyUrl = $tag->attribute('data-url');
			if ($proxyUrl)
			{
				$src = $proxyUrl;
			}

			$output = '';

			if (preg_match('#^(data:|blob:|webkit-fake-url:)#i', $src))
			{
				// data URI - ignore
			}
			else if ($src)
			{
				$smilies = \XF::app()->container('smilies');
				foreach ($smilies AS $smilie)
				{
					if ($src == $smilie['image_url'])
					{
						$output = reset($smilie['smilieText']);
						break;
					}
				}

				if (!$output)
				{
					$output =  '[IMG' . $this->getAttachAttributes($tag) . ']' . $this->convertUrlToAbsolute($src) . '[/IMG]';
				}
			}
		}

		return $this->renderCss($tag, $output);
	}

	/**
	 * Handles VIDEO tags.
	 *
	 * @param string $text Child text of the tag (probably none)
	 * @param Tag $tag HTML tag triggering call
	 *
	 * @return string
	 */
	public function handleTagVideo($text, Tag $tag)
	{
		if ($this->tagIsAttachment($tag, $match))
		{
			$output = sprintf('[ATTACH%1$s%2$s]%3$s[/ATTACH]',
				($match[1] == 'full' ? ' type="full"' : ''),
				$this->getAttachAttributes($tag),
				$match[2]
			);
		}
		else
		{
			// TODO: Handle non-attachment video tags?
			$output = '';
		}

		return $this->renderCss($tag, $output);
	}

	protected function tagIsAttachment(Tag $tag, &$match)
	{
		return $tag->attribute('data-attachment')
			&& preg_match('#^(thumb|full):(\d+)$#', $tag->attribute('data-attachment'), $match);
	}

	public function getAttachAttributes(Tag $tag)
	{
		$attributes = $this->getAttachAlignAttribute($tag)
			. $this->getAttachWidthAttribute($tag);

		if ($tag->tagName() == 'img')
		{
			$attributes .= $this->getAttachAltAttribute($tag);
		}

		return $attributes;
	}

	protected function getAttachAlignAttribute(Tag $tag)
	{
		if ($tag->hasClass('fr-fir') || $tag->hasClass('fr-fvr'))
		{
			return ' align="right"';
		}
		else if ($tag->hasClass('fr-fil') || $tag->hasClass('fr-fvl'))
		{
			return ' align="left"';
		}
		else
		{
			return '';
		}
	}

	protected function getAttachWidthAttribute(Tag $tag)
	{
		if ($style = $tag->attribute('style'))
		{
			if (isset($style['width']))
			{
				if (preg_match('/^(?P<width>[\d\.]+(?:px|%))$/i', $style['width'], $match))
				{
					return " width=\"{$match['width']}\"";
				}
			}
			if (isset($style['height']))
			{
				if (preg_match('/^(?P<height>[\d\.]+(?:px|%))$/i', $style['height'], $match))
				{
					return " height=\"{$match['height']}\"";
				}
			}
		}

		return '';
	}

	protected function getAttachAltAttribute(Tag $tag)
	{
		if ($_alt = $tag->attribute('alt'))
		{
			return ' alt="' . str_replace('"', '', $tag->attribute('alt')) . '"';
		}
		else
		{
			return '';
		}
	}

	protected function handleListTag($listFormatter, $text, Tag $tag)
	{
		if (!strlen($text))
		{
			// no children, just do a linebreak
			return "\n";
		}

		$childList = 0;
		$childOtherTag = 0;
		$childText = 0;

		foreach ($tag->children() AS $child)
		{
			if ($child instanceof Tag)
			{
				if ($child->tagName() == 'ol' || $child->tagName() == 'ul')
				{
					$childList++;
				}
				else
				{
					$childOtherTag++;
				}
			}
			else if ($child instanceof Text)
			{
				if (strlen(trim($child->text())) > 0)
				{
					$childText++;
				}
			}
		}

		if ($childList && !$childOtherTag && !$childText)
		{
			// just a list like <ul><ul><li>...</ul></ul>. This is how Chrome implements indent
			$output = "[INDENT]{$text}[/INDENT]";
		}
		else
		{
			$output = sprintf($listFormatter, $text);
		}

		return $this->renderCss($tag, $output);
	}

	/**
	 * Handles UL tags.
	 *
	 * @param string $text Child text of the tag
	 * @param Tag $tag HTML tag triggering call
	 *
	 * @return string
	 */
	public function handleTagUl($text, Tag $tag)
	{
		return $this->handleListTag("[LIST]\n%s\n[/LIST]", $text, $tag);
	}

	/**
	 * Handles OL tags.
	 *
	 * @param string $text Child text of the tag
	 * @param Tag $tag HTML tag triggering call
	 *
	 * @return string
	 */
	public function handleTagOl($text, Tag $tag)
	{
		return $this->handleListTag("[LIST=1]\n%s\n[/LIST]", $text, $tag);
	}

	/**
	 * Handles LI tags.
	 *
	 * @param string $text Child text of the tag
	 * @param Tag $tag HTML tag triggering call
	 *
	 * @return string
	 */
	public function handleTagLi($text, Tag $tag)
	{
		$parent = $tag->parent();
		if ($parent && !in_array($parent->tagName(), ['ol', 'ul']))
		{
			if (trim($text) === '')
			{
				return '';
			}
			else
			{
				return '[LIST][*]' . $text . '[/LIST]';
			}
		}
		else
		{
			if (substr($text, -1) == self::BR_SUBSTITUTE)
			{
				// has a trailiing br. we need to add an extra line to make it really count
				$text .= "\n";
			}
			return '[*]' . $text;
		}
	}

	/**
	 * Handles table tags. This is mostly done as a callback as we need to apply any CSS changes around this tag.
	 *
	 * @param string $text Child text of the tag
	 * @param Tag $tag HTML tag triggering call
	 *
	 * @return string
	 */
	public function handleTagTable($text, Tag $tag)
	{
		$output = "[TABLE]\n{$text}\n[/TABLE]";
		return $this->renderCss($tag, $output);
	}

	/**
	 * Handles heading tags.
	 *
	 * @param string $text Child text of the tag
	 * @param Tag $tag HTML tag triggering call
	 *
	 * @return string
	 */
	public function handleTagH($text, Tag $tag)
	{
		switch ($tag->tagName())
		{
			case 'h1': $size = 6; break;
			case 'h2': $size = 5; break;
			case 'h3': $size = 4; break;
			case 'h4': $size = 3; break;
			default: $size = false;
		}

		$text = '[B]' . $text . '[/B]';

		if ($size)
		{
			$text = '[SIZE=' . $size . ']' . $text . '[/SIZE]';
		}

		return $text . "\n";
	}

	/**
	 * Handles CSS (text) color rules.
	 *
	 * @param string $text Child text of the tag with the CSS
	 * @param string $alignment Value of the CSS rule
	 *
	 * @return string
	 */
	public function handleCssColor($text, $color)
	{
		return "[COLOR=$color]{$text}[/COLOR]";
	}

	/**
	 * Handles CSS float rules.
	 *
	 * @param string $text Child text of the tag with the CSS
	 * @param string $alignment Value of the CSS rule
	 *
	 * @return string
	 */
	public function handleCssFloat($text, $alignment)
	{
		switch (strtolower($alignment))
		{
			case 'left':
			case 'right':
				$alignmentUpper = strtoupper($alignment);
				return "[$alignmentUpper]{$text}[/$alignmentUpper]";

			default:
				return $text;
		}
	}

	/**
	 * Handles CSS font-family rules. The first font is used.
	 *
	 * @param string $text Child text of the tag with the CSS
	 * @param string $alignment Value of the CSS rule
	 *
	 * @return string
	 */
	public function handleCssFontFamily($text, $cssValue)
	{
		list($fontFamily) = explode(',', $cssValue);
		if (preg_match('/^(\'|")(.*)\\1$/', $fontFamily, $match))
		{
			$fontFamily = $match[2];
		}

		if ($fontFamily && preg_match('/^[a-z0-9 \-]+$/i', $fontFamily))
		{
			return "[FONT=$fontFamily]{$text}[/FONT]";
		}
		else
		{
			return $text;
		}
	}

	/**
	 * Handles CSS font-size rules.
	 *
	 * @param string $text Child text of the tag with the CSS
	 * @param string $alignment Value of the CSS rule
	 *
	 * @return string
	 */
	public function handleCssFontSize($text, $fontSize)
	{
		switch (strtolower($fontSize))
		{
			case 'xx-small':
			case '9px':
				$fontSize = 1; break;

			case 'x-small':
			case '10px':
				$fontSize = 2; break;

			case 'small':
			case '12px':
				$fontSize = 3; break;

			case 'medium':
			case '15px':
			case '100%':
				$fontSize = 4; break;

			case 'large':
			case '18px':
				$fontSize = 5; break;

			case 'x-large':
			case '22px':
				$fontSize = 6; break;

			case 'xx-large':
			case '26px':
				$fontSize = 7; break;

			default:
				if (!preg_match('/^[0-9]+(px)?$/i', $fontSize))
				{
					$fontSize = 0;
				}
		}

		if ($fontSize)
		{
			return "[SIZE=$fontSize]{$text}[/SIZE]";
		}
		else
		{
			return $text;
		}
	}

	/**
	 * Handles CSS font-style rules.
	 *
	 * @param string $text Child text of the tag with the CSS
	 * @param string $alignment Value of the CSS rule
	 *
	 * @return string
	 */
	public function handleCssFontStyle($text, $fontStyle)
	{
		switch (strtolower($fontStyle))
		{
			case 'italic':
			case 'oblique':
				return '[I]' . $text . '[/I]';

			default:
				return $text;
		}
	}

	/**
	 * Handles CSS font-weight rules.
	 *
	 * @param string $text Child text of the tag with the CSS
	 * @param string $alignment Value of the CSS rule
	 *
	 * @return string
	 */
	public function handleCssFontWeight($text, $fontWeight)
	{
		switch (strtolower($fontWeight))
		{
			case 'bold':
			case 'bolder':
			case '700':
			case '800':
			case '900':
				return '[B]' . $text . '[/B]';

			default:
				return $text;
		}
	}

	/**
	 * Handles CSS padding-left/margin-left rules to represent LTR indent.
	 *
	 * @param string $text Child text of the tag with the CSS
	 * @param string $amount Value of the CSS rule
	 *
	 * @return string
	 */
	public function handleCssIndentLeft($text, $amount)
	{
		$language = \XF::language();
		if ($language['text_direction'] == 'RTL')
		{
			return $text;
		}

		if (preg_match('/^(\d+)px$/i', $amount, $match))
		{
			$depth = floor($match[1] / 20); // editor puts in 20px
			if ($depth)
			{
				$open = ($depth > 1 ? '[INDENT=' . $depth . ']' : '[INDENT]');

				return $open . $text . '[/INDENT]';
			}
		}

		return $text;
	}

	/**
	 * Handles CSS padding-right/margin-rigth rules to represent RTL indent.
	 *
	 * @param string $text Child text of the tag with the CSS
	 * @param string $amount Value of the CSS rule
	 *
	 * @return string
	 */
	public function handleCssIndentRight($text, $amount)
	{
		$language = \XF::language();
		if ($language['text_direction'] != 'RTL')
		{
			return $text;
		}

		if (preg_match('/^(\d+)px$/i', $amount, $match))
		{
			$depth = floor($match[1] / 20); // editor puts in 20px
			if ($depth)
			{
				$open = ($depth > 1 ? '[INDENT=' . $depth . ']' : '[INDENT]');

				return $open . $text . '[/INDENT]';
			}
		}

		return $text;
	}

	/**
	 * Handles CSS text-align rules.
	 *
	 * @param string $text Child text of the tag with the CSS
	 * @param string $alignment Value of the CSS rule
	 * @param Tag $tag
	 *
	 * @return string
	 */
	public function handleCssTextAlign($text, $alignment, Tag $tag)
	{
		switch (strtolower($alignment))
		{
			case 'left':
			case 'center':
			case 'right':
				$alignmentUpper = strtoupper($alignment);

				if (!$tag->parent() || !$tag->parent()->tagName())
				{
					$language = \XF::language();
					if (
						($language['text_direction'] == 'RTL' && $alignmentUpper == 'RIGHT')
						|| ($language['text_direction'] != 'RTL' && $alignmentUpper == 'LEFT')
					)
					{
						return $text;
					}
				}

				return "[$alignmentUpper]{$text}[/$alignmentUpper]";

			default:
				return $text;
		}
	}

	/**
	 * Handles CSS text-decoration rules.
	 *
	 * @param string $text Child text of the tag with the CSS
	 * @param string $alignment Value of the CSS rule
	 *
	 * @return string
	 */
	public function handleCssTextDecoration($text, $decoration)
	{
		switch (strtolower($decoration))
		{
			case 'underline':
				return "[U]{$text}[/U]";

			case 'line-through':
				return "[S]{$text}[/S]";

			default:
				return $text;
		}
	}
}

class BbCode_Element
{
	protected $_type = '';
	protected $_text = '';
	protected $_isWhiteSpace = null;

	protected $_modifiers = [];
	protected $_leadingLines = 0;
	protected $_trailingLines = 0;

	public function __construct($type, $text = '')
	{
		$this->setType($type);
		$this->setText($text);
	}

	public function type()
	{
		return $this->_type;
	}

	public function text()
	{
		return $this->_text;
	}

	public function append($text)
	{
		$this->_text .= $text;
		$this->_setIsWhiteSpace();
	}

	public function setText($text)
	{
		$this->_text = $text;
		$this->_setIsWhiteSpace();
	}

	protected function _setIsWhiteSpace()
	{
		$this->_isWhiteSpace = (strlen(trim($this->_text)) == 0);
	}

	public function setType($type)
	{
		$this->_type = $type;
	}

	public function setModifier($key, $value = true)
	{
		$this->_modifiers[$key] = $value;
	}

	public function unsetModifier($key)
	{
		unset($this->_modifiers[$key]);
	}

	public function getModifier($key)
	{
		return (isset($this->_modifiers[$key]) ? $this->_modifiers[$key] : null);
	}

	public function incrementModifier($key, $offset = 1)
	{
		if (!isset($this->_modifiers[$key]))
		{
			$this->_modifiers[$key] = $offset;
		}
		else
		{
			$this->_modifiers[$key] += $offset;
		}
	}

	public function decrementModifier($key, $offset = 1)
	{
		if (isset($this->_modifiers[$key]))
		{
			$this->_modifiers[$key] -= $offset;
			if ($this->_modifiers[$key] <= 0)
			{
				$this->unsetModifier($key);
			}
		}
	}

	public function leadingLines()
	{
		return $this->_leadingLines;
	}

	public function incrementLeadingLines($offset = 1)
	{
		$this->_leadingLines += $offset;
	}

	public function decrementLeadingLines($offset = 1)
	{
		$this->_leadingLines = max(0, $this->_leadingLines - $offset);
	}

	public function trailingLines()
	{
		return $this->_trailingLines;
	}

	public function incrementTrailingLines($offset = 1)
	{
		$this->_trailingLines += $offset;
	}

	public function decrementTrailingLines($offset = 1)
	{
		$this->_trailingLines = max(0, $this->_trailingLines - $offset);
	}

	public function isBlock()
	{
		return ($this->_type == 'block');
	}

	public function isBr()
	{
		return ($this->_type == 'br');
	}

	public function isInline()
	{
		return !$this->isBlock();
	}

	public function isText()
	{
		return ($this->_type == 'text');
	}

	public function isEmpty()
	{
		return (strlen(trim($this->_text)) == 0);
	}

	public function isWhiteSpace()
	{
		return ($this->_isWhiteSpace && $this->isText());
	}
}
