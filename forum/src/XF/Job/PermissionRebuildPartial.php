<?php

namespace XF\Job;

class PermissionRebuildPartial extends AbstractJob
{
	protected $defaultData = [
		'steps' => 0,
		'combinationIds' => []
	];

	public function run($maxRunTime)
	{
		$start = microtime(true);

		$this->data['steps']++;

		$em = $this->app->em();
		$permissionBuilder = $this->app->permissionBuilder();

		foreach ($this->data['combinationIds'] AS $k => $combinationId)
		{
			unset($this->data['combinationIds'][$k]);

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

		if (!$this->data['combinationIds'])
		{
			return $this->complete();
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