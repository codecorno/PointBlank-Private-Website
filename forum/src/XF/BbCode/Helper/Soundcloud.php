<?php

namespace XF\BbCode\Helper;

class Soundcloud
{
	const URL_REGEX = '#soundcloud\.com/(?P<user>[A-Z0-9\-_]+)(?:/)?(?P<type>sets|albums)?(?:/)?((?P<item>[A-Z0-9\-_]+)?(?P<secret>[A-Z0-9\-_]+)?(?:/)?(?P<time>\#t=[0-9:]+)?)#si';

	public static function matchCallback($url, $matchedId, \XF\Entity\BbCodeMediaSite $site, $siteId)
	{
		if (preg_match(self::URL_REGEX, $url, $i))
		{
			if (!empty($i['item']))
			{
				if (empty($i['type'])) // track
				{
					return $i['user'] . '/' . $i['item'] . (empty($i['secret']) ? '' : '/' . $i['secret']) . (empty($i['time']) ? '' : $i['time']);
				}
				else // playlist
				{
					return $i['user'] . '/sets/' . $i['item'];
				}
			}
			else // user (or albums page)
			{
				return $i['user'];
			}
		}
		else
		{
			return false;
		}
	}
}