<?php

namespace XF\Db;

use XF\Db\Schema\AbstractDdl;
use XF\Db\Schema\Alter;

class SchemaManager
{
	protected $db;

	public function __construct(AbstractAdapter $db)
	{
		$this->db = $db;
	}

	public function getTableCharacterSet($table)
	{
		$table = $this->db->fetchRow("SHOW TABLE STATUS LIKE " . $this->db->quote($table));
		if (!$table)
		{
			return null;
		}

		$collation = $table['Collation'];
		if (preg_match('/^([^_]+)_/', $collation, $match))
		{
			return $match[1];
		}
		else
		{
			return $collation;
		}
	}

	public function getTableEngine($table)
	{
		$table = $this->db->fetchRow("SHOW TABLE STATUS LIKE " . $this->db->quote($table));
		if (!$table)
		{
			return null;
		}

		return $table['Engine'];
	}

	public function hasUnicodeMismatch(&$errorType = null, $checkTable = null)
	{
		$tableCharset = $this->getTableCharacterSet($checkTable ?: 'xf_post');
		$config = $this->db->getDefaultTableConfig();
		$expectedCharset = $config['charset'];

		if ($tableCharset == $expectedCharset)
		{
			return false;
		}

		if ($tableCharset == 'utf8mb4')
		{
			// table is utf8mb4, only allowing utf8
			$errorType = 'tight'; // our restrictions are too tight; won't allow valid characters
		}
		else
		{
			// table is utf8, allowing utf8mb4
			$errorType = 'loose'; // our restrictions are too loose; allowing invalid characters
		}

		return false;
	}

	public function getTableConfigSql($forceEngine = null)
	{
		$tableConfig = $this->db->getDefaultTableConfig();
		$engine = strtoupper($forceEngine ?: $tableConfig['engine']);
		$charset = $tableConfig['charset'];
		$collation = $tableConfig['collation'];

		return "ENGINE = {$engine} CHARACTER SET {$charset} COLLATE {$collation}";
	}

	public function getTableStatus($tableName)
	{
		return $this->db->fetchRow('
			SHOW TABLE STATUS WHERE Name = ?
		', $tableName);
	}

	public function getTableColumnDefinitions($tableName)
	{
		return $this->db->fetchAllKeyed('
			SHOW FULL COLUMNS FROM `' . $tableName . '`
		', 'Field');
	}

	public function getTableIndexDefinitions($tableName)
	{
		$indexes = $this->db->fetchAllKeyed('
			SHOW INDEXES FROM `' . $tableName . '`
		', 'Field');

		$grouped = [];
		foreach ($indexes AS $index)
		{
			$grouped[$index['Key_name']][$index['Seq_in_index']] = $index;
		}

		return $grouped;
	}

	public function tableExists($tableName)
	{
		$status = $this->getTableStatus($tableName);

		return $status ? true : false;
	}

	public function columnExists($tableName, $column, &$definition = null)
	{
		if (!$this->tableExists($tableName))
		{
			return false;
		}

		$columns = $this->getTableColumnDefinitions($tableName);
		if (isset($columns[$column]))
		{
			$definition = $columns[$column];

			return true;
		}
		else
		{
			return false;
		}
	}

	/**
	 * @param $tableName
	 * @param \Closure $toApply
	 */
	public function alterTable($tableName, \Closure $toApply)
	{
		$alter = $this->newAlter($tableName);

		try
		{
			$toApply($alter);
			$alter->apply();
		}
		catch (\Exception $e)
		{
			$this->handleException($e, $tableName, $alter);
		}
	}

	/**
	 * @param $oldTableName
	 * @param $newTableName
	 */
	public function renameTable($oldTableName, $newTableName)
	{
		$alter = $this->newAlter($oldTableName)->renameTo($newTableName);

		try
		{
			$alter->apply();
		}
		catch (\Exception $e)
		{
			$this->handleException($e, $oldTableName, $alter);
		}
	}

	/**
	 * @param $tableName
	 * @param \Closure $toApply
	 */
	public function createTable($tableName, \Closure $toApply)
	{
		$create = $this->newTable($tableName);

		try
		{
			$toApply($create);
			$create->apply();
		}
		catch (\Exception $e)
		{
			$this->handleException($e, $tableName, $create);
		}
	}

	/**
	 * @param $tableName
	 * @param \Closure|null $toApply
	 */
	public function dropTable($tableName, \Closure $toApply = null)
	{
		$drop = $this->newDrop($tableName);

		try
		{
			if ($toApply)
			{
				$toApply($drop);
			}
			$drop->apply();
		}
		catch (\Exception $e)
		{
			$this->handleException($e, $tableName, $drop);
		}
	}

	/**
	 * @param $tableName
	 *
	 * @return Schema\Alter
	 */
	public function newAlter($tableName)
	{
		return new Schema\Alter($this->db, $this, $tableName);
	}

	/**
	 * @param $tableName
	 *
	 * @return Schema\Create
	 */
	public function newTable($tableName)
	{
		return new Schema\Create($this->db, $this, $tableName);
	}

	/**
	 * @param $tableName
	 *
	 * @return Schema\Drop
	 */
	public function newDrop($tableName)
	{
		return new Schema\Drop($this->db, $this, $tableName);
	}

	protected function handleException($exception, $tableName, AbstractDdl $ddl)
	{
		// The exception might have some extra useful stuff in it, so include attempt to modify the message directly.

		try
		{
			$reflectionClass = new \ReflectionClass($exception);
			$messageProperty = $reflectionClass->getProperty('message');
			$messageProperty->setAccessible(true);
			$messageProperty->setValue($exception, $tableName . ': ' . $messageProperty->getValue($exception));
		}
		catch (\ReflectionException $ignored)
		{
		}

		throw $exception;
	}
}
