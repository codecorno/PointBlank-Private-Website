<?php

namespace XF\Search\Query;

class TableReference
{
	protected $alias;
	protected $table;
	protected $condition;

	public function __construct($alias, $table, $condition)
	{
		$this->alias = $alias;
		$this->table = $table;
		$this->condition = $condition;
	}

	public function getAlias()
	{
		return $this->alias;
	}

	public function getTable()
	{
		return $this->table;
	}

	public function getCondition()
	{
		return $this->condition;
	}
}