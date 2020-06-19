<?php

namespace XF\Install\Upgrade;

class Version1010270 extends AbstractUpgrade
{
	public function getVersionName()
	{
		return '1.1.2';
	}

	public function step1()
	{
		$this->executeUpgradeQuery('
			ALTER TABLE xf_user_external_auth
				DROP PRIMARY KEY,
				ADD PRIMARY KEY (user_id, provider)
		');

		return true;
	}
}