<?php

namespace XF\Db\Mysqli;

class ReplicationAdapter extends \XF\Db\AbstractAdapter implements \XF\Db\ReplicationAdapterInterface
{
	/**
	 * @var \mysqli
	 */
	protected $writeConnection;

	/**
	 * @var \mysqli
	 */
	protected $readConnection;

	/**
	 * @var \mysqli
	 */
	protected $lastConnection;

	protected $forceAllWrite = false;

	protected function getStatementClass()
	{
		return 'XF\Db\Mysqli\Statement';
	}

	/**
	 * @return \mysqli
	 */
	public function getConnection()
	{
		$this->lastConnection = $this->getTypeConnection('last');
		return $this->lastConnection;
	}

	public function closeConnection()
	{
		if ($this->readConnection)
		{
			$this->readConnection->close();
		}

		if ($this->writeConnection)
		{
			$this->writeConnection->close();
		}

		$this->readConnection = null;
		$this->writeConnection = null;
		$this->lastConnection = null;
	}

	public function getConnectionForQuery($query)
	{
		$modifiers = $this->getModifiersFromQuery($query);

		$type = 'write';
		$forceAllWrite = true;

		// if is a select query...
		if (preg_match('/^\s*(\(\s*)?SELECT\s/i', $query))
		{
			$queryStart = substr(ltrim($query), 0, 200);
			$queryEnd = substr($query, -100);

			if (preg_match('/(FOR\s+UPDATE|LOCK\s+IN\s+SHARE\s+MODE)\s*$/i', $queryEnd))
			{
				// relates to updating - treat equivalent to running an UPDATE query
			}
			else if (
				preg_match('/(GET_LOCK|IS_FREE_LOCK|IS_USED_LOCK|RELEASE_ALL_LOCKS|RELEASE_LOCK)\s*\(/i', $queryStart)
			)
			{
				// relates to getting a lock - force from the master but not for future yet
				$type = 'write';
				$forceAllWrite = false;
			}
			else
			{
				// otherwise, just a select query
				$type = 'read';
				$forceAllWrite = false;
			}
		}

		foreach ($modifiers AS $modifier)
		{
			switch ($modifier)
			{
				case 'fromWrite':
					$type = 'write';
					break;

				case 'forceAllWrite':
					$type = 'write';
					$forceAllWrite = true;
					break;

				case 'noForceAllWrite':
					$forceAllWrite = false;
					break;
			}
		}

		if ($forceAllWrite && $this->forceAllWrite !== 'explicit')
		{
			$this->forceToWriteServer('explicit');
		}

		$this->lastConnection = $this->getTypeConnection($type);

		return $this->lastConnection;
	}

	protected function getTypeConnection($type)
	{
		if ($type == 'write' || $this->forceAllWrite)
		{
			if (!$this->writeConnection)
			{
				$writeConfig = !empty($this->config['write']) ? $this->config['write'] : [];
				$this->writeConnection = $this->makeConnection($writeConfig);
			}
			return $this->writeConnection;
		}
		else if ($type == 'read')
		{
			if (!$this->readConnection)
			{
				$readConfig = !empty($this->config['read']) ? $this->config['read'] : [];
				$this->readConnection = $this->makeConnection($readConfig);
			}
			return $this->readConnection;
		}
		else if ($type == 'last')
		{
			if ($this->lastConnection)
			{
				return $this->lastConnection;
			}
			else
			{
				return $this->getTypeConnection('read');
			}
		}
		else
		{
			throw new \InvalidArgumentException("Unknown connection type '$type' requested");
		}
	}

	public function isConnected()
	{
		return $this->lastConnection ? true : false;
	}

	protected function _rawQuery($query)
	{
		return $this->getConnectionForQuery($query)->query($query);
	}

	public function ping()
	{
		return $this->getConnection()->ping();
	}

	public function lastInsertId()
	{
		return $this->getTypeConnection('write')->insert_id;
	}

	public function getServerVersion()
	{
		$version = $this->getTypeConnection('write')->server_version;
		$major = (int) ($version / 10000);
		$minor = (int) ($version % 10000 / 100);
		$revision = (int) ($version % 100);
		return $major . '.' . $minor . '.' . $revision;
	}

	public function getConnectionStats()
	{
		$connection = $this->getTypeConnection('write');
		if (!method_exists($connection, 'get_connection_stats'))
		{
			return null;
		}

		return $connection->get_connection_stats();
	}

	public function escapeString($string)
	{
		return $this->getTypeConnection('write')->real_escape_string($string);
	}

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

	protected function makeConnection(array $config)
	{
		$config = $this->standardizeConfig($config);

		$connection = \mysqli_init();
		$connection->real_connect(
			$config['host'], $config['username'], $config['password'],
			$config['dbname'], $config['port'] ?: 3306, $config['socket']
		);
		$connection->set_charset($this->fullUnicode ? 'utf8mb4' : 'utf8');

		$connection->query("SET @@session.sql_mode='STRICT_ALL_TABLES'");

		return $connection;
	}

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

	public function forceToWriteServer($type = 'implicit')
	{
		if (!$this->forceAllWrite)
		{
			if ($this->readConnection && $this->readConnection !== $this->writeConnection)
			{
				$this->readConnection->close();
			}

			$this->readConnection = $this->writeConnection;
			$this->lastConnection = $this->writeConnection;
		}

		$this->forceAllWrite = $type;
	}

	public function isForcedToWriteServer()
	{
		return (bool)$this->forceAllWrite;
	}

	public function isForcedToWriteServerExplicit()
	{
		return $this->forceAllWrite === 'explicit';
	}

	public function getForceToWriteServerLength()
	{
		return 5;
	}
}