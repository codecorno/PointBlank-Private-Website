<?php

namespace XF\Job;

class UserMerge extends AbstractJob
{
	protected $defaultData = [
		'sourceUserId' => null,
		'targetUserId' => null,

		'currentStep' => 0,
		'lastOffset' => null,

		'start' => 0
	];

	public function run($maxRunTime)
	{
		$this->data['start']++;

		if (!$this->data['sourceUserId'] || !$this->data['targetUserId'])
		{
			return $this->complete();
		}

		$source = $this->app->em()->find('XF:User', $this->data['sourceUserId']);
		$target = $this->app->em()->find('XF:User', $this->data['targetUserId']);

		if (!$source || !$target)
		{
			return $this->complete();
		}

		/** @var \XF\Service\User\Merge $merger */
		$merger = $this->app->service('XF:User\Merge');
		$merger->setSource($source)->setTarget($target);
		$merger->restoreState($this->data['currentStep'], $this->data['lastOffset']);

		$result = $merger->merge($maxRunTime);
		if ($result->isCompleted())
		{
			return $this->complete();
		}
		else
		{
			$continueData = $result->getContinueData();
			$this->data['currentStep'] = $continueData['currentStep'];
			$this->data['lastOffset'] = $continueData['lastOffset'];

			return $this->resume();
		}
	}

	public function getStatusMessage()
	{
		$actionPhrase = \XF::phrase('merging_users');
		return sprintf('%s... (%s)', $actionPhrase, $this->data['start']);
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