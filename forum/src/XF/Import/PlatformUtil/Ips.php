<?php

namespace XF\Import\PlatformUtil;

use XF\Util\Arr;

class Ips
{
	public static function getConfig($basePath = null)
	{
		if ($basePath === null)
		{
			$basePath = getcwd();
		}

		$configPath = "$basePath/conf_global.php";

		if (!file_exists($configPath) && !is_readable($configPath))
		{
			return [];
		}

		$INFO = [];
		include($configPath);
		return $INFO;
	}

	public static function getDbConfig($basePath = null)
	{
		$config = self::getConfig($basePath);

		return [
			'host' => !empty($config['sql_host']) ? $config['sql_host'] : \XF::config()['db']['host'],
			'port' => !empty($config['sql_port']) ? $config['sql_port'] : \XF::config()['db']['port'],
			'username' => !empty($config['sql_user']) ? $config['sql_user'] : \XF::config()['db']['username'],
			'password' => !empty($config['sql_pass']) ? $config['sql_pass'] : '',
			'dbname' => !empty($config['sql_database']) ? $config['sql_database'] : '',
			'tablePrefix' => !empty($config['sql_tbl_prefix']) ? $config['sql_tbl_prefix'] : ''
		];
	}

	public static function getDefaultImportConfig()
	{
		return [
			'db' => [
				'host'     => '',
				'username' => '',
				'password' => '',
				'dbname'   => '',
				'port'     => 3306,
				'tablePrefix'   => ''
			],
			'ips_path'     => null,
			'forum_import_log' => ''
		];
	}

	public static function validateImportConfig(array &$baseConfig, array &$errors, $validateForumLog = false)
	{
		$baseConfig['db']['tablePrefix'] = preg_replace('/[^a-z0-9_]/i', '', $baseConfig['db']['tablePrefix']);

		$fullConfig = array_replace_recursive(self::getDefaultImportConfig(), $baseConfig);
		$missingFields = false;

		if ($fullConfig['db']['host'])
		{
			$validDbConnection = false;

			try
			{
				$sourceDb = new \XF\Db\Mysqli\Adapter($fullConfig['db'], true);
				$sourceDb->getConnection();
				$validDbConnection = true;
			}
			catch (\XF\Db\Exception $e)
			{
				$errors[] = \XF::phrase('source_database_connection_details_not_correct_x', ['message' => $e->getMessage()]);
			}

			if ($validDbConnection)
			{
				try
				{
					$sourceDb->fetchOne("
						SELECT member_id
						FROM core_members
						ORDER BY member_id
						LIMIT 1
					");
				}
				catch (\XF\Db\Exception $e)
				{
					if ($fullConfig['db']['dbname'] === '')
					{
						$errors[] = \XF::phrase('please_enter_database_name');
					}
					else
					{
						$errors[] = \XF::phrase('table_prefix_or_database_name_is_not_correct');
					}
				}

				if ($validateForumLog)
				{
					if ($fullConfig['forum_import_log'])
					{
						$logExists = \XF::db()->getSchemaManager()->tableExists($fullConfig['forum_import_log']);
						if (!$logExists)
						{
							$errors[] = \XF::phrase('forum_import_log_cannot_be_found');
						}
					}
					else
					{
						$missingFields = true;
					}
				}
			}
			else
			{
				$missingFields = true;
			}
		}

		if ($fullConfig['ips_path'])
		{
			$path = rtrim($fullConfig['ips_path'], '/\\ ');

			if (!file_exists($path) || !is_dir($path))
			{
				$errors[] = \XF::phrase('directory_x_not_found_is_not_readable', ['dir' => $path]);
			}
			else if (!file_exists("$path/uploads") || !is_dir("$path/uploads"))
			{
				$errors[] = \XF::phrase('directory_x_does_not_contain_expected_contents', ['dir' => $path]);
			}

			$baseConfig['ips_path'] = $path;
		}
		else
		{
			$missingFields = true;
		}

		if ($missingFields)
		{
			$errors[] = \XF::phrase('please_complete_required_fields');
		}
	}

	public static function getFieldType($oldFieldType, $multiple = false)
	{
		switch ($oldFieldType)
		{
			case 'Color':
			case 'Date':
			case 'Email':
			case 'Member':
			case 'Number':
			case 'Tel':
			case 'Text':
			case 'Url':
				return 'textbox';

			case 'Address':
			case 'Codemirror':
			case 'TextArea':
				return 'textarea';

			case 'Checkbox':
			case 'CheckboxSet':
			case 'YesNo':
				return 'checkbox';

			case 'Editor':
				return 'bbcode';

			case 'Radio':
				return 'radio';

			case 'Rating':
				return 'stars';

			case 'Select':
				if ($multiple)
				{
					return 'multiselect';
				}
				else
				{
					return 'select';
				}

			case 'Password': // could import as text but no way to obfuscate it
			case 'Poll':
			case 'Upload':
			default:
				return null; // incompatible so skip
		}
	}

	public static function getMatchType($oldFieldType)
	{
		switch ($oldFieldType)
		{
			case 'Color':
				return 'color';

			case 'Date':
				return 'date';

			case 'Email':
				return 'email';

			case 'Number':
			case 'Tel':
				return 'number';

			case 'Url':
				return 'url';

			default:
				return 'none';
		}
	}

	public static function isFieldChoiceType($oldFieldType)
	{
		switch ($oldFieldType)
		{
			case 'Checkbox':
			case 'CheckboxSet':
			case 'Radio':
			case 'Select':
			case 'YesNo':
				return true;

			default:
				return false;
		}
	}

	public static function convertFieldChoices($oldFieldType, $choices)
	{
		$choices = @json_decode($choices, true) ?: [];

		switch ($oldFieldType)
		{
			case 'Checkbox':
			case 'YesNo':
				$choices[] = 'Yes';
				break;
		}

		return $choices;
	}

	/**
	 * Converts IPS 4's regex into a regular expression without delimiters so we can check it within our own.
	 *
	 * @param string $inputFormat
	 *
	 * @return string
	 */
	public static function convertFieldMatchTypeToRegex($inputFormat)
	{
		$delimiter = $inputFormat[0];
		$lastDelPos = strrpos($inputFormat, $delimiter);
		if ($lastDelPos !== false)
		{
			$inputFormat = substr($inputFormat, 1, $lastDelPos - 1);
		}
		return $inputFormat;
	}

	public static function getFieldValue($oldFieldType, $fieldValue, $multiple, array $fieldChoices, \XF\Db\AbstractAdapter $sourceDb)
	{
		switch ($oldFieldType)
		{
			case 'Address':
				$decodedValue = @json_decode($fieldValue, true) ?: [];

				if (!$decodedValue)
				{
					return '';
				}

				$address = '';

				if (isset($decodedValue['addressLines']) && is_array($decodedValue['addressLines']))
				{
					$address .= implode(' ', $decodedValue['addressLines']) . "\n";
				}

				$additionalParts = [];

				if (isset($decodedValue['city']))
				{
					$additionalParts[] = $decodedValue['city'];
				}
				if (isset($decodedValue['region']))
				{
					$additionalParts[] = $decodedValue['region'];
				}
				if (isset($decodedValue['country']))
				{
					$additionalParts[] = $decodedValue['country'];
				}
				if (isset($decodedValue['postalCode']))
				{
					$additionalParts[] = $decodedValue['postalCode'];
				}

				$address .= implode("\n", $additionalParts);

				return $address ?: '';

			case 'Checkbox':
			case 'YesNo':
				if (!$fieldValue)
				{
					return [];
				}
				else
				{
					return ['0']; // option 0 (Yes) is checked
				}

			case 'CheckboxSet':
				return Arr::stringToArray($fieldValue, '/,/');

			case 'Date':
				return date('Y-m-d', $fieldValue);

			case 'Editor':
				return self::convertContentToBbCode($fieldValue);

			case 'Member':
				$memberIds = Arr::stringToArray($fieldValue);

				if ($memberIds)
				{
					$usernames = $sourceDb->fetchAllColumn("
						SELECT `name`
						FROM core_members
						WHERE member_id IN(" . $sourceDb->quote($memberIds) . ")
						ORDER BY member_id
					");

					return implode(', ', $usernames);
				}

				return $fieldValue;

			case 'Radio':
			case 'Select':
				if ($multiple)
				{
					$values = Arr::stringToArray($fieldValue, '/,/');

					$keys = [];
					foreach ($values AS $value)
					{
						$keys[] = array_search($value, $fieldChoices);
					}

					if ($keys)
					{
						return $keys;
					}
				}
				else
				{
					$key = array_search($fieldValue, $fieldChoices);

					if ($key)
					{
						return $key;
					}
				}

				return $fieldValue;

			default:
				return $fieldValue;
		}
	}

	public static function stripRichText($content)
	{
		$content = preg_replace('/<br( \/)?>(\r?\n)?/si', "\n", $content);
		$content = str_replace('&nbsp;' , ' ', $content);

		$content = strip_tags($content);

		return $content;
	}

	public static function convertContentToBbCode($content, $quoteType = null)
	{
		$content = preg_replace('/<br( \/)?>(\r?\n)?/si', "\n", $content);
		$content = str_replace('&nbsp;' , ' ', $content);

		$content = self::convertEmbeddedVideo($content);
		$content = self::convertQuotes($content, $quoteType);

		$replacements = self::getContentReplacements();

		$content = preg_replace(array_keys($replacements), $replacements, $content);
		$content = strip_tags($content);

		return $content;
	}

	public static function convertEmbeddedVideo($content)
	{
		if (stripos($content, 'ipsEmbeddedVideo') !== false)
		{
			$content = preg_replace_callback(
				'#<div [^>]*class=(?:"|\')ipsEmbeddedVideo.*?(?:"|\')[^>]*>.*?<div>.*?<iframe [^>]*src=(?:"|\')(.*)(?:"|\')[^>]*(?:data-embed-src=(?:"|\')(.*)(?:"|\')[^>]*)?></iframe>.*?</div>.*?</div>#siU',
				function(array $matches)
				{
					if (isset($matches[2])) // data-embed-src
					{
						$url = $matches[2];
					}
					else // src
					{
						$url = $matches[1];
					}

					/** @var \XF\Repository\BbCodeMediaSite $mediaSiteRepo */
					$mediaSiteRepo = \XF::repository('XF:BbCodeMediaSite');
					$matchedSite = $mediaSiteRepo->urlMatchesMediaSiteList($url, self::getMediaSites());

					if ($matchedSite)
					{
						return '[MEDIA=' . $matchedSite['media_site_id'] . ']' . $matchedSite['media_id'] . '[/MEDIA]';
					}
					else
					{
						return '[URL]' . $url . '[/URL]';
					}
				},
				$content
			);
		}

		if (stripos($content, 'ipsEmbeddedOther') !== false)
		{
			$content = preg_replace_callback(
				'#<div [^>]*class=(?:"|\')ipsEmbeddedOther.*?(?:"|\')[^>]*>.*?<iframe [^>]*data-embed-src=(?:"|\').*;url=(.*)(?:"|\')[^>]*></iframe>.*?</div>#siU',
				function(array $matches)
				{
					/** @var \XF\Repository\BbCodeMediaSite $mediaSiteRepo */
					$mediaSiteRepo = \XF::repository('XF:BbCodeMediaSite');
					$matchedSite = $mediaSiteRepo->urlMatchesMediaSiteList($matches[1], self::getMediaSites());

					if ($matchedSite)
					{
						return '[MEDIA=' . $matchedSite['media_site_id'] . ']' . $matchedSite['media_id'] . '[/MEDIA]';
					}
					else
					{
						return '[URL]' . $matches[1] . '[/URL]';
					}
				},
				$content
			);
		}

		return $content;
	}

	protected static $mediaSites;

	protected static function getMediaSites()
	{
		if (self::$mediaSites === null)
		{
			self::$mediaSites = \XF::repository('XF:BbCodeMediaSite')->findActiveMediaSites()->fetch();
		}

		return self::$mediaSites;
	}

	public static function convertQuotes($content, $quoteType = null)
	{
		if (stripos($content, 'ipsQuote') !== false)
		{
			$quoteReplacements = self::getQuoteReplacements($quoteType);
			foreach ($quoteReplacements AS $pattern => $replacement)
			{
				do
				{
					$newContent = preg_replace($pattern, $replacement, $content);
					if ($newContent === $content)
					{
						break;
					}

					$content = $newContent;
				}
				while (true);
			}
		}

		return $content;
	}

	protected static function getQuoteReplacements($quoteType = null)
	{
		return [
			// IPS 4.1+ quotes
			'#<blockquote [^>]*class="ipsQuote"[^>]*data-ipsquote-username="([^"]+)"[^>]*data-ipsquote-contentcommentid="(\d+)"[^>]*>.*<div [^>]*class="ipsQuote_contents[^"]*"[^>]*>\s*?(.*)\s*?</div>\s*</blockquote>(\r?\n)??#siU' => '[QUOTE="\\1, ' . ($quoteType ?: 'post') . ': \\2"]\\3[/QUOTE]',
			'#<blockquote [^>]*class="ipsQuote"[^>]*data-ipsquote-contentcommentid="(\d+)"[^>]*data-ipsquote-username="([^"]+)"[^>]*>.*<div [^>]*class="ipsQuote_contents[^"]*"[^>]*>\s*?(.*)\s*?</div>\s*</blockquote>(\r?\n)??#siU' => '[QUOTE="\\2, ' . ($quoteType ?: 'post') . ': \\1"]\\3[/QUOTE]',
			'#<blockquote [^>]*class="ipsQuote"[^>]*data-ipsquote-username="([^"]+)"[^>]*>.*<div [^>]*class="ipsQuote_contents[^"]*"[^>]*>(.*)</div>\s*</blockquote>(\r?\n)??#siU' => '[QUOTE=\\1]\\2[/QUOTE]',
			'#<blockquote [^>]*class="ipsQuote"[^>]*>.*<div [^>]*class="ipsQuote_contents[^"]*"[^>]*>(.*)</div>\s*</blockquote>(\r?\n)??#siU' => '[QUOTE]\\1[/QUOTE]',

			// IPS 4.0 quotes
			'#<blockquote [^>]*class="ipsQuote"[^>]*data-cite="([^"]+)"[^>]*data-ipsquote-contentcommentid="(\d+)"[^>]*>(.*)</blockquote>(\r?\n)??#siU' => '[QUOTE="\\1, ' . ($quoteType ?: 'post') . ': \\2"]\\3[/QUOTE]',
			'#<blockquote [^>]*class="ipsQuote"[^>]*data-ipsquote-contentcommentid="(\d+)"[^>]*data-cite="([^"]+)"[^>]*>(.*)</blockquote>(\r?\n)??#siU' => '[QUOTE="\\2, ' . ($quoteType ?: 'post') . ': \\1"]\\3[/QUOTE]',
			'#<blockquote [^>]*class="ipsQuote"[^>]*data-cite="([^"]+)"[^>]*>(.*)</blockquote>(\r?\n)??#siU' => '[QUOTE=\\1]\\2[/QUOTE]',
			'#<blockquote [^>]*class="ipsQuote"[^>]*>(.*)</blockquote>(\r?\n)??#siU' => '[QUOTE]\\1[/QUOTE]'
		];
	}

	protected static function getContentReplacements()
	{
		return [
			// this is likely the closest to correct this can be - in IPS this is replaced with the base_url as stored in settings
			// but this can be blank, so it would still leave IMG and URLs with relative URLs which will not work in XF.
			'#<___base_url___>#siU' => \XF::app()->request()->getFullBasePath(),

			// common attachment links - attachment links containing thumbnailed images
			'#<a [^>]*href=(\'|")([^"\']+)\\1[^>]*class="ipsAttachLink\s*ipsAttachLink_image".*data-fileid="(\d+)".*</a>#siU' => '[ATTACH]\\3[/ATTACH]',
			'#<a [^>]*class="ipsAttachLink\s*ipsAttachLink_image"[^>]*href=(\'|")([^"\']+)\\1.*data-fileid="(\d+)".*</a>#siU' => '[ATTACH]\\3[/ATTACH]',

			// common attachment links - attachment links pointing to attached files
			'#<a [^>]*href=".*attachment\.php\?id=(\d+)"[^>]*class="ipsAttachLink"[^>]*>.*</a>#siU' => '[ATTACH]\\1[/ATTACH]',
			'#<a [^>]*class="ipsAttachLink"[^>]*href=".*attachment\.php\?id=(\d+)"[^>]*>.*</a>#siU' => '[ATTACH]\\1[/ATTACH]',

			// less common attachment links - attached image no link
			'#<img [^>]*class="ipsImage\s*ipsImage_thumbnailed"[^>]*data-fileid="(\d+)"[^>]*src="[^"]*"[^>]*>#siU' => '[ATTACH]\\1[/ATTACH]',

			// code block - handle it specifically
			'#<pre [^>]*class="ipsCode"[^>]*>(.*)</pre>(\r?\n)??#siU' => '[CODE]\\1[/CODE]',

			// user mentions
			'#<a\s+[^>]*data-mentionid=(?:"|\')(\d+)(?:"|\')\s+[^>]*>(\@.+)</a>#siU' => '[USER=\\1]\\2[/USER]',

			// emoticons
			'#<img [^>]*src="<fileStore\.core_Emoticons>[^>]*"[^>]*alt="([^"]+)" srcset=".*"[^>]*>#siU' => ' \\1 ',
			'#<img [^>]*alt="([^"]+)"[^>]*src="<fileStore\.core_Emoticons>[^>]*" srcset=".*"[^>]*>#siU' => ' \\1 ',
			'#<img [^>]*src="<fileStore\.core_Emoticons>[^>]*"[^>]*alt="([^"]+)"[^>]*>#siU' => ' \\1 ',
			'#<img [^>]*alt="([^"]+)"[^>]*src="<fileStore\.core_Emoticons>[^>]*"[^>]*>#siU' => ' \\1 ',

			// IPS 4.0 spoiler
			'#<blockquote [^>]*class="ipsStyle_spoiler"[^>]*>(.*)</blockquote>(\r?\n)??#siU' => '[SPOILER]\\1[/SPOILER]',

			// IPS 4.1+ spoiler
			'#<div [^>]*class="ipsSpoiler"[^>]*>.*<div [^>]*class="ipsSpoiler_contents"[^>]*>(.*)</div>\s*</div>(\r?\n)??#siU' => '[SPOILER]\\1[/SPOILER]',

			'#<span [^>]*style="color:\s*([^";\\]]+?)[^"]*"[^>]*>(.*)</span>#siU' => '[COLOR=\\1]\\2[/COLOR]',
			'#<span [^>]*style="font-family:\s*([^";\\],]+?)[^"]*"[^>]*>(.*)</span>#siU' => '[FONT=\\1]\\2[/FONT]',
			'#<span [^>]*style="font-size:\s*([^";\\]]+?)[^"]*"[^>]*>(.*)</span>#siU' => '[SIZE=\\1]\\2[/SIZE]',
			'#<span[^>]*>(.*)</span>#siU' => '\\1',
			'#<(strong|b)(?:\s[^>]*)?>(.*)</\\1>#siU' => '[B]\\2[/B]',
			'#<(em|i)(?:\s[^>]*)?>(.*)</\\1>#siU' => '[I]\\2[/I]',
			'#<(u)(?:\s[^>]*)?>(.*)</\\1>#siU' => '[U]\\2[/U]',
			'#<(strike|s)(?:\s[^>]*)?>(.*)</\\1>#siU' => '[S]\\2[/S]',
			'#<a [^>]*href=(\'|")([^"\']+)\\1[^>]*>(.*)</a>#siU' => '[URL="\\2"]\\3[/URL]',
			'#<a [^>]*href=(http[^\s>]+?)[^>]*>(.*)</a>#siU' => '[URL="\\1"]\\2[/URL]',
			'#<img [^>]*src="([^"]+)"[^>]*>#' => '[IMG]\\1[/IMG]',
			'#<img [^>]*src=\'([^\']+)\'[^>]*>#' => '[IMG]\\1[/IMG]',

			'#<(p|div) [^>]*style="text-align:\s*left;?">\s*?(.*)\s*?</\\1>(\r?\n)??#siU' => "[LEFT]\\2[/LEFT]\n",
			'#<(p|div) [^>]*style="text-align:\s*center;?">\s*?(.*)\s*?</\\1>(\r?\n)??#siU' => "[CENTER]\\2[/CENTER]\n",
			'#<(p|div) [^>]*style="text-align:\s*right;?">\s*?(.*)\s*?</\\1>(\r?\n)??#siU' => "[RIGHT]\\2[/RIGHT]\n",
			'#<(p|div) [^>]*class="bbc_left"[^>]*>\s*?(.*)\s*?</\\1>(\r?\n)??#siU' => "[LEFT]\\2[/LEFT]\n",
			'#<(p|div) [^>]*class="bbc_center"[^>]*>\s*?(.*)\s*?</\\1>(\r?\n)??#siU' => "[CENTER]\\2[/CENTER]\n",
			'#<(p|div) [^>]*class="bbc_right"[^>]*>\s*?(.*)\s*?</\\1>(\r?\n)??#siU' => "[RIGHT]\\2[/RIGHT]\n",

			// lists
			'#<ul[^>]*>(.*)</ul>(\r?\n)??#siU' => "[LIST]\\1[/LIST]\n",
			'#<ol[^>]*>(.*)</ol>(\r?\n)??#siU' => "[LIST=1]\\1[/LIST]\n",
			'#<li[^>]*>(.*)</li>(\r?\n)??#siU' => "[*]\\1\n",


			// strip the unnecessary whitespace between start of bullet point and text
			'#(\[\*\])\s*?#siU' => '\\1',

			'#<(p|pre)[^>]*>(&nbsp;|' . chr(0xC2) . chr(0xA0) .'|\s)*</\\1>(\r?\n)??#siU' => "\n",
			'#<p[^>]*>\s*?(.*)\s*?</p>\s*?#siU' => "\\1\n\n",
			'#<div[^>]*>\s*?(.*)\s*?</div>\s*?#siU' => "\\1\n",

			'#<pre[^>]*>(.*)</pre>\s*?#siU' => "[CODE]\\1[/CODE]\n",

			'#<!--.*-->#siU' => ''
		];
	}
}