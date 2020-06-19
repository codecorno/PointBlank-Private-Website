<?php

namespace XF\Search\Query;

class SqlOrder
{
	protected $order;
	protected $tables = [];

	public function __construct($order, TableReference $table = null)
	{
		$this->order = $order;

		if ($table)
		{
			$this->tables[$table->getAlias()] = $table;
		}
	}

	public function addTable(TableReference $table)
	{
		$this->tables[$table->getAlias()] = $table;
	}

	public function getOrder()
	{
		return $this->order;
	}

	public function getTables()
	{
		return $this->tables;
	}
}