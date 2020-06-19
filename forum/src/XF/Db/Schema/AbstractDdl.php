<?php

namespace XF\Db\Schema;

use XF\Db\AbstractAdapter;
use XF\Db\SchemaManager;

abstract class AbstractDdl
{
	/**
	 * @var AbstractAdapter
	 */
	protected $db;

	/**
	 * @var SchemaManager
	 */
	protected $sm;

	protected $tableName;

	/**
	 * @var Column[]
	 */
	protected $addColumns = [];

	/**
	 * @var Index[]
	 */
	protected $addIndexes = [];

	protected $forceChanges = true;

	abstract public function getQueries();

	public function __construct(AbstractAdapter $db, SchemaManager $sm, $tableName)
	{
		$this->db = $db;
		$this->sm = $sm;
		$this->tableName = $tableName;
	}

	public function getTableName()
	{
		return $this->tableName;
	}

	public function forceChanges($force = true)
	{
		$this->forceChanges = $force;

		return $this;
	}

	public function addColumn($columnName, $type = null, $length = null)
	{
		$column = new Column($this->db, $this, $columnName, null, $this->forceChanges);

		if ($type !== null)
		{
			$column->type($type);
		}
		if ($length !== null)
		{
			$column->length($length);
		}

		$this->addColumns[] = $column;

		return $column;
	}

	public function addIndex($indexName = null)
	{
		$index = new Index($this->db, $this, $indexName, null, $this->forceChanges);

		$this->addIndexes[] = $index;

		return $index;
	}

	/**
	 * @param $column
	 *
	 * @return Index
	 */
	public function addPrimaryKey($column)
	{
		$index = $this->addIndex('primary');

		$index->columns($column);
		$index->type('primary');

		return $index;
	}

	/**
	 * @param $column
	 * @param null $indexName
	 *
	 * @return Index
	 */
	public function addUniqueKey($column, $indexName = null)
	{
		$index = $this->addIndex($indexName);

		$index->columns($column);
		$index->type('unique');

		return $index;
	}

	/**
	 * @param $column
	 * @param null $indexName
	 *
	 * @return Index
	 */
	public function addKey($column, $indexName = null)
	{
		$index = $this->addIndex($indexName);

		$index->columns($column);
		$index->type('key');

		return $index;
	}

	/**
	 * @param $column
	 * @param null $indexName
	 *
	 * @return Index
	 */
	public function addFullTextKey($column, $indexName = null)
	{
		$index = $this->addIndex($indexName);

		$index->columns($column);
		$index->type('fulltext');

		return $index;
	}

	public function apply()
	{
		$queries = $this->getQueries();

		foreach ($queries AS $query)
		{
			$this->db->query($query);
		}

		return $queries;
	}
}