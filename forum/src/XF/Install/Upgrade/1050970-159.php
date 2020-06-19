<?php

namespace XF\Install\Upgrade;

class Version1050970 extends AbstractUpgrade
{
	public function getVersionName()
	{
		return '1.5.9';
	}

	public function step1()
	{
		$this->executeUpgradeQuery("
			ALTER TABLE xf_session_activity CHANGE controller_name controller_name VARBINARY(75) NOT NULL
		");

		$this->executeUpgradeQuery("
			ALTER TABLE xf_admin_search_type CHANGE handler_class handler_class VARCHAR(75) NOT NULL
		");
	}
}