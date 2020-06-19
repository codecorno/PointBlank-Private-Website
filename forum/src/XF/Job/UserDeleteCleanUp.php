<?php

namespace XF\Job;

class UserDeleteCleanUp extends AbstractJob
{
	protected $defaultData = [
		'userId' => null,
		'username' => null,

		'currentStep' => 0,
		'lastOffset' => null,

		'start' => 0
	];

	public function run($maxRunTime)
	{
		$this->data['start']++;

		if (!$this->data['userId'] || !$this->data['username'])
		{
			return $this->complete();
		}

		/** @var \XF\Service\User\DeleteCleanUp $deleter */
		$deleter = $this->app->service(
			'XF:User\DeleteCleanUp', $this->data['userId'], $this->data['username']
		);
		$deleter->restoreState($this->data['currentStep'], $this->data['lastOffset']);

		$result = $deleter->cleanUp($maxRunTime);
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
		$actionPhrase = \XF::phrase('deleting');
		$typePhrase = $this->data['username'];
		return sprintf('%s... %s (%s)', $actionPhrase, $typePhrase, $this->data['start']);
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