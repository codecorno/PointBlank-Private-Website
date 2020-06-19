<?php

namespace XF\Mvc\Entity;

class FinderExpression
{
	protected $sqlExpression;
	protected $columnReferences;

	public function __construct($sqlExpression, array $columnReferences = [])
	{
		$this->sqlExpression = $sqlExpression;
		$this->columnReferences = $columnReferences;
	}

	public function getSqlExpression()
	{
		return $this->sqlExpression;
	}

	public function getColumnReferences()
	{
		return $this->columnReferences;
	}

	public function setExpression($sqlExpression, array $columnReferences = [])
	{
		$this->sqlExpression = $sqlExpression;
		$this->columnReferences = $columnReferences;
	}

	public function renderSql(Finder $finder, $markJoinFundamental)
	{
		$columns = [];
		foreach ($this->columnReferences AS $column)
		{
			$columns[] = $finder->columnSqlName($column, $markJoinFundamental);
		}

		return vsprintf($this->sqlExpression, $columns);
	}
}