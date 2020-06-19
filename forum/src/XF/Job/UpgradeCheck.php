<?php

namespace XF\Job;

class UpgradeCheck extends AbstractJob
{
	public function run($maxRunTime)
	{
		$this->performUpgradeCheck();

		// jitter between 0 and 12 hours on top of the base 1 day. This should ensure some randomness
		// of the requests to the XF server so not all sites try to communicate at the same time
		// while still ensuring that we check versions once every 1-2 days.
		$continueDate = \XF::$time + 1 * 24 * 3600;
		$offsetJitter = mt_rand(0, 12 * 3600);
		$continueDate += $offsetJitter;

		$result = $this->resume();
		$result->continueDate = $continueDate;

		return $result;
	}

	protected function performUpgradeCheck()
	{
		/** @var \XF\Repository\UpgradeCheck $checkRepo */
		$checkRepo = $this->app->repository('XF:UpgradeCheck');

		if (!$checkRepo->canCheckForUpgrades())
		{
			return;
		}

		/** @var \XF\Service\Upgrade\Checker $checker */
		$checker = $this->app->service('XF:Upgrade\Checker');
		$checker->check();
	}

	public function getStatusMessage()
	{
		return \XF::phrase('performing_upgrade_check');
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