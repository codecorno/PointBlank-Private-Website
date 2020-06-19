<?php

namespace XF\Db\Schema;

class Drop extends AbstractDdl
{
	protected $checkExists = true;

	public function checkExists($checkExists)
	{
		$this->checkExists = $checkExists;
	}

	public function getQueries()
	{
		$tableName = $this->tableName;

		$query = 'DROP TABLE';

		if ($this->checkExists)
		{
			$query .= ' IF EXISTS';
		}

		$query .= " `$tableName`";

		return [$query];
	}

	public function addColumn($columnName, $type = null, $length = null)
	{
		throw new \InvalidArgumentException('Cannot add columns while dropping');
	}

	public function addIndex($indexName = null)
	{
		throw new \InvalidArgumentException('Cannot add indexes while dropping');
	}
}