<?php

namespace XF\Cron;

/**
 * Cron entry for executing user group promotions.
 */
class UserGroupPromotion
{
	/**
	 * Runs the cron-based check for new promotions that users should be awarded.
	 */
	public static function runPromotions()
	{
		/** @var \XF\Repository\UserGroupPromotion $promotionRepo */
		$promotionRepo = \XF::repository('XF:UserGroupPromotion');

		$promotions = $promotionRepo->getActiveUserGroupPromotions();
		if (!$promotions)
		{
			return;
		}

		/** @var \XF\Finder\User $userFinder */
		$userFinder = \XF::app()->finder('XF:User');
		$userFinder->where('last_activity', '>', time() - 2 * 3600)
			->with(['Profile', 'Option'])
			->order('user_id');

		$users = $userFinder->fetch();

		$userGroupPromotionLogs = $promotionRepo->getUserGroupPromotionLogsForUsers($users->keys());

		foreach ($users AS $user)
		{
			$promotionRepo->updatePromotionsForUser(
				$user,
				isset($userGroupPromotionLogs[$user->user_id]) ? $userGroupPromotionLogs[$user->user_id] : [],
				$promotions
			);
		}
	}
}