<?php

namespace XF\Str;

use Emojione\Client;
use Emojione\Ruleset;

class EmojiFormatter
{
	const UC_OUTPUT = 0;
	const UC_MATCH = 1;
	const UC_BASE = 2;

	/**
	 * @var Client
	 */
	protected $emojiOne;

	protected $config = [];

	public function __construct(array $config)
	{
		$this->emojiOne = new Client($this->getRuleset());
		$this->config = $this->setTypeSpecificDefaults($config);
	}

	public function formatEmojiToImage($string)
	{
		$client = $this->emojiOne;

		$string = preg_replace_callback('/' . $client->ignoredRegexp . '|' . $client->unicodeRegexp . '/u', function($match) use($client)
		{
			if (!is_array($match) || !isset($match[0]) || empty($match[0]))
			{
				return $match[0];
			}

			$ruleset = $this->getRuleset();
			$unicodeReplace = $ruleset->getUnicodeReplace() + $ruleset->getUnicodeReplaceGreedy();

			$unicode = strtoupper($match[0]);

			if (array_key_exists($unicode, $unicodeReplace))
			{
				$shortname = $unicodeReplace[$unicode];
			}
			else
			{
				return $match[0];
			}

			return $this->getImageFromShortname($shortname);
		},  $string);

		return $string;
	}

	public function formatShortnameToImage($string)
	{
		$client = $this->emojiOne;

		$string = preg_replace_callback('/' . $client->ignoredRegexp . '|(' . $client->shortcodeRegexp . ')/Si', function($match)
		{
			if (!is_array($match) || !isset($match[1]) || empty($match[1]))
			{
				return $match[0];
			}
			else
			{
				$ruleset = $this->getRuleset();
				$shortcodeReplace = $ruleset->getShortcodeReplace();

				$shortname = $match[1];

				if (!isset($shortcodeReplace[$shortname]))
				{
					return $match[0];
				}

				return $this->getImageFromShortname($shortname);
			}
		}, $string);

		return $string;
	}

	public function getImageFromShortname($shortname, $lazyLoad = false)
	{
		$config = $this->config;
		$alt = $this->formatShortnameToEmoji($shortname);

		if ($config['style'] == 'native')
		{
			return $alt;
		}

		$ruleset = $this->getRuleset();
		$shortcodeReplace = $ruleset->getShortcodeReplace();

		if (!isset($shortcodeReplace[$shortname]))
		{
			return $alt;
		}

		$filename = $shortcodeReplace[$shortname][$config['uc_filename']];
		$filename = $config['filename_formatter']($filename);

		$title = $this->getEmojiNameFromShortname($shortname) . '    ' . $shortname;

		if ($lazyLoad)
		{
			return '<span class="smilie smilie--emoji smilie--lazyLoad"'
				. ' data-alt="' . htmlspecialchars($alt) . '" title="' . htmlspecialchars($title) . '"'
				. ' data-src="' . htmlspecialchars($config['path'] . $filename) . '.png"'
				. ' data-shortname="' . $shortname . '"></span>';
		}
		else
		{
			return '<img class="smilie smilie--emoji"'
			. ' alt="' . htmlspecialchars($alt) . '" title="' . htmlspecialchars($title) . '"'
			. ' src="' . htmlspecialchars($config['path'] . $filename) . '.png"'
			. ' data-shortname="' . htmlspecialchars($shortname) . '"'
			. ' />';
		}
	}

	public function getEmojiNameFromShortname($shortname)
	{
		return \XF::phrase('emoji.' . str_replace('-', '_', str_replace(':', '', strtolower($shortname))));
	}

	public function formatShortnameToEmojiExceptions($string, array $exceptions = [], $native = true)
	{
		$client = $this->emojiOne;

		$exceptionsKeyed = array_fill_keys(array_map('strtolower', $exceptions), true);

		$string = preg_replace_callback(
			'/' . $client->ignoredRegexp . '|(\B:([-+\w]+):\B)/Si',
			function($match) use($client, $native, $exceptionsKeyed)
			{
				if (!is_array($match) || !isset($match[1]) || empty($match[1]))
				{
					return $match[0];
				}
				else
				{
					$ruleset = $this->getRuleset();
					$shortcodeReplace = $ruleset->getShortcodeReplace();

					$shortname = strtolower($match[1]);

					if (isset($exceptionsKeyed[$shortname]))
					{
						return $match[0];
					}

					if (!array_key_exists($shortname, $shortcodeReplace))
					{
						return $match[0];
					}

					$unicode = $shortcodeReplace[$shortname][0];
					$unicode = $client->convert($unicode);

					if ($native)
					{
						return html_entity_decode($unicode);
					}
					else
					{
						return $unicode;
					}
				}
			},
			$string
		);

		return $string;
	}

	public function formatShortnameToEmoji($string, $native = true)
	{
		return $this->formatShortnameToEmojiExceptions($string, [], $native);
	}

	public function formatEmojiToShortname($string)
	{
		return $this->emojiOne->toShort($string);
	}

	/**
	 * @return Client
	 */
	public function getClient()
	{
		return $this->emojiOne;
	}

	/**
	 * @return Ruleset
	 */
	public function getRuleset()
	{
		return new Ruleset();
	}

	protected function setTypeSpecificDefaults($config)
	{
		if ($config['style'] == 'native')
		{
			return $config;
		}

		$useCdn = ($config['source'] == 'cdn');

		if ($config['style'] == 'emojione')
		{
			$config['path'] = $useCdn ? 'https://cdn.jsdelivr.net/gh/joypixels/emoji-assets@5.0/png/64/' : $config['path'];
			$config['uc_filename'] = self::UC_BASE;
			$config['filename_formatter'] = function($filename) { return $filename; };
		}
		else if ($config['style'] == 'twemoji')
		{
			$config['path'] = $useCdn ? 'https://twemoji.maxcdn.com/2/72x72/' : $config['path'];
			$config['uc_filename'] = self::UC_OUTPUT;
			$config['filename_formatter'] = function($filename)
			{
				// Twemoji strips the leading zeros.
				if (strpos($filename, '00') === 0)
				{
					$filename = preg_replace('/^(00)/', '', $filename);
				}

				// Some Twemoji symbols don't include the fe0f 'variant form' indicator
				if (strpos($filename, '-fe0f-') !== false)
				{
					$filename = preg_replace('/^(\w{2})(?:-fe0f-)(.*)$/', '$1-$2', $filename);
				}

				// Similar to above but this is a single filename that doesn't map properly so tidy it up manually.
				if ($filename === '1f441-fe0f-200d-1f5e8-fe0f')
				{
					$filename = '1f441-200d-1f5e8';
				}

				return $filename;
			};
		}

		return $config;
	}
}