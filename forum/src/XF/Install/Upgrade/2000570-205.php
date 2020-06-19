<?php

namespace XF\Install\Upgrade;

use XF\Db\Schema\Alter;

class Version2000570 extends AbstractUpgrade
{
	public function getVersionName()
	{
		return '2.0.5';
	}

	public function step1()
	{
		$this->schemaManager()->alterTable('xf_user_alert', function(Alter $table)
		{
			$table->addColumn('depends_on_addon_id', 'varbinary', 50)->setDefault('');
		});
	}
}