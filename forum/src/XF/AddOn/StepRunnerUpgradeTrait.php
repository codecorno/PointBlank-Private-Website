<?php

namespace XF\AddOn;

/**
 * @property AddOn $addOn
 */
trait StepRunnerUpgradeTrait
{
	/**
	 * @param array $stepParams
	 *
	 * @return null|StepResult
	 */
	public function upgrade(array $stepParams = [])
	{
		$stepParams = $this->getStepParams($stepParams);

		$versions = [];
		$stepsGrouped = [];

		$reflection = new \ReflectionObject($this);
		foreach ($reflection->getMethods() AS $method)
		{
			if (preg_match('/^upgrade(\d+)Step(\d+)$/', $method->name, $match))
			{
				$versionId = intval($match[1]);
				$step = intval($match[2]);

				$versions[] = $versionId;
				$stepsGrouped[$versionId][$step] = $step;
			}
		}

		if (!$versions)
		{
			return null;
		}

		if (!$stepParams['version_id'])
		{
			$resume = $this->getUpgradeResumeStep();
			$stepParams['version_id'] = $resume[0];
			$stepParams['step'] = $resume[1];
		}

		$runStep = $stepParams['step'];
		if (!$runStep)
		{
			$runStep = 1;
		}

		$versions = array_unique($versions);
		sort($versions, SORT_NUMERIC);

		foreach ($versions AS $i => $versionId)
		{
			if ($versionId >= $stepParams['version_id'])
			{
				$versionSteps = $stepsGrouped[$versionId];
				if (isset($versionSteps[$runStep]))
				{
					if ($versionId != $stepParams['version_id'] || $runStep != $stepParams['step'])
					{
						// sanity check - don't pass params if we're changing versions or steps
						$stepParams = [];
					}

					$nextVersionId = isset($versions[$i + 1]) ? $versions[$i + 1] : null;

					return $this->upgradeStepRunner($versionId, $runStep, $stepParams, $nextVersionId);
				}

				// if we hit here, then the step couldn't be found, so move onto the next upgrade
				$runStep = 1;
				$stepParams = $this->getStepParams();
			}
		}

		// if we got here, we don't have any other upgrades to run
		return null;
	}

	protected function getStepParams(array $stepParams = [])
	{
		return array_replace([
			'version_id' => 0,
			'step' => 0
		], $stepParams);
	}

	private function getUpgradeResumeStep()
	{
		$installedAddOn = $this->addOn->getInstalledAddOn();
		$lastActionStep = $installedAddOn->getLastActionStep('upgrade');
		if ($lastActionStep)
		{
			$lastActionStep[1]++; // we completed this step, so need to resume with the next
			return $lastActionStep;
		}

		return [$this->addOn->getInstalledAddOn()->version_id + 1, 1];
	}

	/**
	 * @param integer $runVersion
	 * @param integer $runStep
	 * @param array $stepParams
	 * @param integer|null $nextVersionId
	 *
	 * @return null|StepResult
	 */
	private function upgradeStepRunner($runVersion, $runStep, array $stepParams, $nextVersionId)
	{
		$fnPattern = 'upgrade%dStep%d';
		$fn = sprintf($fnPattern, $runVersion, $runStep);
		if (!method_exists($this, $fn))
		{
			throw new \LogicException("Step $fn doesn't exist. Should have been checked earlier.");
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

		$result->step = $runStep;
		$result->version = $runVersion;

		if ($result->complete)
		{
			$this->addOn->updatePendingAction('upgrade', $result->version . ':' . $result->step);

			$nextStep = $runStep + 1;
			$nextStepFn = sprintf($fnPattern, $runVersion, $nextStep);
			if (method_exists($this, $nextStepFn))
			{
				$result = new StepResult(false, [], $nextStep, $runVersion);
			}
			else
			{
				// we're done this upgrade version, see if we have another
				if ($nextVersionId === null)
				{
					return null;
				}

				$nextFn = sprintf($fnPattern, $nextVersionId, 1);
				if (!method_exists($this, $nextFn))
				{
					return null;
				}

				$result = new StepResult(false, [], 1, $nextVersionId);
			}
		}

		return $result;
	}
}