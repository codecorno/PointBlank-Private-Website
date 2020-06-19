<?php

namespace XF\Db\Schema;

class Column extends AbstractDefinition
{
	protected $newName;

	protected $type;
	protected $length;
	protected $values;
	protected $unsigned = false;
	protected $nullable = false;
	protected $default;
	protected $autoIncrement = false;
	protected $comment;
	protected $after;

	protected function init()
	{
		if ($this->isAlter() && !$this->existingDefinition)
		{
			/** @var Alter $ddl */
			$ddl = $this->ddl;
			$this->existingDefinition = $ddl->getColumnDefinition($this->name);
		}
	}

	public function renameTo($renameTo)
	{
		if (!$this->isAlter())
		{
			throw new \InvalidArgumentException(
				"It is not possible to rename the column named {$this->name} during table create."
			);
		}

		if ($renameTo !== $this->name)
		{
			$this->newName = $renameTo;

			/** @var Alter $ddl */
			$ddl = $this->ddl;
			$ddl->registerColumnRename($this->name, $renameTo);
		}

		return $this;
	}

	protected function conflictRename()
	{
		if (!$this->isAlter())
		{
			throw new \InvalidArgumentException(
				"It is not possible to trigger a conflict rename outside of alters."
			);
		}

		/** @var Alter $ddl */
		$ddl = $this->ddl;

		$conflictName = $this->name . '__conflict';
		if ($ddl->getColumnDefinition($conflictName))
		{
			$i = 2;
			while ($ddl->getColumnDefinition($conflictName . $i))
			{
				$i++;
			}

			$conflictName .= $i;
		}

		$this->renameTo($conflictName);
		$this->nullable(); // we need to ensure that a lack of a default doesn't break things

		$ddl->registerConflictRename($this->name, $conflictName);

		return $conflictName;
	}

	public function isRename()
	{
		return ($this->newName ? true : false);
	}

	public function type($type, $length = null)
	{
		$type = strtoupper($type);

		switch ($type)
		{
			case 'ENUM':
			case 'SET':
				if ($length !== null)
				{
					$this->values($length);
					$length = null;
				}
				break;

			case 'DOUBLE PRECISION':
			case 'DEC':
			case 'FIXED':
			case 'NUMERIC':
				$type = 'DOUBLE';
				$this->unsigned = true;
				break;

			case 'REAL':
				$sqlMode = $this->db->fetchOne('SELECT @@sql_mode');
				if (strpos($sqlMode, 'REAL_AS_FLOAT') !== false)
				{
					$type = 'FLOAT';
				}
				else
				{
					$type = 'DOUBLE';
				}
				$this->unsigned = true;
				break;

			case 'SERIAL': // this is an alias so this shouldn't happen
				$type = 'BIGINT';
				$this->unsigned = true;
				$this->autoIncrement = true;
				break;

			case 'BOOL':
			case 'BOOLEAN':
				$type = 'TINYINT';
				$length = null;
				break;

			case 'TINYINT':
			case 'SMALLINT':
			case 'MEDIUMINT':
			case 'INT':
			case 'BIGINT':
			case 'DECIMAL':
			case 'DOUBLE':
			case 'FLOAT':
				$this->unsigned = true;
				break;
		}

		$this->type = $type;
		if ($length)
		{
			$this->length = $length;
		}

		if ($this->isAlter())
		{
			if (!in_array($type, ['ENUM', 'SET']) && $this->values)
			{
				// if values already set and not an enum/set then remove them
				$this->values = null;
			}

			if (!$this->isIntType($type) && $this->unsigned)
			{
				// if changing column to a non-int type, and unsigned is set, reset it
				$this->unsigned = false;
			}
		}

		return $this;
	}

	public function length($length)
	{
		if (in_array($this->type, ['ENUM', 'SET']))
		{
			$this->values($length);
		}
		else
		{
			$this->length = $length;
		}

		return $this;
	}

	public function values($values)
	{
		if (!is_array($values))
		{
			$values = [$values];
		}
		$this->values = $values;
		
		$this->length = null;

		return $this;
	}

	public function addValues($values)
	{
		if (!is_array($this->values))
		{
			throw new \LogicException("Cannot add values to a column when existing values are not loaded/do not apply");
		}

		if (!is_array($values))
		{
			$values = [$values];
		}
		foreach ($values AS $value)
		{
			if (!in_array($value, $this->values))
			{
				$this->values[] = $value;
			}
		}

		return $this;
	}

	public function removeValues($values)
	{
		if (!is_array($this->values))
		{
			throw new \LogicException("Cannot add values to a column when existing values are not loaded/do not apply");
		}

		if (!is_array($values))
		{
			$values = [$values];
		}
		foreach ($values AS $value)
		{
			$index = array_search($value, $this->values);

			if ($index !== false)
			{
				unset($this->values[$index]);
			}
		}

		$this->values = array_values($this->values);

		return $this;
	}

	public function unsigned($unsigned = null)
	{
		if ($unsigned === null)
		{
			$unsigned = true;
		}
		$this->unsigned = $unsigned;

		return $this;
	}

	public function nullable($nullable = null)
	{
		if ($nullable === null)
		{
			$nullable = true;
		}
		$this->nullable = $nullable;

		return $this;
	}

	public function setDefault($default)
	{
		$this->default = $default;

		return $this;
	}

	public function autoIncrement($autoIncrement = null, $setAsPrimary = true)
	{
		if ($autoIncrement === null)
		{
			$autoIncrement = true;
		}
		$this->autoIncrement = $autoIncrement;

		if ($setAsPrimary)
		{
			$this->ddl->addPrimaryKey($this->name);
		}

		return $this;
	}

	public function primaryKey()
	{
		$this->ddl->addPrimaryKey($this->name);

		return $this;
	}

	public function comment($comment)
	{
		$this->comment = $comment;

		return $this;
	}

	public function after($after)
	{
		$this->after = $after;

		return $this;
	}

	public function drop()
	{
		parent::drop();

		if ($this->isAlter())
		{
			/** @var Alter $ddl */
			$ddl = $this->ddl;
			$ddl->forgetColumn($this->name);
		}
	}

	public function getDefinition($change = false)
	{
		if ($this->isRename() && !$change)
		{
			throw new \LogicException("It is only possible to rename the column '{$this->name}' when changing it");
		}

		$columnName = $this->isRename() ? $this->newName : $this->name;

		$type = $this->type;
		$length = $this->length;
		$values = $this->values;

		/** @var Alter|Create $ddl */
		$ddl = $this->ddl;

		if ($this->drop)
		{
			if ($this->existingDefinition)
			{
				return "DROP `$columnName`";
			}
			else
			{
				return '';
			}
		}

		$definition = '';

		if ($this->isAlter() && $this->existingDefinition && !$change)
		{
			// we should be creating a new column, check if we have an existing column...
			$existing = new static($this->db, $ddl, $this->name, $this->existingDefinition);
			$existing->setDefinition();
			if ($this->compare($existing))
			{
				// ...new column is identical to existing column so skip it
				return '';
			}

			// new column exists but isn't the same schema, so try to adjust it instead
			if ($this->isIntType($type) && $length === null)
			{
				// new column doesn't specify length and is an int so let it be inferred
				$existing->length = null;
			}
			if ($length && $existing->length && $length < $existing->length)
			{
				throw new \LogicException("$columnName already exists in table, but cannot change length");
			}
			if (in_array($type, ['ENUM', 'SET']) && $existing->values != $values)
			{
				throw new \LogicException("$columnName already exists in table, but cannot change enum/set values");
			}

			if ($this->forceChanges)
			{
				// adding a new column but one exists with an unexpected schema, we need to rename it
				$conflictName = $existing->conflictRename();
				$definition .= $existing->getDefinition(true) . ', ';

				$errorMessage = sprintf(
					"Renamed column %s.%s to %s to avoid schema conflict",
					$this->ddl->getTableName(),
					$this->name,
					$conflictName
				);

				\XF::logError($errorMessage, true);
			}
			else
			{
				throw new \LogicException("$columnName already exists in table, but does not have the expected schema");
			}
		}

		if ($change)
		{
			if ($this->isRename())
			{
				if ($this->isAlter() && $ddl->getColumnDefinition($this->newName))
				{
					// the column is being renamed to a name that already exists
					return '';
				}
				$definition .= "CHANGE COLUMN `$this->name` ";
			}
			else if ($this->isAlter())
			{
				$definition .= "MODIFY COLUMN ";
			}
		}
		else
		{
			if ($this->isAlter())
			{
				$definition .= "ADD ";
			}
		}

		if ($this->isAlter() && !$this->existingDefinition && $change)
		{
			throw new \InvalidArgumentException("Column definition '{$this->name}' does not exist therefore it cannot be changed.");
		}

		if (!$type)
		{
			throw new \InvalidArgumentException("Column definition '$columnName' must include a type.");
		}

		$definition .= $this->getColumnDefinitionSql();

		if ($this->isAlter() && $this->after)
		{
			$definition .= " AFTER `$this->after`";
		}

		return $definition;
	}

	protected function getColumnDefinitionSql()
	{
		$columnName = $this->isRename() ? $this->newName : $this->name;
		$type = $this->type;
		$length = $this->length;
		$values = $this->values;
		$default = $this->default;
		$comment = $this->comment;

		$definition = "`$columnName` $type";

		if (in_array($type, ['VARCHAR', 'BINARY', 'VARBINARY']) && !$length)
		{
			throw new \InvalidArgumentException("Column type '$type' for column '$columnName' must include a length.");
		}
		if (in_array($type, ['TINYBLOB', 'MEDIUMBLOB', 'LONGBLOB', 'BLOB', 'TINYTEXT', 'MEDIUMTEXT', 'LONGTEXT', 'TEXT']) && $length)
		{
			$length = 0;
		}
		if ($length && !$this->isIntType($type))
		{
			// note that we ignore the length for any integer type as it doesn't change how they work.
			// different lengths are not incompatibilities
			$definition .= "($length)";
		}

		if (in_array($type, ['ENUM', 'SET']) && !$values)
		{
			throw new \InvalidArgumentException("Column type '$type' for column '$columnName' must include values.");
		}
		if ($values)
		{
			$definition .= '(' . $this->db->quote($values) . ')';
		}

		if ($this->unsigned)
		{
			$definition .= ' UNSIGNED';
		}

		if (!$this->nullable)
		{
			$definition .= ' NOT NULL';
		}
		else if ($default === null)
		{
			$default = 'NULL';
		}

		if ($this->autoIncrement)
		{
			$definition .= ' AUTO_INCREMENT';
		}
		else if ($default !== null)
		{
			if (strtoupper($default) === 'NULL')
			{
				$definition .= ' DEFAULT NULL';
			}
			else
			{
				$definition .= ' DEFAULT ' . $this->db->quote(strval($default));
			}
		}

		if ($comment)
		{
			$definition .= ' COMMENT ' . $this->db->quote(strval($comment));
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

	public function setupFromExistingDefinition(array $definition)
	{
		list ($type, $length, $unsigned) = $this->inferColumnType($definition);
		if ($type !== null)
		{
			$this->type($type);
		}
		if ($length !== null)
		{
			if (is_array($length))
			{
				$this->values = $length;
			}
			else
			{
				$this->length = $length;
			}
		}
		if ($type && $unsigned !== null)
		{
			$this->unsigned = $unsigned;
		}
		if ($definition['Null'] == 'YES')
		{
			$this->nullable = true;
		}
		if ($definition['Default'] !== null)
		{
			$this->default = $definition['Default'];
		}
		if ($definition['Extra'] && $definition['Extra'] == 'auto_increment')
		{
			$this->autoIncrement = true;
		}
		if ($definition['Comment'])
		{
			$this->comment = $definition['Comment'];
		}
	}

	public function resetDefinition()
	{
		$this->type = null;
		$this->length = null;
		$this->values = null;
		$this->unsigned = false;
		$this->nullable = false;
		$this->default = null;
		$this->autoIncrement = false;
		$this->comment = null;

		return $this;
	}

	public function inferColumnType($typeDefinition)
	{
		$type = null;
		$length = null;
		$unsigned = null;

		if (preg_match('/^([a-z0-9]*)(?:\((.*)\))*(?:\s+([a-z0-9]*))*/i', $typeDefinition['Type'], $matches))
		{
			$type = isset($matches[1]) ? $matches[1] : null;
			if ($type)
			{
				if (in_array($type, ['enum', 'set']) && isset($matches[2]))
				{
					$length = str_replace('\'', '', explode(',', $matches[2]));
					unset($matches[2]);
				}
			}
			if (isset($matches[2]))
			{
				$length = $matches[2];
			}
			$unsigned = isset($matches[3]) ? true : false;
		}

		return [$type, $length, $unsigned];
	}

	public function isIntType($type)
	{
		switch (strtolower($type))
		{
			case 'tinyint':
			case 'smallint':
			case 'mediumint':
			case 'int':
			case 'integer':
			case 'bigint':
				return true;
			default:
				return false;
		}
	}

	public function getComparisonValue()
	{
		return 'column: ' . $this->getColumnDefinitionSql();
	}

	public function toArray()
	{
		$array = parent::toArray();
		if ($this->isIntType($array['type']))
		{
			// int type lengths aren't really relevant for comparison so remove
			unset($array['length']);
		}
		return $array;
	}
}