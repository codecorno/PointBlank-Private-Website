<?php

namespace XF\BbCode\Helper;

class Spotify
{
	const URL_REGEX = '#open\.spotify\.com/(?P<type>track|album|artist|user/\w+/playlist)/(?P<id>[A-Z0-9]+)#si';

	public static function matchCallback($url, $matchedId, \XF\Entity\BbCodeMediaSite $site, $siteId)
	{
		if (preg_match(self::URL_REGEX, $url, $media))
		{
			return str_replace('/', ':', $media['type']) . ':' . $media['id'];
		}

		return false;
	}
}