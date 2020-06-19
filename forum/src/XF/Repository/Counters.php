<?php

namespace XF\Repository;

use XF\Mvc\Entity\Repository;

class Counters extends Repository
{
	public function getForumStatisticsCacheData()
	{
		$cache = [];

		/** @var \XF\Repository\Forum $forumRepo */
		$forumRepo = $this->repository('XF:Forum');
		$cache += $forumRepo->getForumCounterTotals();

		/** @var \XF\Repository\User $userRepo */
		$userRepo = $this->repository('XF:User');

		$cache['users'] = $userRepo->findValidUsers()->total();

		$latestUser = $userRepo->getLatestValidUser();
		$cache['latestUser'] = $latestUser ? $latestUser->toArray() : null;

		return $cache;
	}

	public function rebuildForumStatisticsCache()
	{
		$cache = $this->getForumStatisticsCacheData();
		\XF::registry()->set('forumStatistics', $cache);
		return $cache;
	}
}