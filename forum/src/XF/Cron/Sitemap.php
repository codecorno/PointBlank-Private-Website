<?php

namespace XF\Cron;

class Sitemap
{
	public static function triggerSitemapRebuild()
	{
		$app = \XF::app();
		if ($app->options()->sitemapAutoRebuild)
		{
			$app->jobManager()->enqueueUnique('sitemapAuto', 'XF:Sitemap', [], false);
		}
	}
}