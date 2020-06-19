<?php

namespace XF\Job;

class UserRenameCleanUp extends AbstractJob
{
	protected $defaultData = [
		'originalUserId' => null,
		'originalUserName' => null,
		'newUserName' => null,

		'currentStep' => 0,
		'lastOffset' => null,

		'start' => 0
	];

	public function run($maxRunTime)
	{
		$this->data['start']++;

		if (!$this->data['originalUserId'] || !$this->data['originalUserName'])
		{
			return $this->complete();
		}

		/** @var \XF\Service\User\ContentChange $contentChanger */
		$contentChanger = $this->app->service(
			'XF:User\ContentChange', $this->data['originalUserId'], $this->data['originalUserName']
		);
		$contentChanger->setupForNameChange($this->data['newUserName']);
		$contentChanger->restoreState($this->data['currentStep'], $this->data['lastOffset']);

		$result = $contentChanger->apply($maxRunTime);
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
		$actionPhrase = \XF::phrase('rebuilding');
		$typePhrase = \XF::phrase('user_names');
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