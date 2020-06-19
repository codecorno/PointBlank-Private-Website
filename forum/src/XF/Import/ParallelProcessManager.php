<?php

namespace XF\Import;

use Symfony\Component\Process\Process;

class ParallelProcessManager
{
	const DEFAULT_MAX_PER_PROCESS = 3000;

	/**
	 * @var string
	 */
	protected $phpPath;

	/**
	 * @var int
	 */
	protected $maxProcesses = 1;

	/**
	 * @var ParallelRunner
	 */
	protected $runner;

	/**
	 * @var Session
	 */
	protected $session;

	protected $executeProcessCount;
	protected $executePerProcess;
	protected $lastProcessEnd;

	protected $processes = [];
	protected $finishedRanges = [];

	protected $sessionUpdatePending;

	public function __construct($phpPath, $maxProcesses, ParallelRunner $runner)
	{
		if (!$phpPath)
		{
			throw new \LogicException("No PHP path provided");
		}

		$this->phpPath = $phpPath;
		$this->maxProcesses = max(1, intval($maxProcesses));
		$this->runner = $runner;
		$this->session = $runner->getSession();
	}

	/**
	 * @param Manager $manager
	 * @param \Closure $onSessionChange Called when the session has been updated
	 * @param \Closure|null $onError Called when an error has occurred
	 *
	 * @return StepState
	 */
	public function execute(Manager $manager, \Closure $onSessionChange, \Closure $onError = null)
	{
		$session = $this->session;
		$stepState = $session->currentState;

		if (!$session->currentStep || !$stepState)
		{
			throw new \LogicException("Need step and state to run");
		}

		$totalRemaining = $stepState->end - $stepState->startAfter;
		if ($totalRemaining < 1)
		{
			return $stepState->complete();
		}

		$processCount = $this->maxProcesses;
		$maxPerProcess = self::DEFAULT_MAX_PER_PROCESS;

		// If we have 100,000 remaining and 4 processes, we would allocate 25,000 to each process. However,
		// we want don't want each process to have that much in one go, so cap this to a particular range.
		$perProcess = min(floor($totalRemaining / $processCount), $maxPerProcess);

		if ($perProcess < (.1 * $maxPerProcess))
		{
			// not enough to bother parallelizing
			$processCount = 1;
			$perProcess = $totalRemaining;
		}

		$this->reinit($processCount, $perProcess);

		for ($i = 0; $i < $this->executeProcessCount; $i++)
		{
			$this->startProcessIfNeeded();
			usleep(mt_rand(0, 300000)); // sleep between 0 and 0.3 seconds to stagger initial processes slightly
		}

		$hasError = false;
		$errorDetails = null;

		while ($this->processes)
		{
			foreach ($this->processes AS $pid => &$processRecord)
			{
				/** @var Process $process */
				$process = $processRecord['process'];

				$output = $process->getIncrementalOutput();
				if (strlen($output))
				{
					$this->onProcessOutput($output, $pid, $processRecord);
				}

				if (!$process->isRunning())
				{
					if ($process->isSuccessful())
					{
						$this->onProcessFinish($pid, $processRecord);
					}
					else
					{
						$hasError = true;

						// TODO: this assumes an exception -- other errors will hide details
						$errorDetails = "Child process exited with code " . $process->getExitCode()
							. ". See control panel error error logs for details.";
					}
				}
			}

			if ($this->sessionUpdatePending)
			{
				$this->sessionUpdatePending = false;
				$manager->updateCurrentSession($session);
				$onSessionChange($session);
			}

			if ($hasError)
			{
				break;
			}

			usleep(500000);

			if (function_exists('pcntl_signal_dispatch'))
			{
				// Dispatch any registered signal handlers for pending signals
				pcntl_signal_dispatch();
			}
		}

		if ($hasError)
		{
			foreach ($this->processes AS $processRecord)
			{
				/** @var Process $process */
				$process = $processRecord['process'];

				if ($process->isRunning())
				{
					$process->stop();
				}
			}

			if ($onError)
			{
				$onError($errorDetails, $this->processes);
			}

			throw new \RuntimeException($errorDetails);
		}

		if ($stepState->startAfter < $stepState->end)
		{
			throw new \RuntimeException(
				"Step did not complete as expected: startAfter={$stepState->startAfter}, end={$stepState->end}"
			);
		}

		return $stepState->resumeIfNeeded();
	}

	protected function reinit($processCount, $perProcess)
	{
		$this->executeProcessCount = $processCount;
		$this->executePerProcess = $perProcess;
		$this->processes = [];
		$this->lastProcessEnd = $this->session->currentState->startAfter;
		$this->finishedRanges = [];
		$this->sessionUpdatePending = false;
	}

	protected function onProcessOutput($output, $pid, array &$processRecord)
	{
		if (isset($processRecord['pendingOutput']))
		{
			$output = $processRecord['pendingOutput'] . $output;
			unset($processRecord['pendingOutput']);
		}

		while (preg_match('#^[^\n]*\n#s', $output, $match))
		{
			$message = $match[0];
			$output = substr($output, strlen($message));

			$decoded = $this->runner->decodeChildMessage($message);
			if ($decoded)
			{
				$sessionUpdateRequired = $this->runner->handleProcessMessage($decoded[0], $decoded[1]);
				if ($sessionUpdateRequired)
				{
					$this->sessionUpdatePending = true;
				}
			}
			else
			{
				$processRecord['unhandledOutput'] .= $message;
			}
		}

		if (strlen($output))
		{
			// we don't have a trailing line break so we don't know if we've read the whole thing
			$processRecord['pendingOutput'] = $output;
		}
	}

	protected function onProcessFinish($pid, array $processRecord)
	{
		if (!isset($this->processes[$pid]))
		{
			return;
		}

		unset($this->processes[$pid]);

		$this->finishedRanges[$processRecord['startAfter']] = $processRecord['end'];

		$this->updateStepStart();
		$this->startProcessIfNeeded();
	}

	protected function updateStepStart()
	{
		$stepState = $this->session->currentState;
		$startAfter = $originalStartAfter = $stepState->startAfter;

		// Each range is keyed by the start after with the value being the end. As long as we find
		// entries in this array from the current start after value, then we know we have imported everything
		// up until that point.
		while (isset($this->finishedRanges[$startAfter]))
		{
			$startAfter = $this->finishedRanges[$startAfter];
		}

		if ($startAfter != $originalStartAfter)
		{
			$stepState->startAfter = $startAfter;
			$this->sessionUpdatePending = true;
		}
	}

	protected function startProcessIfNeeded(&$process = null)
	{
		$stepEnd = $this->session->currentState->end;

		if ($this->lastProcessEnd >= $stepEnd)
		{
			return null;
		}

		$startAfter = $this->lastProcessEnd;
		$end = min($startAfter + $this->executePerProcess, $stepEnd);

		$this->lastProcessEnd = $end;

		$cmd = [
			$this->phpPath,
			\XF::getRootDirectory() . \XF::$DS .'cmd.php',
			'xf:import-child-process',
			$this->session->currentStep,
			$startAfter,
			$end
		];

		$process = new Process($cmd);
		$process->setTimeout(null);
		$process->start();

		$pid = $process->getPid() ?: microtime(true);

		$this->processes[$pid] = [
			'process' => $process,
			'pid' => $pid,
			'startAfter' => $startAfter,
			'end' => $end,
			'unhandledOutput' => ''
		];

		return $pid;
	}
}