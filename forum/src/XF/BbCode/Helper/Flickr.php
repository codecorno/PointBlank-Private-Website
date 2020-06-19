<?php

namespace XF\BbCode\Helper;

class Flickr
{
	const BASE58_ALPHABET = '123456789abcdefghijkmnopqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ';

	public static function matchCallback($url, $matchedId, $site, $siteId)
	{
		if (is_numeric($matchedId))
		{
			$matchedId = self::base58_encode($matchedId);
		}

		return $matchedId;
	}

	public static function base58_encode($num)
	{
		$base58 = self::BASE58_ALPHABET;
		$base_count = strlen($base58);

		$encoded = '';
		while ($num >= $base_count)
		{
			$div = $num / $base_count;
			$mod = intval($num - ($base_count * intval($div)));
			$encoded = $base58[$mod] . $encoded;
			$num = intval($div);
		}

		if ($num)
		{
			$encoded = $base58[$num] . $encoded;
		}

		return $encoded;
	}

	public static function base58_decode($num)
	{
		$decoded = 0;
		$multi = 1;

		while (strlen($num) > 0)
		{
			$digit = $num[strlen($num) - 1];
			$decoded += $multi * strpos(self::BASE58_ALPHABET, $digit);
			$multi = $multi * strlen(self::BASE58_ALPHABET);
			$num = substr($num, 0, -1);
		}

		return $decoded;
	}
}