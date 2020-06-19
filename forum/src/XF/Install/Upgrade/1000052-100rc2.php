<?php

namespace XF\Install\Upgrade;

class Version1000052 extends AbstractUpgrade
{
	public function getVersionName()
	{
		return '1.0.0 Release Candidate 2';
	}

	public function step1()
	{
		$this->executeUpgradeQuery("
			ALTER TABLE xf_user_alert
				DROP INDEX viewDate,
				ADD INDEX viewDate_eventDate (view_date, event_date)
		");

		return true;
	}
}