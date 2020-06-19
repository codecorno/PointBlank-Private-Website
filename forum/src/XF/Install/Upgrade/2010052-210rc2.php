<?php

namespace XF\Install\Upgrade;

use XF\Db\Schema\Alter;

class Version2010052 extends AbstractUpgrade
{
	public function getVersionName()
	{
		return '2.1.0 Release Candidate 2';
	}

	public function step1()
	{
		// mostly a duplication of 2001270 as earlier beta upgrades would otherwise skip this change

		$this->schemaManager()->alterTable('xf_admin_navigation', function(Alter $table)
		{
			$table->changeColumn('navigation_id')->length(50);
			$table->changeColumn('parent_navigation_id')->length(50);
		});
	}
}