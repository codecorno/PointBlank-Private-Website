<?php

namespace XF\BbCode\Helper;

class Twitch
{
	public static function matchCallback($url, $matchedId, \XF\Entity\BbCodeMediaSite $site, $siteId)
	{
		if (preg_match('#^https?://(?P<subdomain>www|clips)\.twitch\.tv/(?P<id>\w+)$#i', $url, $match))
		{
			// match channel or clip
			return ($match['subdomain'] == 'clips' ? 'clip:' : '') . $match['id'];
		}
		else if (preg_match('#^https?://www\.twitch\.tv/videos/(?P<id>\d+)(?:\?t=(?P<time>[0-9hms]+))?$#si', $url, $match))
		{
			// match video with optional time
			if (isset($match['time']))
			{
				return $match['id'] . ':' . $match['time'];
			}
			else
			{
				return $match['id'];
			}
		}
		else
		{
			return false;
		}
	}

	public static function htmlCallback($mediaKey, array $site, $siteId)
	{
		$url = self::getUrl($mediaKey);
		if (!$url)
		{
			return '';
		}

		return \XF::app()->templater()->renderTemplate('public:_media_site_embed_twitch', [
			'site' => $site,
			'id' => rawurlencode($mediaKey),
			'idDigits' => intval($mediaKey),
			'url' => $url
		]);
	}

	protected static function getUrl($mediaId)
	{
		$mediaInfo = explode(':', $mediaId);

		if ($mediaInfo[0] == 'clip')
		{
			if (!isset($mediaInfo[1]))
			{
				return '';
			}

			$url = 'https://clips.twitch.tv/embed?autoplay=false&clip=' . rawurlencode($mediaInfo[1]);
		}
		else
		{
			$url = 'https://player.twitch.tv/?autoplay=false';

			if (is_numeric($mediaInfo[0]))
			{
				$url .= '&video=v' . rawurlencode($mediaInfo[0]);

				if (!empty($mediaInfo[1]))
				{
					$url .= '&t=' . rawurlencode($mediaInfo[1]);
				}
			}
			else
			{
				$url .= '&channel=' . rawurlencode($mediaInfo[0]);
			}
		}

		return $url;
	}

	//Channel:
	//https://www.twitch.tv/{alphanum}
	//
	//Video:
	//https://www.twitch.tv/videos/{digits}
	//
	//Clip:
	//https://clips.twitch.tv/{alphanum}
	//
	//Video with time:
	//https://www.twitch.tv/videos/{digits}?t=04h42m29s
}