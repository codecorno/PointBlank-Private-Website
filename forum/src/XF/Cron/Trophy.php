<?php

namespace XF\Cron;

/**
 * Cron entry for manipulating trophies.
 */
class Trophy
{
	/**
	 * Runs the cron-based check for new trophies that users should be awarded.
	 */
	public static function runTrophyCheck()
	{
		if (!\XF::options()->enableTrophies)
		{
			return;
		}

		/** @var \XF\Repository\Trophy $trophyRepo */
		$trophyRepo = \XF::repository('XF:Trophy');
		$trophies = $trophyRepo->findTrophiesForList()->fetch();
		if (!$trophies)
		{
			return;
		}

		$userFinder = \XF::finder('XF:User');

		$users = $userFinder
			->where('last_activity', '>=',time() - 2 * 3600)
			->isValidUser(false)
			->fetch();

		$userTrophies = $trophyRepo->findUsersTrophies($users->keys())->fetch()->groupBy('user_id');
		foreach ($users AS $user)
		{
			$trophyRepo->updateTrophiesForUser($user, isset($userTrophies[$user->user_id]) ? $userTrophies[$user->user_id] : [], $trophies);
		}
	}
}