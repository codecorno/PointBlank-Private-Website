<?php

namespace XF\Search\Query;

class SqlConstraint
{
	protected $condition;
	protected $values = [];
	protected $tables = [];

	public function __construct($condition, $values = null, TableReference $table = null)
	{
		$this->condition = $condition;

		if ($values !== null)
		{
			if (!is_array($values))
			{
				$values = [$values];
			}
			$this->values = $values;
		}

		if ($table)
		{
			$this->tables[$table->getAlias()] = $table;
		}
	}

	public function addTable(TableReference $table)
	{
		$this->tables[$table->getAlias()] = $table;
	}

	public function getCondition()
	{
		return $this->condition;
	}

	public function getValues()
	{
		return $this->values;
	}

	public function getTables()
	{
		return $this->tables;
	}

	public function getSql(\XF\Db\AbstractAdapter $db)
	{
		if ($this->values)
		{
			return vsprintf(
				$this->condition,
				array_map([$db, 'quote'], $this->values)
			);
		}
		else
		{
			return $this->condition;
		}
	}
}