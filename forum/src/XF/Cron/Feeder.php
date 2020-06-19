<?php

namespace XF\Cron;

/**
 * Cron entry for feed importer.
 */
class Feeder
{
	/**
	 * Imports feeds.
	 */
	public static function importFeeds()
	{
		$app = \XF::app();

		/** @var \XF\Repository\Feed $feedRepo */
		$feedRepo = $app->repository('XF:Feed');

		$dueFeeds = $feedRepo->findDueFeeds()->fetch();
		if ($dueFeeds->count())
		{
			$app->jobManager()->enqueueUnique('feederImport', 'XF:Feeder', [], false);
		}
	}
}