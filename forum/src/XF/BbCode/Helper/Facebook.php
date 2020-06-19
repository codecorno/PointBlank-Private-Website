<?php

namespace XF\BbCode\Helper;

class Facebook
{
	public static function htmlCallback($mediaKey, array $site, $siteId)
	{
		if (preg_match('#^[^/]+/posts/\d+$#', $mediaKey))
		{
			$id = $mediaKey;
			$type = 'post';
		}
		else if (preg_match('#^[^/]+/photos/(?:a\.\d+/)?\d+$#', $mediaKey))
		{
			$id = $mediaKey;
			$type = 'post';
		}
		else if (preg_match('#^story_fbid=(\d+):id=(\d+)$#', $mediaKey, $matches))
		{
			$id = 'permalink.php?story_fbid=' . $matches[1] . '&id=' . $matches[2];
			$type = 'post';
		}
		else if (preg_match('#^\d+$#', $mediaKey))
		{
			$id = $mediaKey;
			$type = 'video';
		}
		else
		{
			return '';
		}

		return \XF::app()->templater()->renderTemplate('public:_media_site_embed_facebook', [
			'type' => $type,
			'id' => rawurlencode($id),
			'idPlain' => $id
		]);
	}
}