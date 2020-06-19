<?php

namespace XF;

trait MultiPartRunnerTrait
{
	protected $currentStep = 0;
	protected $stepLastOffset = null;

	abstract protected function getSteps();

	public function restoreState($currentStep, $lastOffset)
	{
		$this->currentStep = $currentStep;
		$this->stepLastOffset = $lastOffset;

		return $this;
	}

	protected function getRunnableStep()
	{
		$stepId = 0;

		foreach ($this->getSteps() AS $stepMethod)
		{
			if ($stepId < $this->currentStep)
			{
				$stepId++;
				continue;
			}

			return [$stepMethod, $this->stepLastOffset];
		}

		return null;
	}

	protected function runLoop($maxRunTime = 0)
	{
		$start = microtime(true);

		while ($stepInfo = $this->getRunnableStep())
		{
			list($stepMethod, $stepLastOffset) = $stepInfo;

			$remainingTime = $maxRunTime ? ($maxRunTime - (microtime(true) - $start)) : 0;

			$stepResult = $this->$stepMethod($stepLastOffset, $remainingTime);
			if ($stepResult === null || $stepResult === false)
			{
				// next step
				$this->currentStep++;
				$this->stepLastOffset = null;
			}
			else
			{
				// step to be continued
				$this->stepLastOffset = $stepResult;
			}

			if ($maxRunTime && microtime(true) - $start > $maxRunTime)
			{
				break;
			}
		}

		if (!$this->getRunnableStep())
		{
			return \XF\ContinuationResult::completed();
		}
		else
		{
			return \XF\ContinuationResult::continued([
				'currentStep' => $this->currentStep,
				'lastOffset' => $this->stepLastOffset
			]);
		}
	}
}