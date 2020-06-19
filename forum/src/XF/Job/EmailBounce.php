<?php

namespace XF\Job;

class EmailBounce extends AbstractJob
{
	protected $defaultData = [
		'start' => null
	];

	public function run($maxRunTime)
	{
		if (!$this->data['start'])
		{
			$this->data['start'] = time();
		}

		$bounceContainer = \XF::app()->bounce();

		$storage = $bounceContainer->storage();
		if (!$storage)
		{
			return $this->complete();
		}

		$processor = $bounceContainer->processor();
		$finished = $processor->processFromStorage($storage, $maxRunTime);

		$storage->close();

		if ($finished)
		{
			return $this->complete();
		}

		if (time() - $this->data['start'] > 60 * 30)
		{
			// don't let a single run of this run for more than 30 minutes
			return $this->complete();
		}

		return $this->resume();
	}

	public function getStatusMessage()
	{
		return \XF::phrase('processing_email_bounces...');
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