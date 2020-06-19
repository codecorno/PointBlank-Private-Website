<?php

namespace XF\Repository;

use XF\Mvc\Entity\Finder;
use XF\Mvc\Entity\Repository;

class UserGroupPromotion extends Repository
{
	/**
	 * @return Finder
	 */
	public function findUserGroupPromotionsForList()
	{
		return $this->finder('XF:UserGroupPromotion')->order('title');
	}

	/**
	 * @return \XF\Entity\UserGroupPromotion[]
	 */
	public function getActiveUserGroupPromotions()
	{
		return $this->finder('XF:UserGroupPromotion')->where('active', true)->fetch()->toArray();
	}

	/**
	 * @return Finder
	 */
	public function findUserGroupPromotionLogsForList()
	{
		return $this->finder('XF:UserGroupPromotionLog')->order('promotion_date', 'DESC');
	}

	public function getUserGroupPromotionLogsForUsers(array $userIds)
	{
		if (!$userIds)
		{
			return [];
		}

		$finder = $this->finder('XF:UserGroupPromotionLog')
			->where('user_id', $userIds)
			->order('promotion_date', 'desc');

		$logsGrouped = [];
		foreach ($finder->fetch() AS $log)
		{
			$logsGrouped[$log->user_id][$log->promotion_id] = $log;
		}

		return $logsGrouped;
	}

	public function getUserGroupPromotionLogsForUser($userId)
	{
		$logs = $this->getUserGroupPromotionLogsForUsers([$userId]);
		return isset($logs[$userId]) ? $logs[$userId] : [];
	}

	public function getUserGroupPromotionTitlePairs()
	{
		return $this->findUserGroupPromotionsForList()->fetch()->pluck(function($e, $k)
		{
			return [$k, $e->title];
		});
	}

	/**
	 * @param \XF\Entity\User $user
	 * @param \XF\Entity\UserGroupPromotionLog[] $userGroupPromotionLogs
	 * @param \XF\Entity\UserGroupPromotion[] $userGroupPromotions
	 * @return int
	 */
	public function updatePromotionsForUser(\XF\Entity\User $user, $userGroupPromotionLogs = null, $userGroupPromotions = null)
	{
		if ($userGroupPromotionLogs === null)
		{
			$userGroupPromotionLogs = $this->getUserGroupPromotionLogsForUser($user->user_id);
		}

		if ($userGroupPromotions === null)
		{
			$userGroupPromotions = $this->getActiveUserGroupPromotions();
		}

		$changes = 0;

		foreach ($userGroupPromotions AS $userGroupPromotion)
		{
			if (isset($userGroupPromotionLogs[$userGroupPromotion->promotion_id]))
			{
				$skip = false;
				switch ($userGroupPromotionLogs[$userGroupPromotion->promotion_id]->promotion_state)
				{
					case 'manual': // has it, don't take it away
					case 'disabled': // never give it
						$skip = true;
				}
				if ($skip)
				{
					continue;
				}
				$hasPromotion = true;
			}
			else
			{
				$hasPromotion = false;
			}

			$userCriteria = $this->app()->criteria('XF:User', $userGroupPromotion->user_criteria);
			$userCriteria->setMatchOnEmpty(false);
			if ($userCriteria->isMatched($user))
			{
				if (!$hasPromotion)
				{
					$userGroupPromotion->promote($user);
					$changes++;
				}
			}
			else if ($hasPromotion)
			{
				$userGroupPromotion->demote($user);
				$changes++;
			}
		}

		return $changes;
	}
}