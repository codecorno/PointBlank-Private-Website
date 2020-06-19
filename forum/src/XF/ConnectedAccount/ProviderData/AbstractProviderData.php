<?php

namespace XF\ConnectedAccount\ProviderData;

use XF\ConnectedAccount\Storage\StorageState;

abstract class AbstractProviderData implements \ArrayAccess
{
	protected $providerId;

	/**
	 * @var StorageState
	 */
	protected $storageState;

	protected $cache = [];

	abstract public function getDefaultEndpoint();

	abstract public function getProviderKey();

	public function __construct($providerId, StorageState $storageState)
	{
		$this->providerId = $providerId;
		$this->storageState = $storageState;

		$storageState->getProviderToken();
	}

	public function getProviderId()
	{
		return $this->providerId;
	}

	public function getExtraData()
	{
		$storageState = $this->storageState;
		$token = $storageState->getProviderToken();
		$provider = $storageState->getProvider();
		$handler = $provider->handler;

		switch ($version = $handler->getOAuthVersion())
		{
			case 2:
				$extraData = [
					'token' => $token->getAccessToken()
				];
				break;

			case 1:
				$extraData = [
					'token' => $token->getAccessToken(),
					'secret' => $token->getAccessTokenSecret()
				];
				break;

			default:
				throw new \InvalidArgumentException("Unknown OAuth version '$version'");
				break;
		}

		return $extraData;
	}

	public function requestFromEndpoint($key = null, $method = 'GET', $endpoint = null)
	{
		$endpoint = $endpoint ?: $this->getDefaultEndpoint();

		if ($value = $this->requestFromCache($endpoint, $key))
		{
			return $value;
		}

		$storageState = $this->storageState;
		$data = $storageState->retrieveProviderData();
		if ($data && $endpoint == $this->getDefaultEndpoint())
		{
			if ($key === null)
			{
				$value = $data;
			}
			else
			{
				$value = isset($data[$key]) ? $data[$key] : null;
			}
			$this->storeInCache($endpoint, $value, $key);
			return $value;
		}

		$provider = $storageState->getProvider();
		$handler = $provider->handler;

		try
		{
			$config = $handler->getOAuthConfig($provider);
			$config['storageType'] = $storageState->getStorageType();

			$data = $handler->getOAuth($config)->request($endpoint, $method);
			$data = json_decode($data, true);
			$this->storeInCache($endpoint, $data);
			if ($endpoint == $this->getDefaultEndpoint())
			{
				$storageState->storeProviderData($data);
			}
			return $this->requestFromCache($endpoint, $key);
		}
		catch(\Exception $e)
		{
			return null;
		}
	}

	public function requestFromCache($endpoint, $key = null)
	{
		if ($key === null)
		{
			return isset($this->cache[$endpoint]) ? $this->cache[$endpoint] : null;
		}
		else
		{
			return isset($this->cache[$endpoint][$key]) ? $this->cache[$endpoint][$key] : null;
		}
	}

	public function storeInCache($endpoint, $value, $key = null)
	{
		if ($key === null)
		{
			$this->cache[$endpoint] = $value;
		}
		else
		{
			$this->cache[$endpoint][$key] = $value;
		}
	}

	public function prepareBirthday($birthday, $format)
	{
		$format = strtr(preg_quote($format, '#'), [
			'm' => '(?P<month>\d{1,2})',
			'd' => '(?P<day>\d{1,2})',
			'y' => '(?P<year>\d{1,4})',
		]);
		if (!preg_match('#^' . $format . '$#i', $birthday, $match))
		{
			return false;
		}

		$month = intval($match['month']);
		$day = intval($match['day']);
		$year = intval($match['year']);

		if (!$year)
		{
			return false;
		}

		return ['dob_year' => $year, 'dob_month' => $month, 'dob_day' => $day];
	}

	public function offsetExists($offset)
	{
		return $this->offsetGet($offset) !== null;
	}

	public function offsetGet($offset)
	{
		$method = 'get' . \XF\Util\Php::camelCase($offset);
		if (method_exists($this, $method))
		{
			$value = $this->$method();
		}
		else
		{
			$value = $this->requestFromEndpoint($offset);
		}
		return $value ?: null;
	}

	public function offsetSet($offset, $value)
	{
		throw new \LogicException("Cannot set provider data offsets");
	}

	public function offsetUnset($offset)
	{
		throw new \LogicException("Cannot unset provider data offsets");
	}

	function __get($name)
	{
		return $this->offsetGet($name);
	}

	function __set($name, $value)
	{
		$this->offsetSet($name, $value);
	}
}