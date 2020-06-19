<?php

namespace XF\Data;

class Search
{
	public function getSearchDomains()
	{
		return [
			'ask',
			'baidu',
			'bing',
			'dogpile',
			'excite',
			'google',
			'lycos',
			'search.aol',
			'search.yahoo',
			'yandex',
		];
	}

	public function urlMatchesSearchDomain($url)
	{
		$url = @parse_url($url);

		if ($url && !empty($url['host']))
		{
			$host = strtolower($url['host']);
			$domainListRegex = implode('|', array_map('preg_quote', $this->getSearchDomains()));

			if (preg_match('#(^|\.)(' . $domainListRegex . ')\.(co\.|com\.)?[a-z]{2,}$#', $host, $match))
			{
				return $match[2];
			}
		}

		return false;
	}
}