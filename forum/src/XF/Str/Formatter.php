<?php

namespace XF\Str;

use XF\Template\Templater;

class Formatter
{
	protected $censorRules = [];
	protected $censorChar = '*';
	protected $censorCache = null;

	protected $smilieTranslate = [];
	protected $smilieReverse = [];

	/**
	 * @var callable|null
	 */
	protected $smilieHtmlPather = null;

	/**
	 * @var callable|null
	 */
	protected $proxyHandler;

	protected $htmlPlaceholderId = 0;

	/**
	 * @var EmojiFormatter|null
	 */
	protected $emojiFormatter;

	public function censorText($string, $censorChar = null)
	{
		if ($censorChar !== null)
		{
			$map = $this->buildCensorMap($this->censorRules, $censorChar);
		}
		else
		{
			if ($this->censorCache === null)
			{
				$this->censorCache = $this->buildCensorMap($this->censorRules, $this->censorChar);
			}
			$map = $this->censorCache;
		}

		if ($map)
		{
			$string = preg_replace(
				array_keys($map),
				$map,
				$string
			);
		}

		return $string;
	}

	public function setCensorRules(array $censorRules, $censorChar)
	{
		$this->censorRules = $censorRules;
		$this->censorChar = $censorChar;
	}

	protected function buildCensorMap(array $censor, $censorCharacter)
	{
		$map = [];

		foreach ($censor AS $key => $word)
		{
			if (is_string($key) || !isset($word['regex']) || !isset($word['replace']))
			{
				// old format or broken
				continue;
			}

			$regex = $word['regex'];
			$replace = $word['replace'];

			$map[$regex] = is_int($replace) ? str_repeat($censorCharacter, $replace) : $replace;
		}

		return $map;
	}

	public function replacePhrasePlaceholders($string, \XF\Language $language = null)
	{
		if (!preg_match_all(
			'#\{phrase:(\w+)\}#iU', $string, $phraseMatches, PREG_SET_ORDER
		))
		{
			return $string;
		}

		if (!$language)
		{
			$language = \XF::language();
		}

		$replacements = [];
		foreach ($phraseMatches AS $phraseMatch)
		{
			$replacements[$phraseMatch[0]] = $language->phrase($phraseMatch[1]);
		}

		return strtr($string, $replacements);
	}

	public function replacePhraseSyntax($value, \XF\Language $language = null)
	{
		if (!preg_match_all(
			'#\{\{\s*phrase\(("|\')([a-z0-9_.]+)\\1(,\s*\{([^}]+)\})?\s*\)\s*\}\}#iU',
			$value, $phraseMatches, PREG_SET_ORDER
		))
		{
			return $value;
		}

		if (!$language)
		{
			$language = \XF::language();
		}

		$replacements = [];
		foreach ($phraseMatches AS $phraseMatch)
		{
			$phraseParams = [];
			if (!empty($phraseMatch[4]))
			{
				preg_match_all('#("|\')(\w+)\\1\s*:\s*("|\')(.*)\\3#siU',
					$phraseMatch[4], $paramMatches, PREG_SET_ORDER
				);
				foreach ($paramMatches AS $paramMatch)
				{
					$phraseParams[$paramMatch[2]] = $paramMatch[4];
				}
			}

			$replacements[$phraseMatch[0]] = $language->phrase($phraseMatch[2], $phraseParams);
		}

		if (count($replacements) == 1 && key($replacements) == $value)
		{
			return current($replacements);
		}

		return $replacements ? strtr($value, $replacements) : $value;
	}

	public function addSmilies(array $smilies)
	{
		foreach ($smilies AS $smilie)
		{
			foreach ($smilie['smilieText'] AS $text)
			{
				$this->smilieTranslate[$text] = "\0" . $smilie['smilie_id'] . "\0";
			}

			$this->smilieReverse[$smilie['smilie_id']] = $smilie;
		}
	}

	public function getSmilieStrings()
	{
		return array_keys($this->smilieTranslate);
	}

	public function setSmilieHtmlPather(callable $pather = null)
	{
		$this->smilieHtmlPather = $pather;
	}

	public function replaceSmiliesInText($text, $replaceCallback, $escapeCallback = null)
	{
		if ($this->smilieTranslate)
		{
			$text = strtr($text, $this->smilieTranslate);
		}

		if ($escapeCallback)
		{
			/** @var callable $escapeCallback */
			$text = $escapeCallback($text);
		}

		if ($this->smilieTranslate)
		{
			$reverse = $this->smilieReverse;
			$text = preg_replace_callback('#\0(\d+)\0#', function($match) use ($reverse, $replaceCallback)
			{
				$id = $match[1];
				return isset($reverse[$id]) ? $replaceCallback($id, $reverse[$id]) : '';
			}, $text);
		}

		return $text;
	}

	protected $smilieCache = [];

	public function replaceSmiliesHtml($text)
	{
		$cache = &$this->smilieCache;

		$replace = function($id, $smilie) use (&$cache)
		{
			if (isset($cache[$id]))
			{
				return $cache[$id];
			}

			$html = $this->getDefaultSmilieHtml($id, $smilie);
			$cache[$id] = $html;
			return $html;
		};

		return $this->replaceSmiliesInText($text, $replace, 'htmlspecialchars');
	}

	public function getDefaultSmilieHtml($id, array $smilie)
	{
		$smilieTitle = htmlspecialchars($smilie['title']);
		$smilieText = htmlspecialchars(reset($smilie['smilieText']));
		$pather = $this->smilieHtmlPather;

		if (empty($smilie['sprite_params']))
		{
			$url = htmlspecialchars($pather ? $pather($smilie['image_url'], 'base') : $smilie['image_url']);
			$srcSet = '';
			if (!empty($smilie['image_url_2x']))
			{
				$url2x = htmlspecialchars($pather ? $pather($smilie['image_url_2x'], 'base') : $smilie['image_url_2x']);
				$srcSet = 'srcset="' . $url . ' 1x, ' . $url2x . ' 2x"';
			}

			return '<img src="' . $url . '" ' . $srcSet . ' class="smilie" alt="' . $smilieText
				. '" title="' . $smilieTitle . '    ' . $smilieText . '" '
				. 'data-shortname="' . $smilieText . '" />';
		}
		else
		{
			// embed a data URI to avoid a request that doesn't respect paths fully
			return '<img src="' . Templater::TRANSPARENT_IMG_URI . '" class="smilie smilie--sprite smilie--sprite' . $id . '" alt="' . $smilieText
				. '" title="' . $smilieTitle . '    ' . $smilieText . '" '
				. 'data-shortname="' . $smilieText . '" />';
		}
	}

	public function convertStructuredTextToHtml($string, $nl2br = true)
	{
		$string = $this->censorText($string);
		$string = \XF::escapeString($string);
		$string = $this->getEmojiFormatter()->formatEmojiToImage($string);
		$string = $this->autoLinkStructuredText($string);
		$string = $this->linkStructuredTextMentions($string);

		if ($nl2br)
		{
			$string = nl2br($string);
		}

		return $string;
	}

	public function moveHtmlToPlaceholders($string, &$restorerClosure)
	{
		$placeholders = [];

		$string = preg_replace_callback(
			'#<[^>]*>#si',
			function (array $match) use (&$placeholders, &$placeholderPosition)
			{
				$placeholder = "\x1A" . $this->htmlPlaceholderId . "\x1A";
				$placeholders[$placeholder] = $match[0];

				$this->htmlPlaceholderId++;

				return $placeholder;
			},
			$string
		);

		$restorerClosure = function($string) use ($placeholders)
		{
			return strtr($string, $placeholders);
		};

		return $string;
	}

	public function removeHtmlPlaceholders($string)
	{
		return preg_replace("#\x1A\\d+\x1A#", '', $string);
	}

	public function moveHtmlEntitiesToPlaceholders($string, &$restorerClosure)
	{
		$placeholders = [];

		$string = preg_replace_callback(
			'/&(?:[a-z\d]+|#\d+|#x[a-f\d]+);/i',
			function ($match) use (&$placeholders, &$placeholderPosition)
			{
				$placeholder = "\x1A" . $this->htmlPlaceholderId . "\x1A";

				$placeholders[$placeholder] = $match[0];

				$this->htmlPlaceholderId++;

				return $placeholder;
			},
			$string
		);

		$restorerClosure = function($string) use ($placeholders)
		{
			$string = strtr($string, $placeholders);
			return preg_replace("/(\x1A)<[^>]*>(\d+)<[^>]*>(\x1A)/", "$1$2$3", $string);
		};

		return $string;
	}

	public function autoLinkStructuredText($string)
	{
		$string = $this->moveHtmlToPlaceholders($string, $restorePlaceholders);

		$string = preg_replace_callback(
			'#(?<=[^a-z0-9@-]|^)(https?://|www\.)[^\s"<>{}`\x1A]+#i',
			function (array $match)
			{
				$url = $this->removeHtmlPlaceholders($match[0]);
				$url = htmlspecialchars_decode($url, ENT_QUOTES);
				$link = $this->prepareAutoLinkedUrl($url);

				if (!$link['url'])
				{
					return htmlspecialchars($url, ENT_QUOTES, 'utf-8');
				}

				$linkInfo = $this->getLinkClassTarget($link['url']);
				$classAttr = $linkInfo['class'] ? " class=\"$linkInfo[class]\"" : '';
				$targetAttr = $linkInfo['target'] ? " target=\"$linkInfo[target]\"" : '';
				$noFollowAttr = $linkInfo['trusted'] ? '' : ' rel="nofollow"';

				return '<a href="' . htmlspecialchars($link['url'], ENT_QUOTES, 'utf-8')
					. "\"{$classAttr}{$noFollowAttr}{$targetAttr}>"
					. htmlspecialchars($link['linkText'], ENT_QUOTES, 'utf-8') . '</a>'
					. htmlspecialchars($link['suffixText'], ENT_QUOTES, 'utf-8');
			},
			$string
		);

		$string = $restorePlaceholders($string);

		return $string;
	}

	public function convertStructuredTextLinkToBbCode($string)
	{
		$string = preg_replace_callback(
			'#(?<=[^a-z0-9@/\.-]|^)(?<!\]\(|url=(?:"|\')|url\]|url\sunfurl=(?:"|\')true(?:"|\')\]|img\])(https?://|www\.)[^\s"<>{}`]+#iu',
			function (array $match)
			{
				$link = $this->prepareAutoLinkedUrl($match[0]);

				return '[URL]' . $link['url'] . '[/URL]' . $link['suffixText'];
			},
			$string
		);

		return $string;
	}

	public function getLinkClassTarget($url)
	{
		$target = '_blank';
		$class = 'link link--external';
		$type = 'external';
		$schemeMatch = true;

		$urlInfo = @parse_url($url);
		if ($urlInfo)
		{
			if (empty($urlInfo['host']))
			{
				$isInternal = true;
			}
			else
			{
				$request = \XF::app()->request();
				$host = $urlInfo['host'] . (!empty($urlInfo['port']) ? ":$urlInfo[port]" : '');
				$isInternal = ($host == $request->getHost());

				$scheme = (!empty($urlInfo['scheme']) ? strtolower($urlInfo['scheme']) : 'http');
				$schemeMatch = $scheme == ($request->isSecure() ? 'https' : 'http');
			}

			if ($isInternal)
			{
				$target = '';
				$class = 'link link--internal';
				$type = 'internal';
			}
		}

		return [
			'class' => $class,
			'target' => $target,
			'type' => $type,
			'trusted' => $type == 'internal',
			'local' => $type == 'internal' && $schemeMatch
		];
	}

	public function prepareAutoLinkedUrl($url)
	{
		$suffixText = '';

		if (preg_match('/&(?:quot|gt|lt);/i', $url, $match, PREG_OFFSET_CAPTURE))
		{
			$suffixText = substr($url, $match[0][1]);
			$url = substr($url, 0, $match[0][1]);
		}

		$linkText = $url;

		if (strpos($url, '://') === false)
		{
			$url = 'http://' . $url;
		}

		do
		{
			$matchedTrailer = false;
			$lastChar = substr($url, -1);
			switch ($lastChar)
			{
				case ')':
				case ']':
					$closer = $lastChar;
					$opener = $lastChar == ']' ? '[' : '(';

					if (substr_count($url, $closer) == substr_count($url, $opener))
					{
						break;
					}
					// break missing intentionally

				case '(':
				case '[':
				case '.':
				case ',':
				case '!':
				case ':':
				case "'":
					$suffixText = $lastChar . $suffixText;
					$url = substr($url, 0, -1);
					$linkText = substr($linkText, 0, -1);

					$matchedTrailer = true;
					break;
			}
		}
		while ($matchedTrailer);

		if (preg_match('/proxy\.php\?\w+=(http[^&]+)&/i', $url, $match))
		{
			// proxy link of some sort, adjust to the original one
			$proxiedUrl = urldecode($match[1]);
			if (preg_match('/./su', $proxiedUrl))
			{
				if ($proxiedUrl == $linkText)
				{
					$linkText = $proxiedUrl;
				}
				$url = $proxiedUrl;
			}
		}

		if (!\XF::app()->validator('Url')->isValid($url))
		{
			$url = null;
		}

		return [
			'url' => $url,
			'linkText' => $linkText,
			'suffixText' => $suffixText
		];
	}

	public function linkStructuredTextMentions($string)
	{
		$string = $this->moveHtmlToPlaceholders($string, $restorePlaceholders);

		$string = preg_replace_callback(
			'#(?<=^|\s|[\](,/\'"]|--|@)@\[(\d+):(\'|"|&quot;|)(.*)\\2\]#iU',
			function(array $match)
			{
				$userId = intval($match[1]);
				$username = $this->removeHtmlPlaceholders($match[3]);
				$username = htmlspecialchars($username, ENT_QUOTES, 'utf-8', false);

				$link = \XF::app()->router('public')->buildLink('full:members', ['user_id' => $userId]);

				return sprintf('<a href="%s" class="username" data-user-id="%d" data-username="%s" data-xf-init="member-tooltip">%s</a>',
					htmlspecialchars($link), $userId, $username, $username
				);
			},
			$string
		);

		$string = $restorePlaceholders($string);

		return $string;
	}

	public function convertStructuredTextMentionsToBbCode($string)
	{
		$string = preg_replace_callback(
			'#(?<=^|\s|[\](,/\'"]|--|@)@\[(\d+):(\'|"|&quot;|)(.*)\\2\]#iU',
			function(array $match)
			{
				$userId = intval($match[1]);
				$username = htmlspecialchars($match[3], ENT_QUOTES, 'utf-8', false);

				return '[USER=' . $userId . ']' . $username . '[/USER]';
			},
			$string
		);

		return $string;
	}

	public function getProxiedUrlIfActive($type, $url)
	{
		return $this->getProxiedUrlIfActiveExtended($type, $url);
	}

	public function getProxiedUrlIfActiveExtended($type, $url, array $options = [])
	{
		if (!$this->proxyHandler)
		{
			return null;
		}

		$handler = $this->proxyHandler;
		return $handler($type, $url, $options);
	}

	public function setProxyHandler(callable $handler = null)
	{
		$this->proxyHandler = $handler;
	}

	public function getProxyHandler()
	{
		return $this->proxyHandler;
	}

	public function splitLongWords($string, $breakLength, $inserter = null)
	{
		$breakLength = intval($breakLength);
		if ($breakLength < 1 || $breakLength > strlen($string)) // strlen isn't completely accurate, but this is an optimization
		{
			return $string;
		}

		if ($inserter === null)
		{
			$inserter = chr(0xE2) . chr(0x80) . chr(0x8B); // UTF-8 for zero width space
		}

		return preg_replace('#[^\s]{' . $breakLength . '}(?=[^\s])#u', '$0' . $inserter, $string);
	}

	public function wholeWordTrim($string, $maxLength, $offset = 0, $ellipsis = '...')
	{
		$ellipsisLen = strlen($ellipsis);

		if ($offset)
		{
			$string = preg_replace('/^\S*\s+/s', '', utf8_substr($string, $offset));
			if ($maxLength > 0)
			{
				$maxLength = max(1, $maxLength - $ellipsisLen);
			}
		}

		$strLength = utf8_strlen($string);
		if ($maxLength > 0 && $strLength > $maxLength)
		{
			$maxLength -= $ellipsisLen;

			if ($maxLength > 0)
			{
				$string = utf8_substr($string, 0, $maxLength);
				$string = strrev(preg_replace('/^\S*\s+/s', '', strrev($string)));
				$string = rtrim($string, ',.!?:;') . $ellipsis;
			}
			else if ($maxLength <= 0)
			{
				// too short with the ellipsis, can't really display anything
				$string = $ellipsis;
				$offset = 0;
			}
		}

		if ($offset)
		{
			$string = $ellipsis . $string;
		}

		return $string;
	}

	public function wholeWordTrimAroundTerm($string, $maxLength, $term, $ellipsis = '...')
	{
		$stringLength = utf8_strlen($string);

		if ($stringLength > $maxLength)
		{
			$term = strval($term);

			if ($term !== '')
			{
				// TODO: slightly more intelligent search term matching, breaking up multiple words etc.
				$termPosition = utf8_strpos(utf8_strtolower($string), utf8_strtolower($term));
			}
			else
			{
				$termPosition = false;
			}

			if ($termPosition !== false)
			{
				$startPos = $termPosition + utf8_strlen($term); // add term length to term start position
				$startPos -= $maxLength / 2; // count back half the max characters
				$startPos = max(0, $startPos); // don't overflow the beginning
				$startPos = min($startPos, $stringLength - $maxLength); // don't overflow the end
			}
			else
			{
				$startPos = 0;
			}

			$string = $this->wholeWordTrim($string, $maxLength, $startPos, $ellipsis);
		}

		return $string;
	}

	public function highlightTermForHtml($string, $term, $class = 'textHighlight')
	{
		$string = $this->moveHtmlEntitiesToPlaceholders(htmlentities($string, ENT_QUOTES, 'utf-8'), $restorePlaceholders);

		$term = trim(preg_replace('#((^|\s)[+|-]|[/()"~^])#', ' ', strval($term)));
		if ($term !== '')
		{
			$string = preg_replace(
				'/(' . preg_replace('#\s+#', '|', preg_quote(htmlspecialchars($term), '/')) . ')/siu',
				'<em class="' . htmlspecialchars($class) . '">\1</em>',
				\XF::escapeString($string)
			);
		}
		else
		{
			$string = \XF::escapeString($string);
		}

		$string = $restorePlaceholders($string);

		return $string;
	}

	public function stripBbCode($string, array $options = [])
	{
		$options = array_merge([
			'stripQuote' => false,
			'hideUnviewable' => true
		], $options);

		if ($options['stripQuote'])
		{
			$parts = preg_split('#(\[quote[^\]]*\]|\[/quote\])#i', $string, -1, PREG_SPLIT_DELIM_CAPTURE);
			$string = '';
			$quoteLevel = 0;
			foreach ($parts AS $i => $part)
			{
				if ($i % 2 == 0)
				{
					// always text, only include if not inside quotes
					if ($quoteLevel == 0)
					{
						$string .= rtrim($part) . "\n";
					}
				}
				else
				{
					// quote start/end
					if ($part[1] == '/')
					{
						// close tag, down a level if open
						if ($quoteLevel)
						{
							$quoteLevel--;
						}
					}
					else
					{
						// up a level
						$quoteLevel++;
					}
				}
			}
		}

		// replaces unviewable tags with a text representation
		$string = str_replace('[*]', '', $string);
		$string = preg_replace(
			'#\[(attach|media|img|spoiler|ispoiler)[^\]]*\].*\[/\\1\]#siU',
			$options['hideUnviewable'] ? '' : '[\\1]',
			$string
		);

		// split the string into possible delimiters and text; even keys (from 0) are strings, odd are delimiters
		$parts = preg_split('#(\[\w+(?:=[^\]]*)?+\]|\[\w+(?:\s?\w+="[^"]*")+\]|\[/\w+\])#si', $string, -1, PREG_SPLIT_DELIM_CAPTURE);
		$total = count($parts);
		if ($total < 2)
		{
			return trim($string);
		}

		$closes = [];
		$skips = [];
		$newString = '';

		// first pass: find all the closing tags and note their keys
		for ($i = 1; $i < $total; $i += 2)
		{
			if (preg_match("#^\\[/(\w+)]#i", $parts[$i], $match))
			{
				$closes[strtolower($match[1])][$i] = $i;
			}
		}

		// second pass: look for all the text elements and any opens, then find
		// the first corresponding close that comes after it and remove it.
		// if we find that, don't display the open or that close
		for ($i = 0; $i < $total; $i++)
		{
			$part = $parts[$i];
			if ($i % 2 == 0)
			{
				// string part
				$newString .= $part;
				continue;
			}

			if (!empty($skips[$i]))
			{
				// known close
				continue;
			}

			if (preg_match('/^\[(\w+)(?:=|\s?\w+="[^"]*"|\])/i', $part, $match))
			{
				$tagName = strtolower($match[1]);
				if (!empty($closes[$tagName]))
				{
					do
					{
						$closeKey = reset($closes[$tagName]);
						if ($closeKey)
						{
							unset($closes[$tagName][$closeKey]);
						}
					}
					while ($closeKey && $closeKey < $i);
					if ($closeKey)
					{
						// found a matching close after this tag
						$skips[$closeKey] = true;
						continue;
					}
				}
			}

			$newString .= $part;
		}

		return trim($newString);
	}

	public function getBbCodeForQuote($bbCode, $context)
	{
		$bbCodeContainer = \XF::app()->bbCode();

		$processor = $bbCodeContainer->processor()
			->addProcessorAction('quotes', $bbCodeContainer->processorAction('quotes'))
			->addProcessorAction('censor', $bbCodeContainer->processorAction('censor'));

		return trim($processor->render($bbCode, $bbCodeContainer->parser(), $bbCodeContainer->rules($context)));
	}

	public function getBbCodeFromSelectionHtml($html)
	{
		// attempt to parse the selected HTML into BB code
		$html = trim(strip_tags($html, '<b><i><u><a><img><span><ul><ol><li><pre><code><br>'));

		// handle CODE output and turn it back into BB code
		$html = preg_replace_callback('/<code data-language="(\w+)">(.*)<\/code>/siU', function(array $matches)
		{
			return "[CODE=$matches[1]]" . str_replace("\n", '<br>', trim($matches[2])) . "[/CODE]";
		}, $html);

		// handle ICODE output to BB code
		$html = preg_replace_callback('/<code class="bbCodeInline">(.*)<\/code>/siU', function(array $matches)
		{
			return "[ICODE]" . trim($matches[1]) . "[/ICODE]";
		}, $html);

		return $html;
	}

	public function snippetString($string, $maxLength = 0, array $options = [])
	{
		$options = array_merge([
			'term' => '',
			'fromStart' => false,
			'stripBbCode' => false,
			'stripQuote' => false,
			'hideUnviewable' => true,
			'stripHtml' => false,
			'stripPlainTag' => false,
			'censor' => true
		], $options);

		if ($options['stripQuote'])
		{
			$options['stripBbCode'] = true;
		}

		if ($options['stripHtml'])
		{
			$string = strip_tags($string);
		}
		if ($options['stripBbCode'])
		{
			$string = $this->stripBbCode($string, ['stripQuote' => $options['stripQuote'], 'hideUnviewable' => $options['hideUnviewable']]);
		}
		if ($options['stripPlainTag'])
		{
			$string = preg_replace(
				'#(?<=^|\s[\](,/\'"]|--|@)@\[(\d+):(\'|"|&quot;|)(.*)\\2\]#iU',
				'\\3',
				$string
			);
		}

		if ($maxLength)
		{
			if ($options['fromStart'] || !$options['term'])
			{
				$string = $this->wholeWordTrim($string, $maxLength);
			}
			else
			{
				$string = $this->wholeWordTrimAroundTerm($string, $maxLength, $options['term']);
			}
		}

		$string = trim($string);

		if ($options['censor'])
		{
			$string = $this->censorText($string);
		}

		return $string;
	}

	public function createKeyValueSetFromString($string)
	{
		$values = [];

		preg_match_all('/
			^\s*
			(?P<name>([^=\r\n])*?)
			\s*=\s*
			(?P<value>.*?)
			\s*$
		/mix', trim($string), $matches, PREG_SET_ORDER);

		foreach ($matches AS $match)
		{
			$value = $this->replacePhraseSyntax($match['value']);
			$values[$match['name']] = $value;
		}

		return $values;
	}

	public function camelCase($string, $glue = '_')
	{
		return \XF\Util\Php::camelCase($string, $glue);
	}

	public function fromCamelCase($string, $glue = '_')
	{
		return \XF\Util\Php::fromCamelCase($string, $glue);
	}

	/**
	 * @return MentionFormatter
	 *
	 * @throws \Exception
	 */
	public function getMentionFormatter()
	{
		$class = \XF::extendClass('XF\Str\MentionFormatter');
		return new $class();
	}

	/**
	 * @param null $forceStyle
	 * @param bool $forceCdn
	 *
	 * @return \XF\Str\EmojiFormatter
	 * @throws \Exception
	 */
	public function getEmojiFormatter($forceStyle = null, $forceCdn = false)
	{
		$canCache = ($forceStyle === null && $forceCdn === false);

		if (!$this->emojiFormatter || !$canCache)
		{
			$options = \XF::options();
			$pather = \XF::app()->container('request.pather');

			$config = [
				'style' => $forceStyle ?: $options->emojiStyle,
				'source' => $forceCdn ? 'cdn' : $options->emojiSource['source'],
				'path' => $pather(trim($options->emojiSource['path'], '/') . '/', 'base'),
				'uc_filename' => null
			];

			$class = \XF::extendClass('XF\Str\EmojiFormatter');
			$formatter = new $class($config);

			if (!$canCache)
			{
				return $formatter;
			}

			$this->emojiFormatter = $formatter;
		}

		return $this->emojiFormatter;
	}
}