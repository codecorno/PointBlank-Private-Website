<?php

namespace XF\Import;

class Session
{
	public $importerId;
	public $logTable;
	public $retainIds = false;

	public $baseConfig = [];
	public $stepConfig = [];

	public $remainingSteps = [];
	public $currentStep;

	/**
	 * @var StepState|null
	 */
	public $currentState;

	public $stepTotals = [];
	public $stepTime = [];

	public $startTime = null;
	public $runCompleteTime = null;

	public $extra = [];

	public $notes = [];

	public $runType = null;
	public $runComplete = false;
	public $finalized = false;

	public function getStepsRun()
	{
		return array_keys($this->stepTotals);
	}

	public function canRunVia($method)
	{
		return (!$this->runType || $this->runType == $method);
	}

	public function getRunTime()
	{
		if ($this->startTime)
		{
			return ($this->runCompleteTime ?: time()) - $this->startTime;
		}
		else
		{
			return null;
		}
	}

	public function getImportCompletionDetails()
	{
		$completed = count($this->stepTotals);
		$current = $this->currentStep ? $completed + 1 : $completed;
		$remaining = count($this->remainingSteps);
		$total = $current + $remaining;

		return [
			'completed' => $completed,
			'current' => $current,
			'remaining' => $remaining,
			'total' => $total
		];
	}
}