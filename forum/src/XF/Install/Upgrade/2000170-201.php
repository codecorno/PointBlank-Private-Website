<?php

namespace XF\Install\Upgrade;

use XF\Db\Schema\Alter;

class Version2000170 extends AbstractUpgrade
{
	public function getVersionName()
	{
		return '2.0.1';
	}

	public function step1()
	{
		$this->schemaManager()->alterTable('xf_widget_definition', function(Alter $table)
		{
			$table->dropIndexes('definition_class');
		});
	}

	public function step2()
	{
		// this is fairly legacy but an old XF1 bug meant that the "everyone" value was selectable or even
		// default for the allow_send_personal_conversations option.
		// this value is invalid so attempt to fix it where it still exists.
		$this->executeUpgradeQuery("
			UPDATE xf_user_privacy
			SET allow_send_personal_conversation = 'members'
			WHERE allow_send_personal_conversation = 'everyone'
		");
	}
}