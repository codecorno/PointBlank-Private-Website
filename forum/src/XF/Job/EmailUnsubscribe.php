<?php

namespace XF\Job;

class EmailUnsubscribe extends AbstractJob
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

		$unsubContainer = \XF::app()->unsubscribe();

		$storage = $unsubContainer->storage();
		if (!$storage)
		{
			return $this->complete();
		}

		$processor = $unsubContainer->processor();
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
		return \XF::phrase('processing_email_unsubscribe_requests...');
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