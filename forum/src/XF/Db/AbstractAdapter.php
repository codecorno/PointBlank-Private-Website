<?php

namespace XF\Db;

abstract class AbstractAdapter
{
	const ALLOW_DEADLOCK_RERUN = 1;

	protected $statementClass;

	protected $config;

	protected $fullUnicode;

	protected $inTransaction = false;
	protected $savePointCounter = 0;
	protected $savePoints = [];

	protected $queryCount = 0;

	protected $logQueries = false;
	protected $logSimpleOnly = null;
	protected $queryLog = [];

	protected $ignoreLegacyTableWriteError = false;

	public static $legacyTables = ['xf_liked_content'];

	/**
	 * @var SchemaManager
	 */
	protected $schemaManager;

	abstract protected function getStatementClass();
	abstract protected function _rawQuery($query);
	abstract protected function standardizeConfig(array $config);

	/**
	 * @return mixed
	 */
	abstract public function getConnection();

	abstract public function isConnected();
	abstract public function ping();
	abstract public function lastInsertId();
	abstract public function getServerVersion();
	abstract public function getConnectionStats();
	abstract public function escapeString($string);
	abstract public function getDefaultTableConfig();

	public function __construct(array $config, $fullUnicode = false)
	{
		$this->config = $config;
		$this->fullUnicode = $fullUnicode;
		$this->statementClass = $this->getStatementClass();
	}

	public function connect()
	{
		$this->getConnection();
		return true;
	}

	public function getConnectionForQuery($query)
	{
		return $this->getConnection();
	}

	public function closeConnection()
	{
		return;
	}

	/**
	 * @param $query
	 * @param array $params
	 *
	 * @return AbstractStatement
	 * @throws Exception
	 */
	public function query($query, $params = [])
	{
		$this->connect();

		if (!empty($this->config['tablePrefix']))
		{
			$query = $this->prependPrefixToTables($this->config['tablePrefix'], $query);
		}

		$class = $this->statementClass;

		/** @var AbstractStatement $statement */
		$statement = new $class($this, $query, $params);
		$statement->execute();

		return $statement;
	}

	protected function prependPrefixToTables($prefix, $query)
	{
		if ($prefix == '')
		{
			return $query;
		}

		if (in_array('noPrefix', $this->getModifiersFromQuery($query)))
		{
			return $query;
		}

		return preg_replace('/((?:\s|^)(?:UPDATE|INTO|FROM|JOIN|STRAIGHT_JOIN)\s)([a-z0-9_-]+(?:\s|$))/siU', '$1' . $prefix . '$2$3', $query);
	}

	protected function getModifiersFromQuery(&$query)
	{
		$modifiers = [];

		// strip any leading comments then search ones that start with XFDB= to find modifiers
		if (preg_match('#^((\s*(--[^\n]*\n|\\#[^\n]*\n|/\*.*?\*/))+)\s*#si', $query, $match))
		{
			$query = substr($query, strlen($match[0]));

			preg_match_all(
				'#--([^\n]*)\n|\\#([^\n]*)\n|/\*(.*?)\*/#',
				$match[1],
				$comments,
				PREG_SET_ORDER
			);

			foreach ($comments AS $comment)
			{
				$content = trim($comment[1] ?: $comment[2] ?: $comment[3] ?: '');
				if (substr($content, 0, 5) == 'XFDB=' && strlen($content) > 5)
				{
					$modifiers = array_merge($modifiers, explode(',', substr($content, 5)));
				}
			}
		}

		return $modifiers;
	}

	public function fetchRow($query, $params = [])
	{
		return $this->query($query, $params)->fetch();
	}

	public function fetchOne($query, $params = [], $column = 0)
	{
		return $this->query($query, $params)->fetchColumn($column);
	}

	public function fetchAll($query, $params = [])
	{
		return $this->query($query, $params)->fetchAll();
	}

	public function fetchAllNum($query, $params = [])
	{
		return $this->query($query, $params)->fetchAllNum();
	}

	public function fetchAllKeyed($query, $key, $params = [])
	{
		return $this->query($query, $params)->fetchAllKeyed($key);
	}

	public function fetchAllColumn($query, $params = [], $column = 0)
	{
		return $this->query($query, $params)->fetchAllColumn($column);
	}

	public function fetchPairs($query, $params = [])
	{
		return $this->query($query, $params)->fetchPairs();
	}

	/**
	 * @param $query
	 *
	 * @return mixed
	 */
	public function rawQuery($query)
	{
		$this->logQueryExecution($query);
		$res = $this->_rawQuery($query);
		$this->logQueryCompletion();

		return $res;
	}

	public function insert($table, array $rawValues, $replaceInto = false, $onDupe = false, $modifier = '')
	{
		if (!$rawValues)
		{
			throw new \InvalidArgumentException('Values must be provided to insert');
		}

		$cols = [];
		$sqlValues = [];
		$bind = [];
		foreach ($rawValues AS $key => $value)
		{
			$cols[] = "`$key`";
			$bind[] = $value;
			$sqlValues[] = '?';
		}

		$keyword = ($replaceInto ? 'REPLACE' : 'INSERT');
		if ($replaceInto)
		{
			$onDupe = false;
		}

		try
		{
			$res = $this->query(
				"$keyword $modifier INTO `$table` (" . implode(', ', $cols) . ') VALUES '
				. '(' . implode(', ', $sqlValues) . ')'
				. ($onDupe ? " ON DUPLICATE KEY UPDATE $onDupe" : ''),
				$bind
			);
			return $res->rowsAffected();
		}
		catch (Exception $e)
		{
			return $this->processDbWriteException($table, $e);
		}
	}

	public function insertBulk($table, array $rows, $replaceInto = false, $onDupe = false, $modifier = '')
	{
		if (!$rows)
		{
			throw new \InvalidArgumentException('Rows must be provided to bulk insert');
		}

		$firstRow = reset($rows);
		$cols = array_keys($firstRow);

		$rowSql = [];
		foreach ($rows AS $row)
		{
			$values = [];
			foreach ($cols AS $col)
			{
				if (!array_key_exists($col, $row))
				{
					throw new \InvalidArgumentException("Row missing column $col in bulk insert");
				}

				$values[] = $this->quote($row[$col]);
			}

			$rowSql[] = '(' . implode(',', $values) . ')';
		}

		foreach ($cols AS &$col)
		{
			$col = "`$col`";
		}

		$keyword = ($replaceInto ? 'REPLACE' : 'INSERT');
		if ($replaceInto)
		{
			$onDupe = false;
		}

		try
		{
			$res = $this->query(
				"$keyword $modifier INTO `$table` (" . implode(', ', $cols) . ') VALUES '
				. implode(",\n", $rowSql)
				. ($onDupe ? " ON DUPLICATE KEY UPDATE $onDupe" : '')
			);
			return $res->rowsAffected();
		}
		catch (Exception $e)
		{
			return $this->processDbWriteException($table, $e);
		}
	}

	public function delete($table, $where, $params = [], $modifier = '', $order = '', $limit = 0)
	{
		try
		{
			$res = $this->query(
				"DELETE $modifier FROM `$table` WHERE " . ($where ? $where : '1=1')
				. ($order ? " ORDER BY $order" : '')
				. ($limit ? ' LIMIT ' . intval($limit) : ''),
				$params
			);
			return $res->rowsAffected();
		}
		catch (Exception $e)
		{
			return $this->processDbWriteException($table, $e);
		}
	}

	public function update($table, array $cols, $where, $params = [], $modifier = '', $order = '', $limit = 0)
	{
		if (!$cols)
		{
			return 0;
		}

		$sqlValues = [];
		$bind = [];
		foreach ($cols AS $col => $value)
		{
			$bind[] = $value;
			$sqlValues[] = "`$col` = ?";
		}

		$bind = array_merge($bind, is_array($params) ? $params : [$params]);

		try
		{
			$res = $this->query(
				"UPDATE $modifier `$table` SET " . implode(', ', $sqlValues)
				. ' WHERE ' . ($where ? $where : '1=1')
				. ($order ? " ORDER BY $order" : '')
				. ($limit ? ' LIMIT ' . intval($limit) : ''),
				$bind
			);
			return $res->rowsAffected();
		}
		catch (Exception $e)
		{
			return $this->processDbWriteException($table, $e);
		}
	}

	public function emptyTable($table)
	{
		$method = isset($this->config['emptyMethod']) ? $this->config['emptyMethod'] : 'TRUNCATE';
		switch (strtoupper($method))
		{
			case 'TRUNCATE':
				$query = "TRUNCATE TABLE `$table`";
				break;

			case 'DELETE':
				$query = "DELETE FROM `$table`";
				break;

			default:
				throw new \InvalidArgumentException("Unknown emptyMethod '$method'.");
		}

		try
		{
			$res = $this->query($query);
			return $res->rowsAffected();
		}
		catch (Exception $e)
		{
			return $this->processDbWriteException($table, $e);
		}
	}

	protected function processDbWriteException($table, Exception $e)
	{
		if ($this->ignoreLegacyTableWriteError && in_array($table, self::$legacyTables))
		{
			\XF::logException($e, false, "Ignored write to legacy table $table. Code update required. ");
			return 0;
		}

		throw $e;
	}

	public function beginTransaction()
	{
		$this->connect();

		if (!$this->inTransaction)
		{
			$this->rawQuery('BEGIN');
			$this->inTransaction = true;
		}
		else
		{
			$savepoint = 'save' . ++$this->savePointCounter;
			$this->rawQuery("SAVEPOINT $savepoint");

			$this->savePoints[] = $savepoint;
		}
	}

	public function commit()
	{
		if ($this->inTransaction)
		{
			if ($this->savePoints)
			{
				$this->rawQuery('RELEASE SAVEPOINT ' . array_pop($this->savePoints));
			}
			else
			{
				$this->rawQuery('COMMIT');
				$this->inTransaction = false;
			}
		}
	}

	public function commitAll()
	{
		if ($this->inTransaction)
		{
			$this->rawQuery('COMMIT');

			$this->inTransaction = false;
			$this->savePoints = [];
		}
	}

	public function rollback()
	{
		if ($this->inTransaction)
		{
			if ($this->savePoints)
			{
				$this->rawQuery('ROLLBACK TO SAVEPOINT ' . array_pop($this->savePoints));
			}
			else
			{
				$this->rawQuery('ROLLBACK');
				$this->inTransaction = false;
			}
		}
	}

	public function rollbackAll()
	{
		if ($this->inTransaction)
		{
			$this->rawQuery('ROLLBACK');

			$this->inTransaction = false;
			$this->savePoints = [];
		}
	}

	public function executeTransaction(callable $execute, $options = 0)
	{
		$startedInTransaction = $this->inTransaction;

		$this->beginTransaction();

		try
		{
			$result = $execute($this);
		}
		catch (\XF\Db\DeadlockException $e)
		{
			$this->rollback();

			if (!$startedInTransaction && $options & self::ALLOW_DEADLOCK_RERUN)
			{
				// deadlock detected, try rerunning once
				$result = $this->executeTransaction($execute, $options & ~self::ALLOW_DEADLOCK_RERUN);
			}
			else
			{
				throw $e;
			}
		}
		catch (\Exception $e)
		{
			$this->rollback();
			throw $e;
		}

		$this->commit();

		return $result;
	}

	public function inTransaction()
	{
		return $this->inTransaction;
	}

	public function quote($data, $type = null)
	{
		if (is_array($data))
		{
			$output = [];
			foreach ($data AS $value)
			{
				$output[] = $this->quote($value);
			}
			return implode(', ', $output);
		}

		if (!$type)
		{
			$type = gettype($data);
		}

		switch (strtolower($type))
		{
			case 'integer':
				return strval(floor($data));
			case 'double':
				return strval($data + 0);
			case 'boolean':
				return ($data ? '1' : '0');
			case 'null':
				return 'NULL';

			default:
				return "'" . $this->escapeString($data) . "'";
		}
	}

	public function escapeLike($data, $format = null)
	{
		if (is_array($data))
		{
			$output = [];
			foreach ($data AS $value)
			{
				$output[] = $this->escapeLike($value, $format);
			}
			return implode(', ', $output);
		}

		$data = strval($data);
		$data = strtr($data, [
			'_' => '\\_',
			'%' => '\\%'
		]);
		if ($format)
		{
			$data = str_replace('?', $data, $format);
		}

		return $data;
	}

	public function limit($query, $amount, $offset = 0)
	{
		$offset = max(0, intval($offset));

		if ($amount === null)
		{
			if (!$offset)
			{
				// no limit
				return $query;
			}

			// no amount limit, but there's an offset
			$amount = 1000000;
		}
		$amount = max(1, intval($amount));

		return "$query\nLIMIT $amount" . ($offset ? " OFFSET $offset" : '');
	}

	public function logQueries($value, $simpleOnly = null)
	{
		$this->logQueries = (bool)$value;
		$this->logSimpleOnly = $simpleOnly;
	}

	public function getUtf8Type()
	{
		return $this->fullUnicode ? 'utf8mb4' : 'utf8';
	}

	public function logSimpleOnly($value = true)
	{
		$this->logSimpleOnly = $value;
	}

	public function areQueriesLogged()
	{
		return $this->logQueries;
	}

	public function logQueryExecution($query, array $params = [])
	{
		$this->queryCount++;

		if ($this->logQueries)
		{
			if ($this->logSimpleOnly)
			{
				$trace = null;
			}
			else
			{
				$trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
				array_shift($trace);
			}

			$this->queryLog[$this->queryCount] = [
				'query' => $query,
				'params' => $this->logSimpleOnly ? null : $params,
				'start' => microtime(true),
				'trace' => $trace
			];

			if ($this->queryCount >= 150 && $this->logSimpleOnly === null)
			{
				// we haven't specified that we want full details, so switch to reduce memory usage
				$this->logSimpleOnly = true;
			}
		}

		return $this->queryCount;
	}

	public function logQueryStage($stage, $queryId = null)
	{
		if (!$this->logQueries)
		{
			return;
		}

		if (!$queryId)
		{
			$queryId = $this->queryCount;
		}
		if (!isset($this->queryLog[$queryId]))
		{
			return;
		}

		$this->queryLog[$queryId][$stage] = microtime(true);
	}

	public function logQueryCompletion($queryId = null)
	{
		if (!$this->logQueries)
		{
			return;
		}

		$this->logQueryStage('complete', $queryId);
	}

	public function getQueryCount()
	{
		return $this->queryCount;
	}

	public function getQueryLog()
	{
		return $this->queryLog;
	}

	public function ignoreLegacyTableWriteError($ignore)
	{
		$this->ignoreLegacyTableWriteError = (bool)$ignore;
	}

	public function getIgnoreLegacyTableWriteError()
	{
		return $this->ignoreLegacyTableWriteError;
	}

	public function getSchemaManager()
	{
		if ($this->schemaManager === null)
		{
			$this->schemaManager = new SchemaManager($this);
		}

		return $this->schemaManager;
	}

	public function __sleep()
	{
		throw new \LogicException('Instances of ' . __CLASS__ . ' cannot be serialized or unserialized');
	}

	public function __wakeup()
	{
		throw new \LogicException('Instances of ' . __CLASS__ . ' cannot be serialized or unserialized');
	}
}