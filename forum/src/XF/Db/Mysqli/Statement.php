<?php

namespace XF\Db\Mysqli;

class Statement extends \XF\Db\AbstractStatement
{
	/**
	 * @var \mysqli_stmt
	 */
	protected $statement;

	/**
	 * @var array
	 */
	protected $metaFields;

	protected $keys = [];

	protected $values = [];

	/**
	 * @return bool
	 * @throws \XF\Db\Exception
	 */
	public function prepare()
	{
		if ($this->statement)
		{
			throw new \LogicException("Statement has already been prepared");
		}

		/** @var \mysqli $connection */
		$connection = $this->adapter->getConnectionForQuery($this->query);

		$this->statement = $connection->prepare($this->query);
		if (!$this->statement)
		{
			throw $this->getException(
				"MySQL statement prepare error [$connection->errno]: $connection->error", $connection->errno, $connection->sqlstate
			);
		}

		return true;
	}

	/**
	 * @return bool
	 * @throws \XF\Db\Exception
	 */
	public function execute()
	{
		if (!$this->statement)
		{
			$this->prepare();
		}

		$statement = $this->statement;

		if ($this->params)
		{
			$bind = [str_repeat('s', count($this->params))];
			foreach ($this->params AS &$param)
			{
				$bind[] =& $param;
			}

			call_user_func_array([$statement, 'bind_param'], $bind);
		}

		$this->adapter->logQueryExecution($this->query, $this->params);
		$success = $statement->execute();
		$this->adapter->logQueryStage('execute');

		if (!$success)
		{
			throw $this->getException(
				"MySQL query error [$statement->errno]: $statement->error", $statement->errno, $statement->sqlstate
			);
		}

		$meta = $statement->result_metadata();
		if ($meta)
		{
			$this->metaFields = $meta->fetch_fields();

			$statement->store_result();

			$keys = [];
			$values = [];
			$refs = [];
			$i = 0;

			foreach ($this->metaFields AS $field)
			{
				$keys[] = $field->name;
				$refs[] = null;
				$values[] =& $refs[$i];

				$i++;
			}

			$this->keys = $keys;
			$this->values = $values;

			call_user_func_array([$statement, 'bind_result'], $this->values);
		}

		$this->adapter->logQueryCompletion();

		return $success;
	}

	/**
	 * @return array|bool
	 * @throws \XF\Db\Exception
	 */
	public function fetchRowValues()
	{
		$statement = $this->statement;
		if (!$statement)
		{
			return false;
		}

		$success = $statement->fetch();

		if ($success === null)
		{
			return false;
		}
		else if ($success === false)
		{
			throw $this->getException(
				"MySQL fetch error [$statement->errno]: $statement->error", $statement->errno, $statement->sqlstate
			);
		}

		// need to dereference these values
		$values = [];
		foreach ($this->values AS $v)
		{
			$values[] = $v;
		}

		return $values;
	}

	/**
	 * @return int|null
	 */
	public function rowsAffected()
	{
		return $this->statement ? $this->statement->affected_rows : null;
	}

	public function reset()
	{
		if (!$this->statement)
		{
			return;
		}

		$this->statement->reset();
	}

	protected function closeStatement()
	{
		if ($this->statement)
		{
			$this->statement->free_result();
			$this->statement->close();
			$this->statement = null;
		}
	}

	/**
	 * @param string $message
	 * @param int    $code
	 * @param null   $sqlStateCode
	 *
	 * @return \XF\Db\Exception
	 */
	protected function getException($message, $code = 0, $sqlStateCode = null)
	{
		if (!$sqlStateCode || $sqlStateCode === '00000')
		{
			// MySQL some times doesn't set a SQLSTATE so change it for common cases
			switch ($code)
			{
				case 1062: $sqlStateCode = '23000'; break; // duplicate key
				case 1064: $sqlStateCode = '42000'; break; // invalid query
				case 1213: $sqlStateCode = '40001'; break; // deadlock
			}
		}

		return parent::getException($message, $code, $sqlStateCode);
	}
}