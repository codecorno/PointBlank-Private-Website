<?php

namespace XF\Job;

class UserGroupPromotion extends AbstractJob
{
	protected $defaultData = [
		'steps' => 0,
		'start' => 0,
		'batch' => 5
	];

	public function run($maxRunTime)
	{
		/** @var \XF\Repository\UserGroupPromotion $promotionRepo */
		$promotionRepo = $this->app->repository('XF:UserGroupPromotion');

		$promotions = $promotionRepo->getActiveUserGroupPromotions();
		if (!$promotions)
		{
			return $this->complete();
		}

		$startTime = microtime(true);

		$this->data['steps']++;

		$db = $this->app->db();

		$ids = $db->fetchAllColumn($db->limit(
			"
				SELECT user_id
				FROM xf_user
				WHERE user_id > ?
				ORDER BY user_id
			", $this->data['batch']
		), $this->data['start']);
		if (!$ids)
		{
			return $this->complete();
		}

		/** @var \XF\Finder\User $userFinder */
		$userFinder = $this->app->finder('XF:User');
		$userFinder->where('user_id', $ids)
			->with(['Profile', 'Option'])
			->order('user_id');

		$users = $userFinder->fetch();

		$userGroupPromotionLogs = $promotionRepo->getUserGroupPromotionLogsForUsers($users->keys());

		$done = 0;

		foreach ($users AS $user)
		{
			$this->data['start'] = $user->user_id;

			$promotionRepo->updatePromotionsForUser(
				$user,
				isset($userGroupPromotionLogs[$user->user_id]) ? $userGroupPromotionLogs[$user->user_id] : [],
				$promotions
			);

			$done++;

			if (microtime(true) - $startTime >= $maxRunTime)
			{
				break;
			}
		}

		$this->data['batch'] = $this->calculateOptimalBatch($this->data['batch'], $done, $startTime, $maxRunTime, 1000);

		return $this->resume();
	}

	public function getStatusMessage()
	{
		$actionPhrase = \XF::phrase('rebuilding');
		$typePhrase = \XF::phrase('user_group_promotions');
		return sprintf('%s... %s (%s)', $actionPhrase, $typePhrase, $this->data['start']);
	}

	public function canCancel()
	{
		return true;
	}

	public function canTriggerByChoice()
	{
		return true;
	}
}