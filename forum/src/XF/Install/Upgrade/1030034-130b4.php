<?php

namespace XF\Install\Upgrade;

class Version1030034 extends AbstractUpgrade
{
	public function getVersionName()
	{
		return '1.3.0 Beta 4';
	}

	public function step1()
	{
		$this->executeUpgradeQuery("
			ALTER TABLE  `xf_user_upgrade_log` ADD INDEX log_date (`log_date`)
		");
	}
}