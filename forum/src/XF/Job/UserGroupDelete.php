<?php

namespace XF\Job;

class UserGroupDelete extends AbstractJob
{
	protected $defaultData = [
		'user_group_id' => null,
		'count' => 0,
		'position' => 0
	];

	public function run($maxRunTime)
	{
		$start = microtime(true);

		$db = $this->app->db();
		$userGroupId = $this->data['user_group_id'];

		$userIds = $db->fetchAllColumn('
			SELECT user_id
			FROM xf_user_group_relation
			WHERE user_group_id = ?
				AND user_id > ?
			ORDER BY user_id
			LIMIT 1000
		', [$userGroupId, $this->data['position']]);
		if (!$userIds)
		{
			$this->finalActions();

			return $this->complete();
		}

		$loopFinished = true;

		foreach ($userIds AS $userId)
		{
			$this->data['count']++;
			$this->data['position'] = $userId;

			/** @var \XF\Entity\User $user */
			$user = $this->app->find('XF:User', $userId);
			if ($user && $user->removeUserFromGroup($userGroupId))
			{
				$user->save();
			}

			if (microtime(true) - $start >= $maxRunTime)
			{
				$loopFinished = false;
				break;
			}
		}

		if ($loopFinished)
		{
			if (!$db->fetchOne(
				'SELECT 1 FROM xf_user_group_relation WHERE user_group_id = ? AND user_id > ? LIMIT 1',
				[$userGroupId, $this->data['position']]
			))
			{
				$this->finalActions();

				return $this->complete();
			}
		}

		return $this->resume();
	}

	protected function finalActions()
	{
		// there will likely be permission combinations involving this group, so clean them up
		/** @var \XF\Repository\PermissionCombination $combinationRepo */
		$combinationRepo = $this->app->repository('XF:PermissionCombination');
		$combinationRepo->deleteUnusedPermissionCombinations();
	}

	public function getStatusMessage()
	{
		$actionPhrase = \XF::phrase('deleting');
		$typePhrase = \XF::phrase('user_group');
		return sprintf('%s... %s (%s)', $actionPhrase, $typePhrase,
			\XF::language()->numberFormat($this->data['count'])
		);
	}

	public function canCancel()
	{
		return true;
	}

	public function canTriggerByChoice()
	{
		return false;
	}
}