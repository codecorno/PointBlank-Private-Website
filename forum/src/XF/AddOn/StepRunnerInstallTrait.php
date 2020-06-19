<?php

namespace XF\AddOn;

/**
 * @property AddOn $addOn
 */
trait StepRunnerInstallTrait
{
	/**
	 * @param array $stepParams
	 *
	 * @return null|StepResult
	 */
	public function install(array $stepParams = [])
	{
		$stepParams = array_replace([
			'step' => 0
		], $stepParams);

		$step = $stepParams['step'];
		if (!$step)
		{
			$step = $this->getInstallResumeStep();
		}

		return $this->installStepRunner($step, $stepParams);
	}

	private function getInstallResumeStep()
	{
		$installedAddOn = $this->addOn->getInstalledAddOn();
		if ($installedAddOn)
		{
			$lastActionStep = $installedAddOn->getLastActionStep('install');
			if ($lastActionStep)
			{
				return $lastActionStep + 1; // this is the last completed step, so resume with the next
			}
		}

		return 1;
	}

	/**
	 * @param integer $step
	 * @param array $stepParams
	 *
	 * @return null|StepResult
	 */
	private function installStepRunner($step, array $stepParams)
	{
		$fnPrefix = 'installStep';
		$fn = $fnPrefix . $step;
		if (!method_exists($this, $fn))
		{
			return null;
		}

		$result = $this->$fn($stepParams);
		if ($result === null || $result === true)
		{
			$result = new StepResult(true);
		}
		else if (is_array($result))
		{
			$result = new StepResult(false, $result);
		}

		if (!($result instanceof StepResult))
		{
			throw new \LogicException("Must return a StepResult object");
		}

		$result->step = $step;

		if ($result->complete)
		{
			$this->addOn->updatePendingAction('install', $result->step);

			$nextStep = $step + 1;
			$nextFn = $fnPrefix . $nextStep;
			if (!method_exists($this, $nextFn))
			{
				return null;
			}

			$result = new StepResult(false, [], $nextStep);
		}

		return $result;
	}
}