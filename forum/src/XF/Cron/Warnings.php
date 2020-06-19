<?php

namespace XF\Cron;

class Warnings
{
	public static function expireWarnings()
	{
		\XF::repository('XF:Warning')->processExpiredWarnings();
	}
}