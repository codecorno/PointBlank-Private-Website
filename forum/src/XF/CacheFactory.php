<?php

namespace XF;

use Doctrine\Common\Cache;

class CacheFactory
{
	protected $namespace = '';

	public function __construct($namespace = '')
	{
		$this->namespace = $namespace;
	}

	public function setNamespace($namespace)
	{
		$this->namespace = $namespace;
	}

	public function getNamespace()
	{
		return $this->namespace;
	}

	/**
	 * @param string|\Closure $provider
	 * @param array $config
	 *
	 * @return Cache\CacheProvider
	 */
	public function create($provider, array $config = [])
	{
		$cache = $this->instantiate($provider, $config);
		$cache->setNamespace($this->namespace);

		return $cache;
	}

	/**
	 * @param string|\Closure $provider
	 * @param array $config
	 *
	 * @return Cache\CacheProvider
	 */
	protected function instantiate($provider, array $config)
	{
		// factory closure
		if ($provider instanceof \Closure)
		{
			return $provider($config);
		}

		// class\name or class\name::method
		if (is_string($provider) && strpos($provider, '\\') !== false)
		{
			$parts = explode('::', $provider);
			if (isset($parts[1]))
			{
				// class::method - assume this is the factory method
				return call_user_func($parts, $config);
			}
			else
			{
				// assume this is the provider itself
				return new $provider($config);
			}
		}

		// provider name only, which we'll map to a method
		if (is_string($provider) && $provider)
		{
			$method = 'create' . $provider . 'Cache';
			if (!is_callable([$this, $method]))
			{
				throw new \InvalidArgumentException("Invalid cache provider '$provider'");
			}

			return $this->$method($config);
		}

		throw new \InvalidArgumentException("Invalid type of cache provider");
	}

	protected function createApcCache(array $config)
	{
		if (!function_exists('apc_fetch'))
		{
			throw new \LogicException("Cannot load APC cache provider without APC");
		}

		return new Cache\ApcCache();
	}

	protected function createFilesystemCache(array $config)
	{
		if (empty($config['directory']))
		{
			throw new \LogicException("Filesystem cache config must define a 'directory'");
		}

		$cache = new Cache\FilesystemCache($config['directory'], '.cache');

		$cleanFrequency = max(!empty($config['clean']) ? intval($config['clean']) : 1000, 2);

		if (rand(1, $cleanFrequency) == 1)
		{
			$this->filesystemCacheCleaner($config['directory'], '.cache');
		}

		return $cache;
	}

	protected function filesystemCacheCleaner($directory, $extension)
	{
		if (!is_dir($directory))
		{
			return;
		}

		$time = time();

		$files = \XF\Util\File::getRecursiveDirectoryIterator($directory);
		foreach ($files AS $name => $file)
		{
			if ($file->isDir())
			{
				// this will only work for an empty directory
				@rmdir($name);
			}
			else if (strrpos($name, $extension) === (strlen($name) - strlen($extension)))
			{
				// if the extension matches, we will read its lifetime value and remove the file if it's too old
				$resource = @fopen($name, "r");
				if ($resource && ($line = fgets($resource)) !== false)
				{
					$lifetime = (int)$line;
				}
				else
				{
					$lifetime = 0;
				}
				@fclose($resource);

				if ($lifetime !== 0 && $lifetime < $time)
				{
					@unlink($name);
				}
			}
		}
	}

	protected function createMemcachedCache(array $config)
	{
		if (!class_exists('Memcached'))
		{
			throw new \LogicException("Cannot load Memcached cache provider without Memcached");
		}

		$m = new \Memcached();
		$m->setOption(\Memcached::OPT_DISTRIBUTION, \Memcached::DISTRIBUTION_CONSISTENT);
		$m->setOption(\Memcached::OPT_HASH, \Memcached::HASH_MD5);
		$m->setOption(\Memcached::OPT_LIBKETAMA_COMPATIBLE, true);

		if (!empty($config['servers']))
		{
			$m->addServers($config['servers']);
		}
		else if (!empty($config['server']))
		{
			$server = $config['server'];
			if (is_array($server))
			{
				$m->addServers($server);
			}
			else
			{
				$m->addServer($server, 11211);
			}
		}
		else
		{
			$m->addServer('localhost', 11211);
		}

		if (!empty($config['custom']) && $config['custom'] instanceof \Closure)
		{
			$custom = $config['custom'];
			$custom($m);
		}

		$cache = new \XF\Cache\MemcachedCache();
		$cache->setMemcached($m);
		return $cache;
	}

	protected function createRedisCache(array $config)
	{
		if (!class_exists('Redis'))
		{
			throw new \LogicException("Cannot load Redis cache provider without Redis");
		}

		$config = array_replace([
			'host' => '',
			'port' => 6379,
			'timeout' => 0.0,
			'password' => '',
			'database' => 0,
			'persistent' => false,
			'persistent_id' => ''
		], $config);

		$r = new \Redis();

		if ($config['persistent'] && $config['persistent_id'])
		{
			$r->pconnect($config['host'], $config['port'], $config['timeout'], $config['persistent_id']);
		}
		else if ($config['persistent'])
		{
			$r->pconnect($config['host'], $config['port'], $config['timeout']);
		}
		else
		{
			$r->connect($config['host'], $config['port'], $config['timeout']);
		}

		if ($config['password'])
		{
			$r->auth($config['password']);
		}

		if ($config['database'])
		{
			$r->select($config['database']);
		}

		$cache = new \XF\Cache\RedisCache();
		$cache->setRedis($r);
		return $cache;
	}

	protected function createVoidCache(array $config)
	{
		return new Cache\VoidCache();
	}

	protected function createWinCacheCache(array $config)
	{
		if (!function_exists('wincache_ucache_get'))
		{
			throw new \LogicException("Cannot load WinCache cache provider without WinCache");
		}

		return new Cache\WinCacheCache();
	}

	protected function createXCacheCache(array $config)
	{
		if (!function_exists('xcache_get'))
		{
			throw new \LogicException("Cannot load XCache cache provider without XCache");
		}

		return new Cache\XcacheCache();
	}
}