<?php

namespace XF\Job;

class Atomic extends AbstractJob
{
	protected $defaultData = [
		'steps' => 0,
		'execute' => []
	];

	protected $statusMessage = '';

	public function run($maxRunTime)
	{
		$start = microtime(true);

		$this->data['steps']++;
		$remaining = $maxRunTime;

		do
		{
			$jobInfo = $this->getFirstJob();
			if (!$jobInfo)
			{
				return $this->complete();
			}

			/** @var AbstractJob $job */
			$job = $jobInfo['job'];
			if (!$job)
			{
				// broken job, skip
				$this->deleteAtomicJob($jobInfo['key']);
				continue;
			}

			$result = $job->run($remaining);
			if (!($result instanceof \XF\Job\JobResult))
			{
				throw new \LogicException("Jobs must return JobResult objects");
			}

			$this->statusMessage = $job->getStatusMessage();

			if ($result->completed)
			{
				// all finished
				$this->deleteAtomicJob($jobInfo['key']);
			}
			else
			{
				$this->updateAtomicJob($jobInfo['key'], $result->data);
			}

			\XF::triggerRunOnce();

			$remaining = $maxRunTime - (microtime(true) - $start);
			if ($remaining < 1)
			{
				break;
			}
		}
		while ($this->data['execute']);

		if (!$this->data['execute'])
		{
			return $this->complete();
		}

		return $this->resume();
	}

	protected function getFirstJob()
	{
		$value = reset($this->data['execute']);
		if (!$value)
		{
			return null;
		}

		$key = key($this->data['execute']);

		if (is_string($value))
		{
			$class = $value;
			$data = [];
		}
		else
		{
			$class = $value[0];
			$data = $value[1];
		}

		$job = $this->app->job($class, 0, $data);
		return [
			'key' => $key,
			'job' => $job
		];
	}

	protected function deleteAtomicJob($key)
	{
		unset($this->data['execute'][$key]);
	}

	protected function updateAtomicJob($key, array $data)
	{
		if (!isset($this->data['execute'][$key]))
		{
			return false;
		}

		$value = $this->data['execute'][$key];
		$class = is_string($value) ? $value : $value[0];
		$this->data['execute'][$key] = [$class, $data];

		return true;
	}

	public function getStatusMessage()
	{
		return $this->statusMessage;
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