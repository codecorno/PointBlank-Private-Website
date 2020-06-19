<?php

namespace XF\Job;

class Trophy extends AbstractJob
{
	protected $defaultData = [
		'steps' => 0,
		'start' => 0,
		'batch' => 100
	];

	public function run($maxRunTime)
	{
		/** @var \XF\Repository\Trophy $trophyRepo */
		$trophyRepo = $this->app->repository('XF:Trophy');

		$trophies = $trophyRepo->findTrophiesForList()->fetch();
		if (!$trophies || !$this->app->options()->enableTrophies)
		{
			$this->complete();
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

		$userTrophies = $trophyRepo->findUsersTrophies($users->keys())->fetch()->groupBy('user_id');

		$done = 0;

		foreach ($users AS $user)
		{
			$this->data['start'] = $user->user_id;

			$trophyRepo->updateTrophiesForUser(
				$user,
				isset($userTrophies[$user->user_id]) ? $userTrophies[$user->user_id] : [],
				$trophies
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
		$typePhrase = \XF::phrase('trophies');
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