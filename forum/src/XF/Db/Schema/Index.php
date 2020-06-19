<?php

namespace XF\Db\Schema;

class Index extends AbstractDefinition
{
	protected $type;
	protected $columns;
	protected $using;

	protected $autoNamed = false;

	protected function init()
	{
		if ($this->isAlter() && !$this->existingDefinition && $this->name)
		{
			/** @var Alter $ddl */
			$ddl = $this->ddl;

			$indexName = (strtolower($this->name) == 'primary' ? 'PRIMARY' : $this->name);
			$this->existingDefinition = $ddl->getIndexDefinition($indexName);
		}
	}

	public function type($type)
	{
		$type = strtolower($type);

		switch ($type)
		{
			case 'primary':
			case 'unique':
			case 'key':
			case 'fulltext':
				$this->type = $type;
				break;

			default:
				throw new \InvalidArgumentException("Index definition type $type is not valid.");
		}

		return $this;
	}

	public function name($indexName)
	{
		$this->name = $indexName;
		$this->existingDefinition = null;

		return $this;
	}

	public function columns($columns)
	{
		if (!is_array($columns))
		{
			$columns = [$columns];
		}
		$this->columns = $columns;

		if ($this->autoNamed)
		{
			$this->name = null;
		}

		return $this;
	}

	public function addColumn($columnName)
	{
		$this->columns[] = $columnName;

		return $this;
	}

	public function using($using = null)
	{
		if ($using === null)
		{
			$using = 'BTREE';
		}
		$this->using = $using;

		return $this;
	}

	public function drop()
	{
		parent::drop();

		if ($this->isAlter())
		{
			if (!$this->name)
			{
				throw new \LogicException("Must have an index name before dropping");
			}

			$this->syncExistingDefinition();

			/** @var Alter $ddl */
			$ddl = $this->ddl;
			$ddl->forgetIndex($this->name);
		}
	}

	public function getIndexName()
	{
		$this->syncIndexName();

		return $this->name;
	}

	protected function syncIndexName()
	{
		$name = $this->name;

		if ($this->type == 'primary')
		{
			$this->name = 'PRIMARY';
		}
		else if (!$this->name)
		{
			$columnNames = [];
			foreach ($this->columns AS $column)
			{
				if (is_array($column))
				{
					$columnNames[] = $column[0];
				}
				else
				{
					$columnNames[] = $column;
				}
			}
			$this->name = implode('_', $columnNames);
			$this->autoNamed = true;
		}

		if ($name !== $this->name)
		{
			$this->existingDefinition = null;
		}
	}

	protected function syncExistingDefinition($force = false)
	{
		$this->syncIndexName();

		if ($this->isAlter() && (!$this->existingDefinition || $force))
		{
			/** @var Alter $ddl */
			$ddl = $this->ddl;
			$this->existingDefinition = $ddl->getIndexDefinition($this->name);
		}
	}

	public function getDefinition($change = false)
	{
		$this->syncExistingDefinition();

		$type = $this->type;
		$indexName = $this->getIndexName();
		$columns = $this->columns;

		/** @var Alter|Create $ddl */
		$ddl = $this->ddl;

		$definition = '';

		if ($this->drop)
		{
			if (!$this->existingDefinition)
			{
				return '';
			}
			else if ($type == 'primary')
			{
				return "DROP PRIMARY KEY";
			}
			else
			{
				return "DROP INDEX `$indexName`";
			}
		}

		if ($this->isAlter() && $this->existingDefinition)
		{
			$conflictRenames = $ddl->getConflictRenames();
			foreach ($this->columns AS $column)
			{
				if (is_array($column))
				{
					$column = $column[0];
				}

				if (isset($conflictRenames[$column]))
				{
					$this->syncExistingDefinition(true);
					break;
				}
			}
		}

		if ($this->isAlter() && $this->existingDefinition)
		{
			// confirm whether we've changed anything in this index
			$existing = new static($this->db, $ddl, $indexName, $this->existingDefinition);
			$existing->setDefinition();

			if ($this->compare($existing))
			{
				// new index is identical to existing index so skip it
				return '';
			}

			// otherwise, we need to drop the old one
			if ($existing->type == 'primary')
			{
				$definition .= "DROP PRIMARY KEY";
			}
			else
			{
				$definition .= "DROP INDEX `$indexName`";
			}
		}

		if ($this->isAlter() && !$this->existingDefinition && $change)
		{
			throw new \InvalidArgumentException("Index definition '{$indexName}' does not exist therefore it cannot be changed.");
		}

		if (!$columns)
		{
			throw new \InvalidArgumentException("Index definition must include columns.");
		}

		if ($definition)
		{
			$definition .= ",\n";
		}

		if ($this->isAlter())
		{
			$definition .= 'ADD ';
		}

		$definition .= $this->getIndexDefinitionSql();

		return $definition;
	}

	protected function getIndexDefinitionSql()
	{
		$type = $this->type;
		$indexName = $this->getIndexName();
		$columns = $this->columns;
		$using = $this->using;

		$definition = '';

		$columnsString = $this->getColumnsString($columns);

		switch ($type)
		{
			case 'primary':
				$definition .= 'PRIMARY KEY (' . $columnsString . ')';
				break;

			case 'unique':
			case 'key':
			case 'fulltext':

				$typePrefix = '';
				if ($type == 'unique')
				{
					$typePrefix = 'UNIQUE ';
				}
				else if ($type == 'fulltext')
				{
					$typePrefix = 'FULLTEXT ';
				}

				$key = $typePrefix . "KEY `$indexName` ($columnsString)";
				if ($using)
				{
					$key .= ' USING ' . strtoupper($using);
				}

				$definition .= $key;
				break;
		}

		return $definition;
	}

	public function setDefinition()
	{
		if (!$this->isAlter())
		{
			return;
		}

		$definition = $this->existingDefinition;
		if (!$definition)
		{
			return;
		}

		$this->setupFromExistingDefinition($definition);
	}

	public function setupFromExistingDefinition(array $indexColumns)
	{
		$type = null;
		$columns = [];

		foreach ($indexColumns AS $key => $index)
		{
			if ($type === null)
			{
				if (strtolower($index['Key_name']) == 'primary')
				{
					$type = 'primary';
				}
				else if ($index['Non_unique'] == 1)
				{
					$type = 'key';
				}
				else
				{
					$type = 'unique';
				}
			}

			if ($index['Sub_part'] === null)
			{
				$column = $index['Column_name'];
			}
			else
			{
				$column = [$index['Column_name'], $index['Sub_part']];
			}

			$columns[] = $column;
		}

		$this->type($type);
		$this->columns($columns);
		// name should already be set
	}

	public function resetDefinition() { }

	protected function getColumnsString(array $columns)
	{
		$output = [];

		foreach ($columns AS $column)
		{
			if (is_array($column))
			{
				if (!empty($column[1]))
				{
					$output[] = "`$column[0]`($column[1])";
				}
				else
				{
					$output[] = "`$column[0]`";
				}
			}
			else
			{
				$output[] = "`$column`";
			}
		}

		return implode(', ', $output);
	}

	public function getComparisonValue()
	{
		return 'index: ' . $this->getIndexDefinitionSql();
	}
}