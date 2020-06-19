<?php

namespace XF\Util;

class Hash
{
	public static function hashTextFile($fileName, $method = 'md5')
	{
		return self::hashText(file_get_contents($fileName), $method);
	}

	public static function hashText($text, $method = 'md5')
	{
		$contents = str_replace("\r", '', $text);
		return self::hash($method, $contents);
	}

	public static function hash($method, $contents)
	{
		return hash($method, $contents);
	}
}