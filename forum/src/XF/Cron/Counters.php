<?php

namespace XF\Cron;

/**
 * Cron entry for timed counter updates.
 */
class Counters
{
	/**
	 * Rebuilds the board totals counter.
	 */
	public static function rebuildForumStatistics()
	{
		/** @var \XF\Repository\Counters $countersRepo */
		$countersRepo = \XF::app()->repository('XF:Counters');
		$countersRepo->rebuildForumStatisticsCache();
	}

	/**
	 * Log daily statistics
	 */
	public static function recordDailyStats()
	{
		/** @var \XF\Repository\Stats $statsRepo */
		$statsRepo = \XF::app()->repository('XF:Stats');
		
		// get the the timestamp of 00:00 UTC for today
		$time = \XF::$time - \XF::$time % 86400;
		$statsRepo->build($time - 86400, $time);
	}
}