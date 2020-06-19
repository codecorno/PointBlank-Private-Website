<?php

namespace XF\BbCode\ProcessorAction;

use XF\BbCode\Parser;
use XF\BbCode\RuleSet;

class Markdown implements FiltererInterface, ProcessorAwareInterface
{
	protected $tokens;

	/**
	 * @var \XF\BbCode\Processor
	 */
	protected $processor;

	/**
	 * @var array
	 */
	protected $tags;

	protected $enabled = true;

	protected $smilieStrings = [];

	public function setProcessor(\XF\BbCode\Processor $processor)
	{
		$this->processor = $processor;
	}

	public function setSmilieStrings(array $smilies)
	{
		$this->smilieStrings = array_fill_keys($smilies, true);
	}

	public function addFiltererHooks(FiltererHooks $hooks)
	{
		$hooks->addParseHook('filterInput');
	}

	public function setEnabled($enabled)
	{
		$this->enabled = (bool)$enabled;
	}

	public function filterInput($string, Parser $parser, RuleSet $rules, array &$options)
	{
		if (!$this->enabled)
		{
			return $string;
		}

		$this->tags = $rules->getTags();

		return $this->parseMarkdown($string);
	}

	protected function getBbCodePlainTags()
	{
		$tags = ['code', 'icode', 'php', 'html', 'plain', 'media', 'img', 'user', 'attach'];

		if ($this->tags)
		{
			foreach ($this->tags AS $tag => $config)
			{
				if (!empty($config['plain']))
				{
					$this->tags[] = $tag;
				}
			}
		}

		return array_unique($tags);
	}

	protected function addToken($string)
	{
		$tokenId = count($this->tokens);
		$token = "\x1A" . $tokenId . "\x1A";

		$this->tokens[$tokenId] = $string;

		return $token;
	}

	protected function replaceTokens($string, $isTopLevel = false)
	{
		if ($this->tokens)
		{
			$string = preg_replace_callback(
				"#\x1A(\d+)\x1A#",
				function($match)
				{
					$tokenId = $match[1];
					if (isset($this->tokens[$tokenId]))
					{
						return $this->replaceTokens($this->tokens[$tokenId]);
					}
					else
					{
						return '';
					}
				},
				$string
			);

			if ($isTopLevel)
			{
				$string = str_replace("\x1A", '', $string);
			}
		}

		return $string;
	}

	protected function parseMarkdown($string)
	{
		$this->tokens = [];

		// tokenize stuff we don't want parsed for markdown
		$string = $this->stashBbCodeListItems($string);
		$string = $this->stashBbCodePlainItems($string);
		$string = $this->stashBbCodeMarkUp($string);
		$string = $this->stashUrls($string);

		// parse markdown
		$string = $this->parseFencedCode($string);
		$string = $this->parseUnorderedLists($string);
		$string = $this->parseOrderedLists($string);
		$string = $this->parseBlockQuote($string);
		$string = $this->parseInlineCode($string);
		$string = $this->parseLinks($string);
		$string = $this->parseInlineTags($string);

		// restore stuff that was stashed or tokenized
		$string = $this->replaceTokens($string, true);

		return $string;
	}

	/**
	 * Replace BB Code [*] items with tokens so they are not parsed for tokens
	 *
	 * @param $string
	 *
	 * @return string
	 */
	protected function stashBbCodeListItems($string)
	{
		return preg_replace_callback('/\[\*\]/', function($match)
		{
			return $this->addToken($match[0]);
		}, $string);
	}

	/**
	 * Replace BB Code "plain" items with tokens so they are not parsed for markdown
	 *
	 * @param $string
	 *
	 * @return string
	 */
	protected function stashBbCodePlainItems($string)
	{
		$plainTagsRegex = '(' . implode('|', $this->getBbCodePlainTags()) . ')';

		return preg_replace_callback(
			'#\[' . $plainTagsRegex . '(=[^\]]*)?](.*)\[/\\1]#siU',
			function($match)
			{
				return $this->addToken($match[0]);
			},
			$string
		);
	}

	/**
	 * Stashes actual BB code markup so Markdown is not matched within it.
	 *
	 * @param string $string
	 *
	 * @return string
	 */
	protected function stashBbCodeMarkUp($string)
	{
		if ($this->tags)
		{
			$tagRegex = '(' . implode('|', array_keys($this->tags)) . ')';
		}
		else
		{
			$tagRegex = '\w+';
		}

		return preg_replace_callback(
			'#(\[' . $tagRegex . '(?:=[^\]]*)?+\]|\[\w+(?:\s?\w+="[^"]*")+\]|\[/' . $tagRegex . '\])#si',
			function($match)
			{
				return $this->addToken($match[0]);
			},
			$string
		);
	}

	/**
	 * Stashes things that look like URLs so they don't get modified by Markdown parsing.
	 *
	 * @param string $string
	 *
	 * @return string
	 */
	protected function stashUrls($string)
	{
		// note: keep URLs preceded by ]( as this is likely a Markdown link

		return preg_replace_callback(
			'#(?<=[^a-z0-9@/\.-]|^)(?<!\]\()(https?://|www\.)[^\s"<>{}`\[]+#siu',
			function($match)
			{
				$url = $match[0];
				$suffix = '';

				// if we pick up a trailer that looks like inline MD, we need to step back our stashing before it
				$suffixMatchesMd = true;
				while ($suffixMatchesMd)
				{
					$lastChar = substr($url, -1);

					switch ($lastChar)
					{
						case '_':
						case '*':
						case '~':
						case '`':
							$url = substr($url, 0, -1);
							$suffix = $lastChar . $suffix;
							break;

						default:
							$suffixMatchesMd = false;
					}
				}

				return $this->addToken($url) . $suffix;
			},
			$string
		);
	}

	/**
	 * Parse markdown fenced code into [CODE] BB code
	 *
	 * @param $string
	 *
	 * @return string
	 */
	protected function parseFencedCode($string)
	{
		return preg_replace_callback('/(?<=\s|^)(`{3}|~{3})([^\n`]+)?\n(.+)\1(?=\s|$)/siU', function($match)
		{
			if (empty($match[2]))
			{
				return $this->addToken('[CODE]' . $match[3] . '[/CODE]');
			}
			else if (preg_match('/^[a-z0-9]+$/i', $match[2]) && substr($match[3], -1) == "\n")
			{
				$match[3] = rtrim($match[3]);

				if (strtoupper($match[2]) == 'RICH')
				{
					return "[CODE=\"rich\"]{$match[3]}[/CODE]";
				}
				else
				{
					return $this->addToken("[CODE=\"{$match[2]}\"]" . $match[3] . '[/CODE]');
				}
			}
			else
			{
				return $this->addToken('[CODE]' . "{$match[2]}\n{$match[3]}" . '[/CODE]');
			}
		}, $string);
	}

	/**
	 * Parse markdown inline code into [ICODE] BB code
	 *
	 * @param $string
	 *
	 * @return string
	 */
	protected function parseInlineCode($string)
	{
		return preg_replace_callback('/(?<=\W|^)(?<!@)(`{1,3})([^`\n]+)\1(?=\W|$)/U', function($match)
		{
			if ($this->isMatchSmilie($match[0]))
			{
				return $match[0];
			}

			return $this->addToken('[ICODE]' . $match[2] . '[/ICODE]');
		}, $string);
	}

	/**
	 * Parse markdown links and images to [URL] and [IMG]
	 *
	 * @param $string
	 *
	 * @return string
	 */
	protected function parseLinks($string)
	{
		return preg_replace_callback('/(\!)?\[([^\]]*)\]\((http[^\)]+)\)/iU', function($match)
		{
			$url = trim($match[3]);
			$text = str_replace('"', "'", trim($match[2]));

			if (!empty($match[1]))
			{
				// image
				if (strlen($text))
				{
					return $this->addToken("[IMG alt=\"{$text}\"]{$url}[/IMG]");
				}
				else
				{
					return $this->addToken("[IMG]{$url}[/IMG]");
				}
			}
			else
			{
				// link
				if (strlen($text))
				{
					$text = $this->parseInlineTags($text);
					return $this->addToken("[URL=\"{$url}\"]{$text}[/URL]");
				}
				else
				{
					return $this->addToken("[URL]{$url}[/URL]");
				}
			}
		}, $string);
	}

	/**
	 * Parse markdown blockquote bits and pieces into [QUOTE]
	 *
	 * @param $string
	 *
	 * @return string
	 */
	protected function parseBlockQuote($string)
	{
		return preg_replace_callback('/(?<=\n|^)([ \t]*)>([^\r\n]*)(\r?\n\1>[^\r\n]*)*/', function($match)
		{
			return '[QUOTE]' . preg_replace('/(?<=\n|^)[ \t]*>([^\r\n]*)/', '$1', $match[0]) . '[/QUOTE]';
		}, $string);
	}

	/**
	 * Parse inline markdown tags into [B], [I], [S] and [ICODE]
	 *
	 * @param $string
	 *
	 * @return string
	 */
	protected function parseInlineTags($string)
	{
		// __ delimiters require non-word characters on either side
		$string = preg_replace_callback(
			'#(?<=\W|^)(?<!@)(__|_(?!_))(?!\s)(.+)(?<!\s)\\1(?!_)(?=\W|$)#U',
			function($match)
			{
				if ($this->isMatchSmilie($match[0]))
				{
					return $match[0];
				}

				$innerText = $this->parseInlineTags($match[2]);

				if ($match[1] == '__')
				{
					return "[B]{$innerText}[/B]";
				}
				else
				{
					return "[I]{$innerText}[/I]";
				}
			},
			$string
		);

		// note: parsing ~ to strikethrough has been disabled due to false positives
		/*// ~ delimiters require non-word characters on either side
		$string = preg_replace_callback(
			'#(?<=\W|^)(?<!@)(~+?(?!~))(?!\s)(.+)(?<!\s)\\1(?!~)(?=\W|$)#U',
			function ($match) {
				if ($this->isMatchSmilie($match[0]))
				{
					return $match[0];
				}

				$innerText = $this->parseInlineTags($match[2]);

				return "[S]{$innerText}[/S]";
			},
			$string
		);*/

		// * now require non-word characters on either side to avoid false positives with things like word censoring
		$string = preg_replace_callback(
			'#(?<=\W|^)(?<!@)(\*\*|(?<!\[)\*(?!\*|]))(?!\s)(.+)(?<!\s)\\1(?!\*)(?=\W|$)#U',
			function($match)
			{
				if ($this->isMatchSmilie($match[0]))
				{
					return $match[0];
				}

				$innerText = $this->parseInlineTags($match[2]);

				if ($match[1] == '**')
				{
					return "[B]{$innerText}[/B]";
				}
				else
				{
					return "[I]{$innerText}[/I]";
				}
			},
			$string
		);

		return $string;
	}

	/**
	 * Parse unordered markdown lists into [LIST] [*]...
	 * Supports star, dash or plus then space or tab
	 *
	 * @param $string
	 *
	 * @return string
	 */
	protected function parseUnorderedLists($string)
	{
		return preg_replace_callback('#(?<=\n|^)(\*|-|\+)[ \t]+[^\r\n]+(\r?\n\\1[ \t]+[^\r\n]+)*(?:\r?\n|$)#', function($match)
		{
			$listItems = preg_split('#(\n|^)(\*|-|\+)[ \t]+#', $match[0], -1, PREG_SPLIT_NO_EMPTY);
			if (count($listItems) < 2)
			{
				return $match[0];
			}

			$listItems = $this->adjustListItems($listItems, $postListContent);
			return "[LIST]\n[*]" . implode("\n[*]", $listItems) . "\n[/LIST]\n$postListContent";
		}, $string);
	}

	/**
	 * Parse ordered markdown lists into [LIST="1"] [*]...
	 * Supports 1-9 digits followed by . or ) and then space or tab
	 *
	 * @param $string
	 *
	 * @return string
	 */
	protected function parseOrderedLists($string)
	{
		return $string;

		// Ordered list parsing has been disabled as it can trigger false positives that are difficult to recover from.
		// To re-enable it, we likely need to support starting lists at different values. We may also wish to be
		// more strict with our matching, such as requiring the numbers to move up continuously. Otherwise, we still risk
		// unexpected behaviors.

		/*return preg_replace_callback('#(?<=\n|^)((?:\d{1,9})(?:\.|\))[ \t]+([^\r\n]+)(?:\r?\n|$)){2,}#', function($match)
		{
			$listItems = preg_split('#(\n|^)\d+(?:\.|\))[ \t]+#', $match[0], -1, PREG_SPLIT_NO_EMPTY);
			if (count($listItems) < 2)
			{
				return $match[0];
			}

			$listItems = $this->adjustListItems($listItems, $postListContent);
			return "[LIST=\"1\"]\n[*]" . implode("\n[*]", $listItems) . "\n[/LIST]\n$postListContent";
		}, $string);*/
	}

	/**
	 * Adjusts the content of the list items for BB code. Primarily, handles situations where there may be
	 * existing BB code at the end of the last list item which shouldn't be part of the list.
	 *
	 * @param array $listItems
	 * @param string $postListContent Content to move after the list if needed
	 *
	 * @return array
	 */
	protected function adjustListItems(array $listItems, &$postListContent)
	{
		$postListContent = '';

		$listItems = array_map('trim', $listItems);

		$lastItem = end($listItems);
		$lastId = key($listItems);

		if (preg_match_all("#\x1A(\d+)\x1A#", $lastItem, $tokenMatches, PREG_SET_ORDER | PREG_OFFSET_CAPTURE))
		{
			$openTags = [];

			foreach ($tokenMatches AS $tokenMatch)
			{
				$tokenId = $tokenMatch[1][0];
				if (!isset($this->tokens[$tokenId]))
				{
					// this really shouldn't happen
					continue;
				}

				$token = $this->tokens[$tokenId];

				if (preg_match('#^\[(\w+)#i', $token, $openMatch))
				{
					// tag open
					array_unshift($openTags, strtolower($openMatch[1]));
				}
				else if (preg_match('#^\[/(\w+)\]#i', $token, $closeMatch))
				{
					// close match
					if (!$openTags || $openTags[0] != strtolower($closeMatch[1]))
					{
						// invalid close, split here and move outside the list
						$postListContent = strval(substr($lastItem, $tokenMatch[0][1])) . "\n";
						$listItems[$lastId] = strval(substr($lastItem, 0, $tokenMatch[0][1]));
						break;
					}
					else
					{
						// close the last tag as it matches
						array_shift($openTags);
					}
				}
			}
		}

		return $listItems;
	}

	protected function isMatchSmilie($match)
	{
		return isset($this->smilieStrings[$match]);
	}

	public static function factory(\XF\App $app)
	{
		$md = new static();

		if (!$app->options()->convertMarkdownToBbCode)
		{
			$md->setEnabled(false);
		}

		$md->setSmilieStrings($app->stringFormatter()->getSmilieStrings());

		return $md;
	}
}