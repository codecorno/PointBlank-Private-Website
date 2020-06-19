<?php

namespace XF\Import;

use Symfony\Component\Process\PhpExecutableFinder;
use Symfony\Component\Process\Process;
use XF\Import\Importer\AbstractImporter;

class ParallelRunner extends Runner
{
	protected $maxProcesses = 1;

	protected $phpPath;

	protected function runUntilCompleteInternal(Manager $manager, \Closure $onTick)
	{
		$session = $this->session;
		$initialStep = $session->currentStep;

		$this->initializeRun();

		if ($session->runComplete)
		{
			$manager->updateCurrentSession($this->session);
			return self::STATE_COMPLETE;
		}

		$currentStep = $session->currentStep;
		$currentState = $session->currentState;

		if ($currentState->complete)
		{
			$this->updateSessionFromStepState($currentState);
			return self::STATE_INCOMPLETE; // the step is complete, but the run isn't yet
		}

		if (!$currentState->end)
		{
			// step doesn't support parallel processing so run as normal
			return parent::runUntilCompleteInternal($manager, $onTick);
		}

		if ($currentStep != $initialStep)
		{
			// we changed step and we're doing a parallel run, so let's update the session so that child processes see this
			$manager->updateCurrentSession($session);
		}

		$onSessionChange = function() use ($onTick)
		{
			$this->triggerOnTick($onTick);
		};

		$processManager = new ParallelProcessManager($this->phpPath, $this->maxProcesses, $this);
		$newStepState = $processManager->execute($manager, $onSessionChange);

		if (!$newStepState)
		{
			throw new \LogicException("Process manager execute did not return a step state");
		}

		$this->lastRunStep = $session->currentStep;
		$this->lastRunState = $newStepState;

		$this->updateSessionFromStepState($newStepState);
		$manager->updateCurrentSession($this->session);

		$this->triggerOnTick($onTick, $this->lastRunStep, $this->lastRunState);

		return self::STATE_INCOMPLETE;
	}

	public function getMaxProcesses()
	{
		return $this->maxProcesses;
	}

	public function encodeChildMessage($messageType, $data)
	{
		$encoded = '[XF:' . $messageType . ']' . base64_encode(json_encode($data));

		if (json_last_error())
		{
			throw new \RuntimeException("Could not encode message data: " . json_last_error_msg());
		}

		return $encoded;
	}

	public function decodeChildMessage($message)
	{
		if (!preg_match('#^\[XF:([a-zA-Z0-9_-]+)\](.*)$#s', trim($message), $messageMatch))
		{
			return null;
		}

		$messageType = $messageMatch[1];
		$messageData = json_decode(base64_decode($messageMatch[2]), true);

		if (json_last_error())
		{
			throw new \RuntimeException("Received non-decodable value: " . json_last_error_msg());
		}

		return [$messageType, $messageData];
	}

	public function handleProcessMessage($messageType, $messageData)
	{
		switch ($messageType)
		{
			case 'CHANGES':
				if (!is_array($messageData))
				{
					throw new \InvalidArgumentException("Change messages must have data in array format");
				}
				return $this->handleChangeMessage($messageData);

			default:
				throw new \InvalidArgumentException("Unknown message type '$messageType'");
		}
	}

	protected function handleChangeMessage(array $messageData)
	{
		$session = $this->session;
		$stepState = $session->currentState;

		$sessionUpdateRequired = false;

		if (isset($messageData['imported']))
		{
			$stepState->imported += intval($messageData['imported']);
			$sessionUpdateRequired = true;
		}

		if (isset($messageData['sessionExtra']) && is_array($messageData['sessionExtra']))
		{
			$session->extra = \XF\Util\Arr::mapMerge($session->extra, $messageData['sessionExtra']);
			$sessionUpdateRequired = true;
		}

		if (isset($messageData['sessionNotes']) && is_array($messageData['sessionNotes']))
		{
			$session->notes = \XF\Util\Arr::mapMerge($session->notes, $messageData['sessionNotes']);
			$sessionUpdateRequired = true;
		}

		return $sessionUpdateRequired;
	}

	public function validateChildProcessRun($step, $startAfter, $end, &$error = null)
	{
		$session = $this->session;

		if ($session->currentStep !== $step)
		{
			$error = "Tried to run step '$step', expected '$session->currentStep'";
			return false;
		}

		$currentState = $session->currentState;
		if (!$currentState)
		{
			$error = "No step state.";
			return false;
		}

		if ($end <= $startAfter)
		{
			$error = "Got range of $startAfter to $end. (end before the start)";
			return false;
		}

		if ($startAfter < $currentState->startAfter)
		{
			$error = "startAfter less than the step state startAfter value.";
			return false;
		}

		if (!$currentState->end)
		{
			$error = "No end set in current state. Not runnable as a child process.";
			return false;
		}

		if ($end > $currentState->end)
		{
			$error = "End less than the step state end value.";
			return false;
		}

		return true;
	}

	public function runChildProcess($step, $startAfter, $end, \Closure $messageReceiver)
	{
		if (!$this->validateChildProcessRun($step, $startAfter, $end, $validateError))
		{
			throw new \LogicException($validateError);
		}

		$stepState = $this->session->currentState;
		$stepState->startAfter = $startAfter;
		$stepState->end = $end;

		while ($stepState->startAfter < $stepState->end && !$stepState->complete)
		{
			$lastSession = clone $this->session;
			$lastImportedCount = $stepState->imported;

			$stepState = $this->runStep($step, $stepState, 8);
			$session = $this->session;

			$changes = [];

			// note: only the imported count is brought over from the step state -- extra there is considered "local"
			$importedDifference = $stepState->imported - $lastImportedCount;
			if ($importedDifference > 0)
			{
				$changes['imported'] = $importedDifference;
			}

			$sessionExtraDiff = \XF\Util\Arr::mapDiff($session->extra, $lastSession->extra);
			if ($sessionExtraDiff)
			{
				$changes['sessionExtra'] = $sessionExtraDiff;
			}

			$sessionNotesDiff = \XF\Util\Arr::mapDiff($session->notes, $lastSession->notes);
			if ($sessionNotesDiff)
			{
				$changes['sessionNotes'] = $sessionNotesDiff;
			}

			if ($changes)
			{
				$messageReceiver($this->encodeChildMessage('CHANGES', $changes));
			}

			if (function_exists('pcntl_signal_dispatch'))
			{
				// Dispatch any registered signal handlers for pending signals
				pcntl_signal_dispatch();
			}
		}
	}

	public static function instantiate(AbstractImporter $importer, Session $session, array $options)
	{
		$options = array_replace([
			'processes' => 1,
			'phpPath' => null
		], $options);

		$processes = max(1, intval($options['processes']));

		if (!$options['phpPath'])
		{
			$phpExecFinder = new PhpExecutableFinder();
			$options['phpPath'] = $phpExecFinder->find(false);
		}

		$runner = new static($importer, $session);
		$runner->maxProcesses = $processes;
		$runner->phpPath = $options['phpPath'];

		return $runner;
	}
}