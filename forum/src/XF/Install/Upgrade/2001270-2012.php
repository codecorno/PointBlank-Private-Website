<?php

namespace XF\Install\Upgrade;

use XF\Db\Schema\Alter;

class Version2001270 extends AbstractUpgrade
{
	public function getVersionName()
	{
		return '2.0.12';
	}

	public function step1()
	{
		$this->schemaManager()->alterTable('xf_admin_navigation', function(Alter $table)
		{
			$table->changeColumn('navigation_id')->length(50);
			$table->changeColumn('parent_navigation_id')->length(50);
		});
	}
}