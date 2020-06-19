<?php

namespace XF\BbCode\Helper;

class DailyMotion
{
	public static function matchCallback($url, $matchedId, \XF\Entity\BbCodeMediaSite $site, $siteId)
	{
		if (preg_match('#(\?|&)start=(?P<time>\d+)#si', $url, $matches))
		{
			$matchedId .= ':' . intval($matches['time']);
		}

		return $matchedId;
	}

	public static function htmlCallback($mediaKey, array $site, $siteId)
	{
		$mediaInfo = explode(':', $mediaKey);

		return \XF::app()->templater()->renderTemplate('public:_media_site_embed_dailymotion', [
			'id' => rawurlencode($mediaInfo[0]),
			'start' => isset($mediaInfo[1]) ? intval($mediaInfo[1]) : 0
		]);
	}
}