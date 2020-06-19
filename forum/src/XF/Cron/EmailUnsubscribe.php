<?php

namespace XF\Cron;

class EmailUnsubscribe
{
	public static function process()
	{
		$handler = \XF::options()->emailUnsubscribeHandler;
		if ($handler && !empty($handler['enabled']))
		{
			\XF::app()->jobManager()->enqueueUnique('EmailUnsubscribe', 'XF:EmailUnsubscribe', [], false);
		}
	}
}