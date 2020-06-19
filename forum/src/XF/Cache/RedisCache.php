<?php

namespace XF\Cache;

use Redis;

/**
 * This is an extension of the Doctrine Redis cache provider to fix a potential race condition
 * and ambiguity in return types. This handles serialization entirely natively, rather than letting
 * Redis handle it internally.
 */
class RedisCache extends \Doctrine\Common\Cache\RedisCache
{
	const SERIALIZER_PHP = 1;
	const SERIALIZER_IGBINARY = 2;

	/** @var int */
	private $serializer;

	public function setRedis(Redis $redis)
	{
		parent::setRedis($redis);
		$this->getRedis()->setOption(Redis::OPT_SERIALIZER, Redis::SERIALIZER_NONE);
		$this->serializer = $this->getSerializerValue();
	}

	/**
	 * Gets the used serializer
	 *
	 * @return int
	 */
	public function getSerializer()
	{
		return $this->serializer;
	}

	protected function doFetch($id)
	{
		$value = parent::doFetch($id);

		if ($value === false)
		{
			return false;
		}

		return $this->unserialize($value);
	}

	protected function doFetchMultiple(array $keys)
	{
		$redis = $this->getRedis();
		$fetchedItems = array_combine($keys, $redis->mget($keys));

		// Redis mget returns false for keys that do not exist. So we need to filter those out unless it's the real data.
		$foundItems = [];

		foreach ($fetchedItems AS $key => $value)
		{
			if ($value === false)
			{
				continue;
			}

			$foundItems[$key] = $this->unserialize($value);
		}

		return $foundItems;
	}

	protected function doSave($id, $data, $lifeTime = 0)
	{
		$redis = $this->getRedis();

		if ($lifeTime > 0)
		{
			return $redis->setex($id, $lifeTime, $this->serialize($data));
		}

		return $redis->set($id, $this->serialize($data));
	}

	protected function serialize($value)
	{
		if ($this->serializer === self::SERIALIZER_IGBINARY)
		{
			return igbinary_serialize($value);
		}

		return serialize($value);
	}

	protected function unserialize($value)
	{
		if ($this->serializer === self::SERIALIZER_IGBINARY)
		{
			return igbinary_unserialize($value);
		}

		return unserialize($value);
	}

	protected function getSerializerValue()
	{
		if (defined('Redis::SERIALIZER_IGBINARY') && extension_loaded('igbinary'))
		{
			return self::SERIALIZER_IGBINARY;
		}

		return self::SERIALIZER_PHP;
	}
}