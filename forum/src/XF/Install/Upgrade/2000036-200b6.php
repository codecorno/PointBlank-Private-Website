<?php

namespace XF\Install\Upgrade;

use XF\Db\Schema\Alter;
use XF\Db\Schema\Create;

class Version2000036 extends AbstractUpgrade
{
	public function getVersionName()
	{
		return '2.0.0 Beta 6';
	}

	public function step1()
	{
		$sm = $this->schemaManager();

		$sm->alterTable('xf_code_event_listener', function(Alter $alter)
		{
			$alter->dropIndexes('addon_id_event_id_class_method');
		});
	}

	public function step2()
	{
		$db = $this->db();

		$jsonValue = $db->fetchOne('
			SELECT option_value
			FROM xf_option
			WHERE option_id = ?', 'usernameValidation'
		);

		$value = json_decode($jsonValue);

		if ($value->matchRegex !== '')
		{
			$value->matchRegex = '#' . str_replace('#', '\\#', $value->matchRegex) . '#i'; // escape delim only

			$this->executeUpgradeQuery("
				UPDATE xf_option
				SET option_value = ?
				WHERE option_id = ?
			", [json_encode($value), 'usernameValidation']);
		}
	}
}