<?php

namespace XF\Install\Upgrade;

use XF\Db\Schema\Alter;

class Version2010033 extends AbstractUpgrade
{
	public function getVersionName()
	{
		return '2.1.0 Beta 3';
	}

	public function step1()
	{
		$this->alterTable('xf_admin_navigation', function(Alter $table)
		{
			$table->addColumn('development_only', 'tinyint', 3)->setDefault(0)->after('debug_only');
		});
	}
}