<?php

namespace XF\Job\Upgrade;

use XF\Job\AbstractJob;

class UserAvatar200 extends AbstractJob
{
	protected $defaultData = [
		'start' => 0,
		'batch' => 100,
		'max' => null
	];

	public function run($maxRunTime)
	{
		$startTime = microtime(true);

		$db = $this->app->db();
		$em = $this->app->em();

		$ids = $db->fetchAllColumn($db->limit(
			"
				SELECT user_id
				FROM xf_user
				WHERE user_id > ?
					AND avatar_date > 0
				ORDER BY user_id
			", $this->data['batch']
		), $this->data['start']);
		if (!$ids)
		{
			return $this->complete();
		}

		if ($this->data['max'] === null)
		{
			$this->data['max'] = $db->fetchOne("SELECT MAX(user_id) FROM xf_user");
		}

		// disable the memory limit to try to prevent issues
		\XF::setMemoryLimit(-1);

		$done = 0;

		foreach ($ids AS $id)
		{
			$this->data['start'] = $id;

			/** @var \XF\Entity\User $user */
			$user = $em->find('XF:User', $id, ['Profile']);
			if (!$user || !$user->avatar_date)
			{
				continue;
			}

			/** @var \XF\Service\User\Avatar $avatarService */
			$avatarService = $this->app->service('XF:User\Avatar', $user);
			$userProfile = $user->Profile;
			if ($userProfile)
			{
				$avatarService->setCrop($userProfile->avatar_crop_x, $userProfile->avatar_crop_y);
			}

			if (!$avatarService->createOSizeAvatarFromL())
			{
				$avatarService->deleteAvatar();
			}

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
		$typePhrase = 'User avatar sizes';
		return sprintf('%s... %s (%d / %d)', $actionPhrase, $typePhrase, $this->data['start'], $this->data['max']);
	}

	public function canCancel()
	{
		return false;
	}

	public function canTriggerByChoice()
	{
		return false;
	}
}