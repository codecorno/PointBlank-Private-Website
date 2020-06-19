<?php

namespace XF\Cron;

class MemberStats
{
	public static function rebuildMemberStatsCache()
	{
		/** @var \XF\Repository\MemberStat $memberStatsRepo */
		$memberStatsRepo = \XF::app()->repository('XF:MemberStat');
		$finder = $memberStatsRepo->findCacheableMemberStats();

		foreach ($finder->fetch() AS $memberStat)
		{
			if (\XF::$time > $memberStat->cache_expiry)
			{
				/** @var \XF\Service\MemberStat\Preparer $preparer */
				$preparer = \XF::app()->service('XF:MemberStat\Preparer', $memberStat);
				$preparer->cache();
			}
		}
	}
}