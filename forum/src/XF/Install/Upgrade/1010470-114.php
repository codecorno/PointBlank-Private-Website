<?php

namespace XF\Install\Upgrade;

class Version1010470 extends AbstractUpgrade
{
	public function getVersionName()
	{
		return '1.1.4';
	}

	public function step1()
	{
		$this->executeUpgradeQuery('
			ALTER TABLE xf_user_confirmation
				ADD INDEX confirmation_date (confirmation_date)
		');

		$this->executeUpgradeQuery('
			ALTER TABLE xf_report
				ADD INDEX content_user_id_modified (content_user_id, last_modified_date)
		');

		$this->executeUpgradeQuery("
			UPDATE xf_user_field SET
				match_regex = '^[a-zA-Z0-9-_.,@:]+$'
			WHERE field_id = 'skype'
		");

		return true;
	}
}