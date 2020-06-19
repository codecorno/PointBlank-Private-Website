<?php

namespace XF\Install\Upgrade;

use XF\Db\Schema\Alter;
use XF\Db\Schema\Create;

class Version2000033 extends AbstractUpgrade
{
	public function getVersionName()
	{
		return '2.0.0 Beta 3';
	}

	public function step1()
	{
		$sm = $this->schemaManager();

		$sm->alterTable('xf_addon', function(Alter $alter)
		{
			$alter->addColumn('last_pending_action', 'varchar', 50)->nullable();
		});
	}
}