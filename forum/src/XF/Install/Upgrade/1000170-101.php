<?php

namespace XF\Install\Upgrade;

class Version1000170 extends AbstractUpgrade
{
	public function getVersionName()
	{
		return '1.0.1';
	}

	public function step1()
	{
		$this->executeUpgradeQuery("
			ALTER TABLE xf_thread
				DROP INDEX node_id_sticky,
				ADD INDEX node_id_sticky_last_post_date (node_id, sticky, last_post_date)
		");

		return true;
	}
}