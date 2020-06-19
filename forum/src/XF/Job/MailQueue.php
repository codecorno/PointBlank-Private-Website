<?php

namespace XF\Job;

class MailQueue extends AbstractJob
{
	public function run($maxRunTime)
	{
		if ($queue = $this->app->mailQueue())
		{
			$queue->preventJobEnqueue(true);
			$queue->run($maxRunTime);
			$queue->preventJobEnqueue(false);

			if ($queue->hasMore($nextSendDate))
			{
				$resume = $this->resume();
				$resume->continueDate = $nextSendDate > 0 ? $nextSendDate : \XF::$time;

				return $resume;
			}
			else
			{
				return $this->complete();
			}
		}
		else
		{
			return $this->complete();
		}
	}

	public function getStatusMessage()
	{
		return '';
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