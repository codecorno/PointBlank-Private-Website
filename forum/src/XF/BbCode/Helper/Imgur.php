<?php

namespace XF\BbCode\Helper;

class Imgur
{
	public static function matchCallback($url, $matchedId, \XF\Entity\BbCodeMediaSite $site, $siteId)
	{
		if ($matchedId === 'user')
		{
			// special case user URLs - a link to a favorite belonging to a user can be embedded otherwise skip

			if (strpos($url, 'favorites/') !== false)
			{
				if (preg_match('#favorites/(.*)$#iUs', $url, $matches))
				{
					if (strlen(trim($matches[1])))
					{
						$matchedId = 'a/' . $matches[1];
					}
					else
					{
						return false;
					}
				}
			}
			else
			{
				return false;
			}
		}

		if (strpos($url, 'gallery/' . $matchedId) !== false
			|| strpos($url, 'a/' . $matchedId) !== false
		)
		{
			$matchedId = 'a/' . $matchedId;
		}

		return $matchedId;
	}
}