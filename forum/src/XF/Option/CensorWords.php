<?php

namespace XF\Option;

class CensorWords extends AbstractOption
{
	public static function renderOption(\XF\Entity\Option $option, array $htmlParams)
	{
		$choices = [];
		foreach ($option->option_value AS $word)
		{
			$choices[] = [
				'word' => $word['word'],
				'replace' => is_string($word['replace']) ? $word['replace'] : ''
			];
		}

		return self::getTemplate('admin:option_template_censorWords', $option, $htmlParams, [
			'choices' => $choices,
			'nextCounter' => count($choices)
		]);
	}

	public static function verifyOption(array &$value)
	{
		$output = [];

		foreach ($value AS $word)
		{
			if (!isset($word['word']) || !isset($word['replace']))
			{
				continue;
			}

			$cache = self::buildCensorCacheValue($word['word'], $word['replace']);
			if ($cache)
			{
				$output[] = $cache;
			}
		}

		$value = $output;

		return true;
	}

	/**
	 * Builds the regex and censor cache value for a find/replace pair
	 *
	 * @param string $find
	 * @param string $replace
	 *
	 * @return array|bool
	 */
	public static function buildCensorCacheValue($find, $replace)
	{
		$find = trim(strval($find));
		if ($find === '')
		{
			return false;
		}

		$prefixWildCard = preg_match('#^\*#', $find);
		$suffixWildCard = preg_match('#\*$#', $find);

		$replace = is_int($replace) ? '' : trim(strval($replace));
		if ($replace === '')
		{
			$replace = utf8_strlen($find);
			if ($prefixWildCard)
			{
				$replace--;
			}
			if ($suffixWildCard)
			{
				$replace--;
			}
		}

		$regexFind = $find;
		if ($prefixWildCard)
		{
			$regexFind = substr($regexFind, 1);
		}
		if ($suffixWildCard)
		{
			$regexFind = substr($regexFind, 0, -1);
		}

		if (!strlen($regexFind))
		{
			return false;
		}

		$regex = '#'
			. ($prefixWildCard ? '' : '(?<=\W|^)')
			. preg_quote($regexFind, '#')
			. ($suffixWildCard ? '' : '(?=\W|$)')
			. '#iu';

		return [
			'word' => $find,
			'regex' => $regex,
			'replace' => $replace
		];
	}
}