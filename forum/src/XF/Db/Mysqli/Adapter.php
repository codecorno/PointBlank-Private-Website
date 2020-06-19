<?php

namespace XF\Db\Mysqli;

class Adapter extends \XF\Db\AbstractAdapter
{
	/**
	 * @var \mysqli
	 */
	protected $connection;

	/**
	 * @return string
	 */
	protected function getStatementClass()
	{
		return 'XF\Db\Mysqli\Statement';
	}

	/**
	 * @return \mysqli
	 * @throws \XF\Db\Exception
	 */
	public function getConnection()
	{
		if (!$this->connection)
		{
			$this->connection = $this->makeConnection($this->config);
		}

		return $this->connection;
	}

	public function closeConnection()
	{
		if ($this->isConnected())
		{
			$this->connection->close();
		}
		$this->connection = null;
	}

	/**
	 * @return bool
	 */
	public function isConnected()
	{
		return $this->connection ? true : false;
	}

	/**
	 * @param $query
	 *
	 * @return mixed
	 */
	protected function _rawQuery($query)
	{
		return $this->getConnectionForQuery($query)->query($query);
	}

	/**
	 * @return bool
	 * @throws \XF\Db\Exception
	 */
	public function ping()
	{
		return $this->getConnection()->ping();
	}

	/**
	 * @return mixed
	 */
	public function lastInsertId()
	{
		$this->connect();
		return $this->connection->insert_id;
	}

	/**
	 * @return string
	 */
	public function getServerVersion()
	{
		$this->connect();
		$version = $this->connection->server_version;
		$major = (int) ($version / 10000);
		$minor = (int) ($version % 10000 / 100);
		$revision = (int) ($version % 100);
		return $major . '.' . $minor . '.' . $revision;
	}

	/**
	 * @return array|bool|null
	 */
	public function getConnectionStats()
	{
		$this->connect();
		if (!method_exists($this->connection, 'get_connection_stats'))
		{
			return null;
		}

		return $this->connection->get_connection_stats();
	}

	/**
	 * @param $string
	 *
	 * @return string
	 */
	public function escapeString($string)
	{
		$this->connect();
		return $this->connection->real_escape_string($string);
	}

	/**
	 * @return array
	 */
	public function getDefaultTableConfig()
	{
		$engine = isset($this->config['engine']) ? $this->config['engine'] : 'InnoDB';

		if ($this->fullUnicode)
		{
			return [
				'engine' => $engine,
				'charset' => 'utf8mb4',
				'collation' => 'utf8mb4_general_ci'
			];
		}
		else
		{
			return [
				'engine' => $engine,
				'charset' => 'utf8',
				'collation' => 'utf8_general_ci'
			];
		}
	}

	/**
	 * @param array $config
	 *
	 * @return \mysqli
	 * @throws \XF\Db\Exception
	 */
	protected function makeConnection(array $config)
	{
		$config = $this->standardizeConfig($config);

		$connection = \mysqli_init();

		$isConnected = @$connection->real_connect(
			$config['host'], $config['username'], $config['password'],
			$config['dbname'], $config['port'] ?: 3306, $config['socket']
		);
		if ($isConnected === false || $connection->connect_errno)
		{
			throw new \XF\Db\Exception($connection->connect_error);
		}

		if (isset($config['charset']))
		{
			if (empty($config['charset']))
			{
				$connection->query('SET character_set_results = NULL');
			}
			else
			{
				$connection->set_charset($config['charset']);
			}
		}
		else
		{
			$connection->set_charset($this->fullUnicode ? 'utf8mb4' : 'utf8');
		}

		$connection->query("SET @@session.sql_mode='STRICT_ALL_TABLES'");

		return $connection;
	}

	/**
	 * @param array $config
	 *
	 * @return array
	 */
	protected function standardizeConfig(array $config)
	{
		return array_merge([
			'host' => 'localhost',
			'username' => '',
			'password' => '',
			'dbname' => '',
			'tablePrefix' => '',
			'port' => 3306,
			'socket' => null
		], $config);
	}
}