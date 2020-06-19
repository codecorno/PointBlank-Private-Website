<?php

namespace XF\Util;

class Time
{
	public static function getIntervalArray($time, $withDays = true)
	{
		return [
			'days' => floor($time / 86400),
			'hours' => floor(($withDays ? ($time % 86400) : $time) / 3600),
			'minutes' => floor(($time % 3600) / 60),
			'seconds' => $time % 60,
		];
	}
}