<?php

namespace XF\BbCode\Helper;

class Reddit
{
	const URL_REGEX = '#reddit\.com/r/(?P<group>\w+)/comments/(?P<postid>[A-Z0-9]+)(/\w+/(?P<commentid>[A-Z0-9]+))?#si';
	const COMMENT_CHECK = '#/comments/(?<postid>\w+)/\w+/(?P<commentid>\w+)#si';

	public static function matchCallback($url, $matchedId, \XF\Entity\BbCodeMediaSite $site, $siteId)
	{
		if (preg_match(self::URL_REGEX, $url, $mediaInfo))
		{
			$matchedId = $mediaInfo['group'] . '/comments/' . $mediaInfo['postid'];

			if (!empty($mediaInfo['commentid']))
			{
				$matchedId .= '/_/' . $mediaInfo['commentid'];
			}
		}

		return $matchedId;
	}

	public static function htmlCallback($mediaKey, array $site, $siteId)
	{
		$idUrl = str_replace('%2F', '/', rawurlencode($mediaKey));

		return \XF::app()->templater()->renderTemplate('public:_media_site_embed_oembed', [
			'provider' => $siteId,
			'id' => $mediaKey,
			'site' => $site,
			'jsState' => preg_match(self::COMMENT_CHECK, $mediaKey) ? 'reddit_comment' : 'reddit',
			'url' => str_replace('{$id}', $idUrl, $site['oembed_url_scheme'])
		]);
	}
}