<?php

namespace XF;

use Doctrine\Common\Cache\CacheProvider;
use XF\Db\AbstractAdapter;
use XF\Util\Json;

class DataRegistry implements \ArrayAccess
{
	/**
	 * @var AbstractAdapter
	 */
	protected $db;

	/**
	 * @var CacheProvider|null
	 */
	protected $cache;

	protected $cacheIdPrefix = 'data_';
	protected $cacheLifeTime = 3600;

	protected $localData = [];

	public function __construct(AbstractAdapter $db, CacheProvider $cache = null)
	{
		$this->db = $db;
		$this->cache = $cache;
	}

	public function getCacheIdPrefix()
	{
		return $this->cacheIdPrefix;
	}

	public function setCacheIdPrefix($prefix)
	{
		$this->cacheIdPrefix = $prefix;
	}

	public function getCacheLifeTime()
	{
		return $this->cacheLifeTime;
	}

	public function setCacheLifeTime($lifeTime)
	{
		$this->cacheLifeTime = $lifeTime;
	}

	public function get($keys)
	{
		if (!is_array($keys))
		{
			$keys = [$keys];
			$isMulti = false;
		}
		else
		{
			if (!$keys)
			{
				return [];
			}

			$isMulti = true;
		}

		$data = [];
		$originalKeys = $keys;
		foreach ($keys AS $i => $key)
		{
			if (array_key_exists($key, $this->localData))
			{
				$data[$key] = $this->localData[$key];
				unset($keys[$i]);
			}
		}

		if ($keys)
		{
			$remainingKeys = $this->readFromCache($keys, $data);
			$this->readFromDb($remainingKeys, $data);
		}

		if ($isMulti)
		{
			return $data;
		}
		else
		{
			return $data[reset($originalKeys)];
		}
	}

	public function exists($key)
	{
		return ($this->get($key) !== null);
	}

	protected function readFromCache(array $keys, array &$data)
	{
		if (!$this->cache || !$keys)
		{
			return $keys;
		}

		$lookups = [];
		foreach ($keys AS $i => $key)
		{
			$lookups[$this->getCacheId($key)] = [$i, $key];
		}

		$results = $this->cache->fetchMultiple(array_keys($lookups));
		foreach ($results AS $cacheKey => $value)
		{
			$keyId = $lookups[$cacheKey][0];
			$keyName = $lookups[$cacheKey][1];
			unset($keys[$keyId]); // don't need to read from the DB

			$data[$keyName] = $value;
			$this->localData[$keyName] = $data[$keyName];
		}

		return $keys;
	}

	protected function readFromDb(array $keys, array &$data)
	{
		if (!$keys)
		{
			return;
		}

		$pairs = $this->db->fetchPairs("
			SELECT data_key, data_value
			FROM xf_data_registry
			WHERE data_key IN (" . $this->db->quote($keys) . ")
		");
		foreach ($keys AS $key)
		{
			$exists = false;

			if (isset($pairs[$key]))
			{
				$value = @unserialize($pairs[$key]);
				if ($value !== false || $pairs[$key] === 'b:0;')
				{
					$data[$key] = $value;
					$exists = true;
				}
			}

			if ($exists)
			{
				// populate the cache on demand
				$this->setInCache($key, $data[$key]);
			}
			else
			{
				$data[$key] = null;
			}

			$this->localData[$key] = $data[$key];
		}
	}

	public function set($key, $value)
	{
		$this->db->query("
			INSERT INTO xf_data_registry
				(data_key, data_value)
			VALUES
				(?, ?)
			ON DUPLICATE KEY UPDATE
				data_value = VALUES(data_value)
		", [$key, serialize($value)]);

		$this->setInCache($key, $value);

		$this->localData[$key] = $value;
	}

	protected function setInCache($key, $value)
	{
		if ($this->cache)
		{
			$this->cache->save($this->getCacheId($key), $value, $this->cacheLifeTime);
		}
	}

	public function delete($keys)
	{
		if (!is_array($keys))
		{
			$keys = [$keys];
		}
		else if (!$keys)
		{
			return;
		}

		$this->db->delete('xf_data_registry', 'data_key IN (' . $this->db->quote($keys) . ')');

		if ($this->cache)
		{
			foreach ($keys AS $key)
			{
				$this->cache->delete($this->getCacheId($key));
			}
		}

		foreach ($keys AS $key)
		{
			$this->localData[$key] = null;
		}
	}

	protected function getCacheId($key)
	{
		return $this->cacheIdPrefix . $key;
	}

	public function offsetGet($key)
	{
		return $this->get($key);
	}

	public function offsetSet($key, $value)
	{
		$this->set($key, $value);
	}

	public function offsetUnset($key)
	{
		$this->delete($key);
	}

	public function offsetExists($key)
	{
		return $this->exists($key);
	}
}