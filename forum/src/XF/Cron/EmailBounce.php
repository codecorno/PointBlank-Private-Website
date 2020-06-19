<?php

namespace XF\Cron;

class EmailBounce
{
	public static function process()
	{
		/** @var \XF\Repository\EmailBounce $bounceRepo */
		$bounceRepo = \XF::repository('XF:EmailBounce');
		$bounceRepo->pruneEmailBounceLogs();
		$bounceRepo->pruneSoftBounceHistory();

		$handler = \XF::options()->emailBounceHandler;
		if ($handler && !empty($handler['enabled']))
		{
			\XF::app()->jobManager()->enqueueUnique('EmailBounce', 'XF:EmailBounce', [], false);
		}
	}
}