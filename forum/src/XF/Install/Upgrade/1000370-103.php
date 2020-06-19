<?php

namespace XF\Install\Upgrade;

class Version1000370 extends AbstractUpgrade
{
	public function getVersionName()
	{
		return '1.0.3';
	}

	public function step1()
	{
		$this->executeUpgradeQuery("
			INSERT IGNORE INTO xf_permission_entry
				(user_group_id, user_id, permission_group_id, permission_id, permission_value, permission_value_int)
			SELECT user_group_id, 0, 'general', 'report', 'allow', 0
				FROM xf_user_group
				WHERE xf_user_group.user_group_id <> 1
		");

		return true;
	}
}