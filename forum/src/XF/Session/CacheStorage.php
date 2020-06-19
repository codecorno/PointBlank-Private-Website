<?php

namespace XF\Session;

use Doctrine\Common\Cache\CacheProvider;

class CacheStorage implements StorageInterface
{
	/**
	 * @var CacheProvider
	 */
	protected $cache;

	protected $cacheIdPrefix;

	public function __construct(CacheProvider $cache, $cacheIdPrefix = 'session_')
	{
		$this->cache = $cache;
		$this->cacheIdPrefix = $cacheIdPrefix;
	}

	public function getSession($sessionId)
	{
		return $this->cache->fetch($this->getCacheId($sessionId));
	}

	public function deleteSession($sessionId)
	{
		$this->cache->delete($this->getCacheId($sessionId));
	}

	public function writeSession($sessionId, array $data, $lifetime, $existing)
	{
		$this->cache->save($this->getCacheId($sessionId), $data, $lifetime);
	}

	public function deleteExpiredSessions()
	{
		// this is expected to happen automatically
	}

	public function getCacheIdPrefix()
	{
		return $this->cacheIdPrefix;
	}

	public function setCacheIdPrefix($prefix)
	{
		$this->cacheIdPrefix = $prefix;
	}

	protected function getCacheId($sessionId)
	{
		return $this->cacheIdPrefix . $sessionId;
	}
}