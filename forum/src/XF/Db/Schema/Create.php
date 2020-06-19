<?php

namespace XF\Db\Schema;

class Create extends AbstractDdl
{
	protected $checkExists = false;
	protected $engine;
	protected $comment;

	public function checkExists($checkExists)
	{
		$this->checkExists = $checkExists;
	}

	public function engine($engine)
	{
		$this->engine = $engine;
	}

	public function comment($comment)
	{
		$this->comment = $comment;
	}

	public function getQueries()
	{
		$tableName = $this->tableName;

		$queries = [];

		$query = 'CREATE TABLE';

		if ($this->checkExists)
		{
			$query .= ' IF NOT EXISTS';
		}
		else
		{
			$existing = $this->db->getSchemaManager()->getTableStatus($tableName);
			if ($existing)
			{
				$existing = new static($this->db, $this->sm, $tableName);
				$existing->setDefinition();

				if ($this->compare($existing))
				{
					// new table is identical, so just skip it
					return [];
				}
				else if ($this->forceChanges)
				{
					$conflictName = $tableName . '__conflict';
					if ($this->sm->tableExists($conflictName))
					{
						$i = 2;
						while ($this->sm->tableExists($conflictName . $i))
						{
							$i++;
						}

						$conflictName .= $i;
					}
					$queries[] = "ALTER TABLE `$tableName` RENAME TO `$conflictName`";

					$errorMessage = sprintf(
						"Renamed table %s to %s to avoid schema conflict",
						$tableName,
						$conflictName
					);

					\XF::logError($errorMessage, true);
				}
				else
				{
					throw new \LogicException("$tableName already exists, but the structure does not match.");
				}
			}
		}

		$query .= " `$tableName` (\n\t";

		$tableDefinition = [];
		foreach ($this->addColumns AS $column)
		{
			$tableDefinition[] = $column->getDefinition();
		}
		foreach ($this->addIndexes AS $index)
		{
			$tableDefinition[$index->getIndexName()] = $index->getDefinition();
		}

		$query .= implode(",\n\t", $tableDefinition);
		$query .= "\n)";
		$query .= ' ' . $this->db->getSchemaManager()->getTableConfigSql($this->engine ?: null);

		if ($this->comment)
		{
			$query .= ' COMMENT=' . $this->db->quote($this->comment);
		}

		$queries[] = $query;

		return $queries;
	}

	protected function setDefinition()
	{
		$db = $this->db;
		$sm = $db->getSchemaManager();
		$tableName = $this->tableName;

		$tableStatus = $sm->getTableStatus($tableName);
		if (isset($tableStatus['Engine']) && $tableStatus['Engine'] != $db->getDefaultTableConfig()['engine'])
		{
			$this->engine = $tableStatus['Engine'];
		}
		if (!empty($tableStatus['Comment']))
		{
			$this->comment = $tableStatus['Comment'];
		}

		$tableColumns = $sm->getTableColumnDefinitions($tableName);
		foreach ($tableColumns AS $tableColumn)
		{
			$column = $this->addColumn($tableColumn['Field']);
			$column->setupFromExistingDefinition($tableColumn);
		}

		$groupedIndexes = $sm->getTableIndexDefinitions($tableName);
		foreach ($groupedIndexes AS $keyName => $indexColumns)
		{
			$index = $this->addIndex($keyName);
			$index->setupFromExistingDefinition($indexColumns);
		}
	}

	protected function compare(Create $existing)
	{
		return ($this->getComparisonValue() == $existing->getComparisonValue());
	}

	public function getComparisonValue()
	{
		$compare = [
			"table: $this->tableName " . $this->db->getSchemaManager()->getTableConfigSql($this->engine ?: null)
		];
		if ($this->comment)
		{
			$compare[] = "comment: $this->comment";
		}

		foreach ($this->addColumns AS $column)
		{
			$compare[] = $column->getComparisonValue();
		}
		foreach ($this->addIndexes AS $index)
		{
			$compare[] = $index->getComparisonValue();
		}

		sort($compare);

		return $compare;
	}

	public function toArray()
	{
		$array = get_object_vars($this);
		unset($array['db']);

		$columns = [];
		foreach ($this->addColumns AS $column)
		{
			$columns[] = $column->toArray();
		}
		$array['addColumns'] = $columns;

		$indexes = [];
		foreach ($this->addIndexes AS $index)
		{
			$indexes[] = $index->toArray();
		}
		$array['addIndexes'] = $indexes;

		return $array;
	}
}