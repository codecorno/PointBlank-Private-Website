<?php

namespace XF\Db\Schema;

use XF\Db\SchemaManager;

class Alter extends AbstractDdl
{
	protected $engine;
	protected $renameTo;
	protected $columnDefinitions;
	protected $indexDefinitions;

	protected $convertCharset;
	protected $convertCollation;

	/**
	 * @var Column[]
	 */
	protected $changeColumns = [];

	/**
	 * @var Index[]
	 */
	protected $changeIndexes = [];

	protected $conflictRenames = [];

	public function __construct(\XF\Db\AbstractAdapter $db, SchemaManager $sm, $tableName)
	{
		parent::__construct($db, $sm, $tableName);

		$sm = $db->getSchemaManager();

		$tableStatus = $sm->getTableStatus($tableName);
		if (!$tableStatus)
		{
			throw new \InvalidArgumentException("Table '$tableName' does not exist so cannot be altered");
		}

		$this->columnDefinitions = $sm->getTableColumnDefinitions($tableName);
		$this->indexDefinitions = $sm->getTableIndexDefinitions($tableName);
	}

	public function engine($engine)
	{
		$this->engine = $engine;

		return $this;
	}

	public function renameTo($newName)
	{
		$this->renameTo = $newName;

		return $this;
	}

	public function convertCharset($charset, $collation = null)
	{
		$this->convertCharset = $charset;
		$this->convertCollation = $collation;

		return $this;
	}

	public function changeColumn($columnName, $type = null, $length = null)
	{
		$column = new Column($this->db, $this, $columnName, null, $this->forceChanges);
		$column->setDefinition();

		if ($type !== null)
		{
			$column->type($type);
		}
		if ($length !== null)
		{
			$column->length($length);
		}

		$this->changeColumns[] = $column;

		return $column;
	}

	public function dropColumns($columnNames)
	{
		foreach ((array)$columnNames AS $columnName)
		{
			$columnDef = $this->getColumnDefinition($columnName);
			if (!$columnDef)
			{
				// column already dropped/doesn't exist so skip it
				continue;
			}

			$column = $this->changeColumn($columnName);
			$column->drop();
		}

		return $this;
	}

	public function renameColumn($columnName, $newColumnName)
	{
		$column = $this->changeColumn($columnName);
		$column->renameTo($newColumnName);
		return $column;
	}

	public function changeIndex($indexName)
	{
		$index = new Index($this->db, $this, $indexName, null, $this->forceChanges);
		$index->setDefinition();

		$this->changeIndexes[] = $index;

		return $index;
	}

	public function dropPrimaryKey()
	{
		$this->dropIndexes('primary');
	}

	public function dropIndexes($indexNames)
	{
		foreach ((array)$indexNames AS $indexName)
		{
			if ($indexName == 'primary')
			{
				$indexName = 'PRIMARY';
			}
			$indexDef = $this->getIndexDefinition($indexName);
			if (!$indexDef)
			{
				// index already dropped/doesn't exist so skip it
				continue;
			}

			$index = $this->changeIndex($indexName);
			$index->drop();
		}
	}

	public function getColumnDefinition($columnName)
	{
		$definitions = $this->columnDefinitions;
		return isset($definitions[$columnName]) ? $definitions[$columnName] : null;
	}

	public function getColumnDefinitions()
	{
		return $this->columnDefinitions;
	}

	public function forgetColumn($columnName)
	{
		unset($this->columnDefinitions[$columnName]);
	}

	public function registerColumnRename($oldName, $newName)
	{
		// forget the column exists
		unset($this->columnDefinitions[$oldName]);

		// look for any existing indexes using the old column, they will implicitly use the new one
		foreach ($this->indexDefinitions AS $index => $columns)
		{
			foreach ($columns AS $id => $column)
			{
				if ($column['Column_name'] == $oldName)
				{
					$this->indexDefinitions[$index][$id]['Column_name'] = $newName;
				}
			}
		}
	}

	public function registerConflictRename($oldName, $newName)
	{
		$this->conflictRenames[$oldName] = $newName;
	}

	public function getConflictRenames()
	{
		return $this->conflictRenames;
	}

	public function getIndexDefinition($indexName)
	{
		$definitions = $this->indexDefinitions;
		return isset($definitions[$indexName]) ? $definitions[$indexName] : null;
	}

	public function getIndexDefinitions()
	{
		return $this->indexDefinitions;
	}

	public function forgetIndex($indexName)
	{
		unset($this->indexDefinitions[$indexName]);
	}

	public function getQueries()
	{
		$tableName = $this->tableName;

		$query = "ALTER TABLE `$tableName`\n";

		$tableDefinition = [];
		if ($this->engine)
		{
			$tableDefinition[] = 'ENGINE ' . $this->engine;
		}

		if ($this->convertCharset)
		{
			$convert = "CONVERT TO CHARACTER SET `{$this->convertCharset}`";
			if ($this->convertCollation)
			{
				$convert .= " COLLATE `{$this->convertCollation}`";
			}
			$tableDefinition[] = $convert;
		}

		foreach ($this->changeColumns AS $column)
		{
			$definition = $column->getDefinition(true);
			if ($definition)
			{
				$tableDefinition[] = $definition;
			}
		}
		foreach ($this->addColumns AS $column)
		{
			$definition = $column->getDefinition();
			if ($definition)
			{
				$tableDefinition[] = $definition;
			}
		}
		foreach ($this->changeIndexes AS $index)
		{
			$definition = $index->getDefinition(true);
			if ($definition)
			{
				$tableDefinition[] = $definition;
			}
		}
		foreach ($this->addIndexes AS $index)
		{
			$definition = $index->getDefinition();
			if ($definition)
			{
				$tableDefinition[] = $definition;
			}
		}

		if (!$tableDefinition && !$this->renameTo)
		{
			return [];
		}

		if ($this->renameTo)
		{
			$oldExists = $this->sm->tableExists($this->tableName);
			$newExists = $this->sm->tableExists($this->renameTo);

			if ($oldExists && !$newExists)
			{
				$query .= "RENAME TO `{$this->renameTo}`";
				if ($tableDefinition)
				{
					$query .= ",\n";
				}
			}
			else
			{
				$this->renameTo = false;
			}
		}

		$query .= implode(",\n", $tableDefinition);

		return $query ? [$query] : [];
	}
}