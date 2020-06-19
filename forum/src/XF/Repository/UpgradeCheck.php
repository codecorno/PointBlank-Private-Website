<?php

namespace XF\Repository;

use XF\Mvc\Entity\Finder;
use XF\Mvc\Entity\Repository;

class UpgradeCheck extends Repository
{
	public function canCheckForUpgrades(&$error = null)
	{
		return true;
	}

	public function canOneClickUpgrade(&$error = null)
	{
		if (!$this->app()->config('enableOneClickUpgrade'))
		{
			$error = \XF::phrase('one_click_upgrades_disabled_upgrade_manually');
			return false;
		}

		if (\XF::$developmentMode)
		{
			$error = \XF::phrase('installation_in_development_mode_upgrade_manually');
			return false;
		}

		return true;
	}

	/**
	 * @return null|\XF\Entity\UpgradeCheck
	 */
	public function getLatestUpgradeCheck()
	{
		return $this->finder('XF:UpgradeCheck')
			->order('check_date', 'DESC')
			->fetchOne();
	}

	public function pruneUpgradeChecks($cutOff = null)
	{
		if ($cutOff === null)
		{
			$cutOff = \XF::$time - 86400 * 30;
		}

		return $this->db()->delete('xf_upgrade_check', 'check_date < ?', $cutOff);
	}
}