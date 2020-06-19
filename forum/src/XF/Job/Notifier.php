<?php

namespace XF\Job;

class Notifier extends AbstractJob
{
	protected $defaultData = [
		'service' => '',
		'extra' => [],
		'notifyData' => [],
		'alerted' => [],
		'emailed' => []
	];

	public function run($maxRunTime)
	{
		$service = $this->data['service'];
		$call = [$service, 'createForJob'];

		if (!class_exists($service) || !is_callable($call))
		{
			return $this->complete();
		}

		/** @var \XF\Service\AbstractNotifier|null $notifier */
		$notifier = call_user_func($call, $this->data['extra']);
		if (!$notifier)
		{
			return $this->complete();
		}

		$notifier->setupFromJobData($this->data);
		$notifier->notify($maxRunTime);
		if (!$notifier->hasMore())
		{
			return $this->complete();
		}

		$this->data = $notifier->getJobData();
		return $this->resume();
	}

	public function getStatusMessage()
	{
		$actionPhrase = \XF::phrase('running');
		$typePhrase = 'Notifications'; // never seen
		return sprintf('%s... %s', $actionPhrase, $typePhrase);
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