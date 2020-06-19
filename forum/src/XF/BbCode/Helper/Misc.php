<?php

namespace XF\BbCode\Helper;

class Misc
{
	protected static $ampersandFind = '&';
	protected static $ampersandReplace = ':';

	public static function matchEncodeAmpersands($url, $matchedId, \XF\Entity\BbCodeMediaSite $site, $siteId)
	{
		return str_replace(self::$ampersandFind, self::$ampersandReplace, $matchedId);
	}

	public static function embedDecodeAmpersands($mediaKey, array $site, $siteId)
	{
		return \XF::app()->templater()->renderTemplate('public:_media_site_embed_' . $siteId, [
			'id' => str_replace(self::$ampersandReplace, self::$ampersandFind, rawurldecode($mediaKey)),
			'site' => $site
		]);
	}
}