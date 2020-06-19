<?php

namespace XF\Job;

class Manager
{
	/**
	 * @var \XF\App
	 */
	protected $app;

	/**
	 * @var \XF\Db\AbstractAdapter
	 */
	protected $db;

	protected $allowManual;

	protected $forceManual = false;

	protected $uniqueEnqueued = [];

	protected $autoEnqueuedList = [];
	protected $autoBlockingList = [];
	protected $manualEnqueuedList = [];

	protected $autoBlockingMessage = null;

	protected $shutdownRegistered = false;
	protected $runningJob;

	public function __construct(\XF\App $app, $allowManual = true, $forceManual = false)
	{
		$this->app = $app;
		$this->db = $app->db();
		$this->allowManual = $allowManual || $forceManual;
		$this->forceManual = $forceManual;
	}

	public function setAllowManual($allowManual)
	{
		$this->allowManual = $allowManual;
	}

	public function setForceManual($forceManual)
	{
		$this->forceManual = $forceManual;
		if ($forceManual)
		{
			$this->setAllowManual(true);
		}
	}

	/**
	 * @param bool $manual
	 * @param float $maxRunTime
	 *
	 * @return null|JobResult
	 */
	public function runQueue($manual, $maxRunTime)
	{
		if ($maxRunTime < 2)
		{
			$maxRunTime = 2;
		}

		$runnable = $this->getRunnable($manual);
		$startTime = microtime(true);
		$result = null;

		foreach ($runnable AS $job)
		{
			$remaining = $maxRunTime - (microtime(true) - $startTime);
			if ($remaining < 1)
			{
				break;
			}

			$result = $this->runJobEntry($job, $remaining);
		}

		return $result;
	}

	/**
	 * @param array $ids
	 * @param $maxRunTime
	 * @return array
	 */
	public function runByIds(array $ids, $maxRunTime)
	{
		if ($maxRunTime < 2)
		{
			$maxRunTime = 2;
		}

		$startTime = microtime(true);
		$result = null;

		foreach ($ids AS $k => $id)
		{
			$remaining = $maxRunTime - (microtime(true) - $startTime);
			if ($remaining < 1)
			{
				break;
			}

			$job = $this->getJob($id);
			if ($job && $job['trigger_date'] <= time())
			{
				$result = $this->runJobEntry($job, $remaining);
			}
			else
			{
				$result = null;
			}

			if (!$result || $result->completed)
			{
				unset($ids[$k]);
			}
		}

		return [
			'result' => $result,
			'remaining' => $ids
		];
	}

	/**
	 * @param string $key
	 * @param float $maxRunTime
	 *
	 * @return null|JobResult
	 */
	public function runUnique($key, $maxRunTime)
	{
		if ($maxRunTime < 2)
		{
			$maxRunTime = 2;
		}

		$job = $this->getUniqueJob($key);
		if ($job)
		{
			return $this->runJobEntry($job, $maxRunTime);
		}
		else
		{
			return null;
		}
	}

	public function runById($id, $maxRunTime)
	{
		if ($maxRunTime < 2)
		{
			$maxRunTime = 2;
		}

		$job = $this->getJob($id);
		if ($job)
		{
			return $this->runJobEntry($job, $maxRunTime);
		}
		else
		{
			return null;
		}
	}

	public function queuePending($manual)
	{
		return count($this->getRunnable($manual)) > 0;
	}

	/**
	 * @param array $job
	 * @param int $maxRunTime
	 *
	 * @return JobResult
	 */
	public function runJobEntry(array $job, $maxRunTime)
	{
		$affected = $this->db->update('xf_job', [
			'trigger_date' => time() + 15*60,
			'last_run_date' => time()
		], 'job_id = ? AND trigger_date = ?', [$job['job_id'], $job['trigger_date']]);
		if (!$affected)
		{
			// job has already been taken, treat it as complete
			return new JobResult(true, $job['job_id']);
		}

		$result = $this->runJobInternal($job, $maxRunTime);
		if ($result->completed)
		{
			$this->db->delete('xf_job', 'job_id = ?', $job['job_id']);

			unset(
				$this->manualEnqueuedList[$job['job_id']],
				$this->autoEnqueuedList[$job['job_id']],
				$this->autoBlockingList[$job['job_id']]
			);

			if ($job['unique_key'])
			{
				unset($this->uniqueEnqueued[$job['unique_key']]);
			}
		}
		else
		{
			$update = [
				'execute_data' => serialize($result->data),
				'trigger_date' => $result->continueDate ? intval($result->continueDate) : $job['trigger_date'],
				'last_run_date' => time()
			];
			$this->db->update('xf_job', $update, 'job_id = ?', $job['job_id']);
		}

		if (!$job['manual_execute'])
		{
			$this->scheduleRunTimeUpdate();
		}

		return $result;
	}

	public function getJobRunner(array $job)
	{
		return $this->app->job($job['execute_class'], $job['job_id'], unserialize($job['execute_data']));
	}

	protected function runJobInternal(array $job, $maxRunTime)
	{
		$runner = $this->getJobRunner($job);
		if (!$runner)
		{
			$this->app->logException(new \Exception("Could not get runner for job $job[execute_class] (unique: $job[unique_key]). Skipping."));

			return new JobResult(true, $job['job_id']);
		}

		if (!$this->shutdownRegistered)
		{
			register_shutdown_function([$this, 'handleShutdown']);
		}

		$this->runningJob = $job;

		try
		{
			$result = $runner->run($maxRunTime);
			$this->runningJob = null;
		}
		catch (\Exception $e)
		{
			$this->runningJob = null;

			$this->db->rollbackAll();

			if ($job['manual_execute'] || $this->app->config()['development']['throwJobErrors'])
			{
				$this->db->update('xf_job', [
					'trigger_date' => $job['trigger_date'],
					'last_run_date' => time()
				], 'job_id = ?', $job['job_id']);

				throw $e;
			}
			else
			{
				$this->app->logException($e, false, "Job $job[execute_class]: ");
				$result = new JobResult(true, $job['job_id'], [], "$job[execute_class] threw exception. See error log.");
			}
		}

		if (!($result instanceof \XF\Job\JobResult))
		{
			throw new \LogicException("Jobs must return JobResult objects");
		}

		\XF::triggerRunOnce();

		return $result;
	}

	public function handleShutdown()
	{
		if (!$this->runningJob)
		{
			return;
		}

		$job = $this->runningJob;

		try
		{
			// job is being run manually, there's no error which implies a call to exit, or forced re-enqueue
			if ($job['manual_execute'] || !error_get_last() || $this->app->config()['development']['throwJobErrors'])
			{
				$this->db->rollbackAll();

				$this->db->update('xf_job', [
					'trigger_date' => $job['trigger_date'],
					'last_run_date' => time()
				], 'job_id = ?', $job['job_id']);

				$this->updateNextRunTime();
			}
		}
		catch (\Exception $e) {}
	}

	public function cancelJob(array $job)
	{
		$rows = $this->db->delete('xf_job', 'job_id = ?', $job['job_id']);
		if ($rows)
		{
			$this->scheduleRunTimeUpdate();
		}
	}

	public function cancelUniqueJob($uniqueId)
	{
		$job = $this->getUniqueJob($uniqueId);
		if ($job)
		{
			$this->cancelJob($job);
			return true;
		}
		else
		{
			return false;
		}
	}

	public function getRunnable($manual)
	{
		return $this->db->fetchAll("
			SELECT *
			FROM xf_job
			WHERE trigger_date <= ?
				AND manual_execute = ?
			ORDER BY trigger_date
			LIMIT 1000
		", [\XF::$time, $manual ? 1 : 0]);
	}

	public function getFirstRunnable($manual)
	{
		return $this->db->fetchRow("
			SELECT *
			FROM xf_job
			WHERE trigger_date <= ?
				AND manual_execute = ?
			ORDER BY trigger_date
			LIMIT 1
		", [\XF::$time, $manual ? 1 : 0]);
	}

	public function hasStoppedManualJobs()
	{
		$match = $this->db->fetchRow("
			SELECT job_id
			FROM xf_job
			WHERE trigger_date <= ?
				AND (last_run_date <= ? OR last_run_date IS NULL)
				AND manual_execute = 1
			LIMIT 1
		", [\XF::$time - 15, \XF::$time - 180]);

		return $match ? true : false;
	}

	public function getJob($id)
	{
		return $this->db->fetchRow("
			SELECT *
			FROM xf_job
			WHERE job_id = ?
		", $id);
	}

	public function getUniqueJob($key)
	{
		return $this->db->fetchRow("
			SELECT *
			FROM xf_job
			WHERE unique_key = ?
		", $key);
	}

	public function getFirstAutomaticTime()
	{
		return $this->db->fetchOne("
			SELECT MIN(trigger_date)
			FROM xf_job
			WHERE manual_execute = 0
		");
	}

	public function updateNextRunTime()
	{
		$runTime = $this->getFirstAutomaticTime();
		$this->app->registry()->set('autoJobRun', $runTime);

		return $runTime;
	}

	public function setNextAutoRunTime($time)
	{
		$this->app->registry()->set('autoJobRun', $time);
	}

	public function scheduleRunTimeUpdate()
	{
		\XF::runOnce('autoJobRun', function()
		{
			$this->updateNextRunTime();
		});
	}

	public function enqueue($jobClass, array $params = [], $manual = false)
	{
		return $this->_enqueue(null, $jobClass, $params, $manual, null);
	}

	public function enqueueAutoBlocking($jobClass, array $params = [])
	{
		return $this->_enqueue(null, $jobClass, $params, false, null, true);
	}

	public function enqueueUnique($uniqueId, $jobClass, array $params = [], $manual = true)
	{
		return $this->_enqueue($uniqueId, $jobClass, $params, $manual, null);
	}

	public function enqueueLater($uniqueId, $runTime, $jobClass, array $params = [], $manual = false)
	{
		return $this->_enqueue($uniqueId, $jobClass, $params, $manual, $runTime);
	}

	/**
	 * @param string|null $uniqueId
	 * @param string $jobClass
	 * @param array $params
	 * @param bool $manual
	 * @param int|null $runTime
	 * @param bool $blocking If auto, this job can be set as blocking which will change the UI for the triggerer
	 *
	 * @return int|null ID of the enqueued job (or null if an error happened)
	 */
	protected function _enqueue($uniqueId, $jobClass, array $params, $manual, $runTime, $blocking = false)
	{
		if ($uniqueId)
		{
			if (strlen($uniqueId) > 50)
			{
				$uniqueId = md5($uniqueId);
			}

			if (isset($this->uniqueEnqueued[$uniqueId]))
			{
				return $this->uniqueEnqueued[$uniqueId];
			}
		}
		else
		{
			$uniqueId = null;
		}

		if ($this->forceManual)
		{
			$manual = true;
		}
		else if (!$this->allowManual)
		{
			$manual = false;
		}

		if (!$runTime)
		{
			$runTime = \XF::$time;
		}

		$db = $this->db;
		$affected = $db->insert('xf_job', [
			'execute_class' => $jobClass,
			'execute_data' => serialize($params),
			'unique_key' => $uniqueId,
			'manual_execute' => $manual ? 1 : 0,
			'trigger_date' => $runTime
		], false, '
			execute_class = VALUES(execute_class),
			execute_data = VALUES(execute_data),
			manual_execute = VALUES(manual_execute),
			trigger_date = VALUES(trigger_date),
			last_run_date = NULL
		');

		if ($affected == 1)
		{
			$id = $db->lastInsertId();
		}
		else
		{
			// this is an update
			$id = $db->fetchOne("
				SELECT job_id
				FROM xf_job
				WHERE unique_key = ?
			", $uniqueId);
			if (!$id)
			{
				return null;
			}
		}

		if ($uniqueId)
		{
			$this->uniqueEnqueued[$uniqueId] = $id;
		}

		if ($manual)
		{
			$this->manualEnqueuedList[$id] = $id;
		}
		else
		{
			if ($blocking)
			{
				$this->autoBlockingList[$id] = $id;
			}
			$this->autoEnqueuedList[$id] = $id;

			$this->scheduleRunTimeUpdate();
		}

		return $id;
	}

	public function hasManualEnqueued()
	{
		return count($this->manualEnqueuedList) > 0;
	}

	public function getManualEnqueued()
	{
		return $this->manualEnqueuedList;
	}

	public function hasAutoEnqueued()
	{
		return count($this->autoEnqueuedList) > 0;
	}

	public function getAutoEnqueued()
	{
		return $this->autoEnqueuedList;
	}

	public function hasAutoBlocking()
	{
		return count($this->autoBlockingList) > 0;
	}

	public function getAutoBlocking()
	{
		return $this->autoBlockingList;
	}

	public function setAutoBlockingMessage($message)
	{
		$this->autoBlockingMessage = $message;
	}

	public function getAutoBlockingMessage()
	{
		return $this->autoBlockingMessage;
	}
}