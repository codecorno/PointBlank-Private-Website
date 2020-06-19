<?php

namespace XF\Db;

abstract class AbstractStatement
{
	/**
	 * @var AbstractAdapter
	 */
	protected $adapter;

	protected $query;

	protected $params;

	/**
	 * @var array
	 */
	protected $metaFields;

	protected $keys = [];

	protected $values = [];

	abstract public function prepare();

	/**
	 * @return bool
	 * @throws \XF\Db\Exception
	 */
	abstract public function execute();
	abstract public function fetchRowValues();
	abstract public function rowsAffected();
	abstract public function reset();
	abstract protected function closeStatement();

	public function __construct(AbstractAdapter $adapter, $query, $params = [])
	{
		$this->adapter = $adapter;
		$this->query = $query;
		$this->params = is_array($params) ? $params : [$params];
	}

	public function query()
	{
		return $this->query;
	}

	public function params(array $params = null)
	{
		if ($params === null)
		{
			return $this->params;
		}

		$this->params = $params;

		return $this;
	}

	public function fetch()
	{
		$values = $this->fetchRowValues();
		if (!$values)
		{
			return false;
		}

		return array_combine($this->keys, $values);
	}

	public function fetchColumn($key = 0)
	{
		$values = $this->fetchRowValues();
		if (!$values)
		{
			return false;
		}

		if (is_int($key))
		{
			return isset($values[$key]) ? $values[$key] : null;
		}
		else
		{
			$values = array_combine($this->keys, $values);
			return isset($values[$key]) ? $values[$key] : null;
		}
	}

	public function fetchAliasGrouped()
	{
		$values = $this->fetchRowValues();
		if (!$values)
		{
			return false;
		}

		$output = [];
		foreach ($this->metaFields AS $k => $field)
		{
			$table = $field->table ?: '__extra';

			$output[$table][$field->name] = $values[$k];
		}

		return $output;
	}

	public function fetchAll()
	{
		$output = [];
		while ($v = $this->fetch())
		{
			$output[] = $v;
		}

		return $output;
	}

	public function fetchAllNum()
	{
		$output = [];
		while ($v = $this->fetchRowValues())
		{
			$output[] = $v;
		}

		return $output;
	}

	public function fetchAllKeyed($key, $fallbackPrefix = '_default')
	{
		$output = [];
		$i = 0;
		while ($v = $this->fetch())
		{
			if (isset($v[$key]))
			{
				$output[$v[$key]] = $v;
			}
			else
			{
				$fallbackKey = $fallbackPrefix . $i;
				$i++;
				$output[$fallbackKey] = $v;

			}
		}

		return $output;
	}

	public function fetchAllColumn($key = 0)
	{
		$output = [];

		while (($v = $this->fetchColumn($key)) !== false)
		{
			$output[] = $v;
		}

		return $output;
	}

	public function fetchPairs()
	{
		$output = [];
		while ($v = $this->fetchRowValues())
		{
			$output[$v[0]] = $v[1];
		}

		return $output;
	}

	public function close()
	{
		$this->closeStatement();

		$this->metaFields = null;
		$this->keys = [];
		$this->values = [];
	}

	/**
	 * @param string $message
	 * @param int $code
	 * @param null|string $sqlStateCode
	 *
	 * @return Exception
	 */
	protected function getException($message, $code = 0, $sqlStateCode = null)
	{
		$eType = null;
		$class = null;

		if ($sqlStateCode)
		{
			switch ($sqlStateCode)
			{
				case '23000': $eType = 'DuplicateKey'; break;
				case '40001': $eType = 'Deadlock'; break;
				case '42000': $eType = 'InvalidQuery'; break;
			}
		}

		if ($eType)
		{
			$class = 'XF\Db\\' . $eType . 'Exception';
		}
		else if (!$class)
		{
			$class = 'XF\Db\Exception';
		}

		$e = new $class($message, $code);
		$e->sqlStateCode = $sqlStateCode;
		$e->query = $this->query;
		$e->statement = $this;

		return $e;
	}
}