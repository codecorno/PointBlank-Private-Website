<?php

namespace XF\Job;

class PermissionRebuild extends AbstractJob
{
	protected $defaultData = [
		'steps' => 0,
		'combinationId' => 0,
		'cleaned' => false
	];

	public function run($maxRunTime)
	{
		$start = microtime(true);

		if (!$this->data['cleaned'])
		{
			/** @var \XF\Repository\PermissionCombination $combinationRepo */
			$combinationRepo = $this->app->repository('XF:PermissionCombination');
			$combinationRepo->deleteUnusedPermissionCombinations();

			$this->data['cleaned'] = true;
		}

		$this->data['steps']++;

		$db = $this->app->db();
		$em = $this->app->em();
		$app = \XF::app();
		$combinationIds = $db->fetchAllColumn("
			SELECT permission_combination_id
			FROM xf_permission_combination
			WHERE permission_combination_id > ?
			ORDER BY permission_combination_id
		", $this->data['combinationId']);
		if (!$combinationIds)
		{
			// there are situations where we run this job but not with this unique key, so this is unnecessary
			$this->app->jobManager()->cancelUniqueJob('permissionRebuild');

			return $this->complete();
		}

		$permissionBuilder = $app->permissionBuilder();

		foreach ($combinationIds AS $combinationId)
		{
			$this->data['combinationId'] = $combinationId;

			/** @var \XF\Entity\PermissionCombination $combination */
			$combination = $em->find('XF:PermissionCombination', $combinationId);
			if (!$combination)
			{
				continue;
			}

			$permissionBuilder->rebuildCombination($combination);

			if (microtime(true) - $start >= $maxRunTime)
			{
				break;
			}
		}

		return $this->resume();
	}

	public function getStatusMessage()
	{
		$actionPhrase = \XF::phrase('rebuilding');
		$typePhrase = \XF::phrase('permissions');
		return sprintf('%s... %s %s', $actionPhrase, $typePhrase, str_repeat('. ', $this->data['steps']));
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