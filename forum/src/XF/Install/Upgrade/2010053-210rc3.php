<?php

namespace XF\Install\Upgrade;

use XF\Db\Schema\Alter;
use XF\Db\Schema\Create;

class Version2010053 extends AbstractUpgrade
{
	public function getVersionName()
	{
		return '2.1.0 Release Candidate 3';
	}

	public function step1()
	{
		$this->createTable('xf_json_convert_error', function(Create $table)
		{
			$table->addColumn('error_id', 'int')->autoIncrement();
			$table->addColumn('table_name', 'varbinary', 100);
			$table->addColumn('column_name', 'varbinary', 100);
			$table->addColumn('pk_id', 'int');
			$table->addColumn('original_value', 'mediumblob');
			$table->addUniqueKey(['table_name', 'column_name', 'pk_id'], 'table_column_pk');
		});
	}
}