<?php

namespace XF\AddOn;

/**
 * @property AddOn $addOn
 */
trait StepRunnerUninstallTrait
{
	/**
	 * @param array $stepParams
	 *
	 * @return null|StepResult
	 */
	public function uninstall(array $stepParams = [])
	{
		$stepParams = array_replace([
			'step' => 0
		], $stepParams);

		$step = $stepParams['step'];
		if (!$step)
		{
			$step = $this->getUninstallResumeStep();
		}
		return $this->uninstallStepRunner($step, $stepParams);
	}

	private function getUninstallResumeStep()
	{
		$installedAddOn = $this->addOn->getInstalledAddOn();
		if ($installedAddOn)
		{
			$lastActionStep = $installedAddOn->getLastActionStep('uninstall');
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
	private function uninstallStepRunner($step, array $stepParams)
	{
		$fnPrefix = 'uninstallStep';
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
			$this->addOn->updatePendingAction('uninstall', $result->step);

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