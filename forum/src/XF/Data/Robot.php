<?php

namespace XF\Data;

class Robot
{
	public function getRobotUserAgents()
	{
		return [
			'archive.org_bot' => 'archive.org',
			'baiduspider' => 'baidu',
			'bingbot' => 'bing',
			'facebookexternalhit' => 'facebookextern',
			'googlebot' => 'google',
			'ia_archiver' => 'alexa',
			'magpie-crawler' => 'brandwatch',
			'mediapartners-google' => 'google-adsense',
			'mj12bot' => 'mj12',
			'msnbot' => 'msnbot',
			'proximic' => 'proximic',
			'scoutjet' => 'scoutjet',
			'sogou web spider' => 'sogou',
			'yahoo! slurp' => 'yahoo',
			'yandex' => 'yandex',

			/*'crawler',
			'php/',
			'zend_http_client',*/
		];
	}

	public function userAgentMatchesRobot($userAgent)
	{
		$bots = $this->getRobotUserAgents();

		if (preg_match(
			'#(' . implode('|', array_map('preg_quote', array_keys($bots))) . ')#i',
			strtolower($userAgent),
			$match
		))
		{
			return $bots[$match[1]];
		}
		else
		{
			return '';
		}
	}

	public function getRobotList()
	{
		return [
			'alexa' => [
				'title' => 'Alexa',
				'link' => 'http://www.alexa.com/help/webmasters',
			],
			'archive.org' => [
				'title' => 'Internet Archive',
				'link' => 'http://www.archive.org/details/archive.org_bot'
			],
			'baidu' => [
				'title' => 'Baidu',
				'link' => 'http://www.baidu.com/search/spider.htm'
			],
			'bing' => [
				'title' => 'Bing',
				'link' => 'http://www.bing.com/bingbot.htm'
			],
			'brandwatch' => [
				'title' => 'Brandwatch',
				'link' => 'http://www.brandwatch.com/how-it-works/gathering-data/'
			],
			'facebookextern' => [
				'title' => 'Facebook',
				'link' => 'http://www.facebook.com/externalhit_uatext.php'
			],
			'google' => [
				'title' => 'Google',
				'link' => 'https://support.google.com/webmasters/answer/182072'
			],
			'google-adsense' => [
				'title' => 'Google AdSense',
				'link' => 'https://support.google.com/webmasters/answer/182072'
			],
			'mj12' => [
				'title' => 'Majestic-12',
				'link' => 'http://majestic12.co.uk/bot.php',
			],
			'msnbot' => [
				'title' => 'MSN',
				'link' => 'http://search.msn.com/msnbot.htm'
			],
			'proximic' => [
				'title' => 'Proximic',
				'link' => 'http://www.proximic.com/info/spider.php'
			],
			'scoutjet' => [
				'title' => 'Blekko',
				'link' => 'http://www.scoutjet.com/',
			],
			'sogou' => [
				'title' => 'Sogou',
				'link' => 'http://www.sogou.com/docs/help/webmasters.htm#07'
			],
			'unknown' => [
				'title' => 'Unknown',
				'link' => ''
			],
			'yahoo' => [
				'title' => 'Yahoo',
				'link' => 'http://help.yahoo.com/help/us/ysearch/slurp'
			],
			'yandex' => [
				'title' => 'Yandex',
				'link' => 'http://help.yandex.com/search/?id=1112030'
			]
		];
	}

	public function getRobotInfo($robot)
	{
		$list = $this->getRobotList();
		return isset($list[$robot]) ? $list[$robot] : null;
	}
}