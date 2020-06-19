<?php

namespace XF\Install\Upgrade;

class Version1051370 extends AbstractUpgrade
{
	public function getVersionName()
	{
		return '1.5.13';
	}

	public function step1()
	{
		$this->executeUpgradeQuery("
			ALTER TABLE xf_warning
				DROP INDEX expiry,
				ADD INDEX is_expired_expiry (is_expired, expiry_date),
				ADD INDEX warning_user_id (warning_user_id)
		");
	}

	public function step2()
	{
		$this->executeUpgradeQuery("
			ALTER TABLE xf_spam_cleaner_log
				ADD INDEX user_id (user_id),
				ADD INDEX applying_user_id (applying_user_id)
		");
	}
}