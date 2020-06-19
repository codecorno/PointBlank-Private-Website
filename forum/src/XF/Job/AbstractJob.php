<?php

namespace XF\Job;

abstract class AbstractJob
{
	protected $app;
	protected $jobId;
	protected $data;
	protected $defaultData = [];

	/**
	 * @param int $maxRunTime
	 *
	 * @return JobResult
	 */
	abstract public function run($maxRunTime);
	abstract public function getStatusMessage();
	abstract public function canCancel();
	abstract public function canTriggerByChoice();

	public function __construct(\XF\App $app, $jobId, array $data = [])
	{
		@set_time_limit(0);

		$this->app = $app;
		$this->jobId = $jobId;
		$this->data = $this->setupData($data);
	}

	protected function setupData(array $data)
	{
		return array_merge($this->defaultData, $data);
	}

	protected function saveIncrementalData()
	{
		$this->app->db->update('xf_job', [
			'execute_data' => serialize($this->data)
		], 'job_id = ?', $this->jobId);
	}

	public function getData()
	{
		return $this->data;
	}

	public function getJobId()
	{
		return $this->jobId;
	}

	public function complete()
	{
		return new JobResult(true, $this->jobId);
	}

	public function resume()
	{
		return new JobResult(false, $this->jobId, $this->data,
			$this->getStatusMessage(), $this->canCancel()
		);
	}

	public function calculateOptimalBatch($expected, $done, $start, $maxTime, $maxBatch = null)
	{
		$spent = microtime(true) - $start;
		$remaining = $maxTime - $spent;

		$percentDone = $done / $expected;
		$percentSpent = $spent / $maxTime;
		$newExpected = floor($done / $percentSpent);

		if ($percentSpent > 1)
		{
			return max(1, $newExpected);
		}

		if ($remaining < 1 || $percentSpent >= .9)
		{
			// if 90% finished, keep grabbing that amount
			return max(1, ($percentDone >= .9 ? $expected : $done));
		}

		if ($maxBatch !== null)
		{
			$newExpected = min($maxBatch, $newExpected);
		}

		return max(1, $newExpected);
	}
}