<?php

namespace XF\BbCode\Helper;

class YouTube
{
	public static function matchCallback($url, $matchedId, \XF\Entity\BbCodeMediaSite $site, $siteId)
	{
		if (preg_match('#(\?|&)t=(?P<time>[0-9hms]+)#si', $url, $matches))
		{
			$matchedId .= ':' . self::getSecondsFromTimeString($matches['time']);
		}

		return $matchedId;
	}

	public static function htmlCallback($mediaKey, array $site, $siteId)
	{
		$mediaInfo = explode(':', $mediaKey);

		return \XF::app()->templater()->renderTemplate('public:_media_site_embed_youtube', [
			'id' => rawurlencode($mediaInfo[0]),
			'start' => isset($mediaInfo[1]) ? intval($mediaInfo[1]) : 0
		]);
	}

	/**
	 * @param $startTime String in the format 00h00m00s, larger components optional
	 *
	 * @return int
	 */
	public static function getSecondsFromTimeString($timeString)
	{
		$seconds = 0;

		if (preg_match('#^(?P<hours>\d+h)?(?P<minutes>\d+m)?(?P<seconds>\d+s?)$#si', $timeString, $time))
		{
			$seconds = intval($time['seconds']);
			$seconds += 60 * intval($time['minutes']);
			$seconds += 3600 * intval($time['hours']);
		}

		return $seconds;
	}
}