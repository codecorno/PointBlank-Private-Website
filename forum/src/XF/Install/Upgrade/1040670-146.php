<?php

namespace XF\Install\Upgrade;

class Version1040670 extends AbstractUpgrade
{
	public function getVersionName()
	{
		return '1.4.6';
	}

	public function step1()
	{
		$this->executeUpgradeQuery("
			ALTER TABLE xf_image_proxy
			ADD INDEX is_processing (is_processing)
		");
	}

	public function step2()
	{
		$this->executeUpgradeQuery("
			ALTER TABLE xf_login_attempt
			ADD INDEX ip_address_attempt_date (ip_address, attempt_date)
		");
	}
}