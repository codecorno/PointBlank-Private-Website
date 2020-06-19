<?php

namespace XF\Session;

use XF\Http\Request;
use XF\HTTP\Response;

class Session implements \ArrayAccess
{
	/**
	 * @var StorageInterface
	 */
	protected $storage;

	protected $config = [
		'cookie' => 'session',
		'keyLength' => 32,
		'lifetime' => 14400, // 4 hours by default
		'ipv4CidrMatch' => 24,
		'ipv6CidrMatch' => 64
	];
	protected $sessionId = false;
	protected $exists = false;
	protected $fromCookie = false;
	protected $data = null;

	public function __construct(StorageInterface $storage, array $config = [])
	{
		$this->storage = $storage;
		$this->config = array_merge($this->config, $config);
	}

	public function start($ownerIp, $sessionId = null)
	{
		if ($this->sessionId)
		{
			throw new \LogicException("The session handler cannot be started twice");
		}

		$fromCookie = false;

		if ($ownerIp instanceof Request && $sessionId === null)
		{
			$sessionId = $ownerIp->getCookie($this->getCookieName());
			$ownerIp = $ownerIp->getIp();
			$fromCookie = $sessionId;
		}

		$ownerIp = \XF\Util\Ip::convertIpStringToBinary($ownerIp);

		if ($sessionId)
		{
			$data = $this->storage->getSession($sessionId);
			if (!is_array($data) || !$this->confirmOwnership($ownerIp, $data))
			{
				$data = false;
			}
		}
		else
		{
			$data = false;
		}

		if (is_array($data))
		{
			$this->sessionId = $sessionId;
			$this->data = $data;
			$this->exists = true;
			$this->fromCookie = $fromCookie;
		}
		else
		{
			$this->sessionId = \XF::generateRandomString($this->config['keyLength']);
			$this->data = ['_ip' => $ownerIp];
			$this->exists = false;
		}

		return $this;
	}

	protected function confirmOwnership($expectedIp, array $data)
	{
		if (!isset($data['_ip']) || empty($data['_ip']) || empty($expectedIp))
		{
			return true; // no IP to check against
		}

		if (strlen($expectedIp) == 4)
		{
			$cidr = intval($this->config['ipv4CidrMatch']);
		}
		else
		{
			$cidr = intval($this->config['ipv6CidrMatch']);
		}

		if (empty($data['userId']) || $cidr <= 0)
		{
			return true; // IP check disabled
		}

		return \XF\Util\Ip::ipMatchesCidrRange($expectedIp, $data['_ip'], $cidr);
	}

	public function __get($key)
	{
		if (!$this->sessionId)
		{
			throw new \LogicException("Cannot manipulate data when the session is not started");
		}

		return isset($this->data[$key]) ? $this->data[$key] : null;
	}

	public function offsetGet($key)
	{
		return $this->__get($key);
	}

	public function get($key)
	{
		return $this->__get($key);
	}

	public function __set($key, $value)
	{
		if (!$this->sessionId)
		{
			throw new \LogicException("Cannot manipulate data when the session is not started");
		}

		$this->data[$key] = $value;
	}

	public function offsetSet($key, $value)
	{
		$this->__set($key, $value);
	}

	public function set($key, $value)
	{
		$this->__set($key, $value);
	}

	public function __unset($key)
	{
		if (!$this->sessionId)
		{
			throw new \LogicException("Cannot manipulate data when the session is not started");
		}

		unset($this->data[$key]);
	}

	public function offsetUnset($key)
	{
		$this->__unset($key);
	}

	public function remove($key)
	{
		$this->__unset($key);
	}

	public function __isset($key)
	{
		if (!$this->sessionId)
		{
			throw new \LogicException("Cannot manipulate data when the session is not started");
		}

		return array_key_exists($key, $this->data);
	}

	public function offsetExists($key)
	{
		return $this->__isset($key);
	}

	public function keyExists($key)
	{
		return $this->__isset($key);
	}

	public function hasData()
	{
		$data = $this->data;
		unset($data['_ip']);

		return count($data) > 0;
	}

	public function isStarted()
	{
		return (bool)$this->sessionId;
	}

	public function exists()
	{
		return $this->exists;
	}

	public function getSessionId()
	{
		return $this->sessionId;
	}

	public function save()
	{
		if (!$this->sessionId)
		{
			return false;
		}

		$this->storage->writeSession($this->sessionId, $this->data, $this->config['lifetime'], $this->exists);
		$this->exists = true;

		return true;
	}

	public function expunge()
	{
		if ($this->sessionId && $this->exists)
		{
			$this->storage->deleteSession($this->sessionId);
		}

		$this->sessionId = false;
		$this->exists = false;
		$this->data = null;

		return true;
	}

	public function regenerate($keepExistingData = false)
	{
		if (!$this->sessionId)
		{
			throw new \LogicException("Cannot regenerate when the session is not started");
		}

		$data = $this->data;
		$ip = isset($this->data['_ip']) ? $this->data['_ip'] : null;

		$this->expunge();
		$this->start($ip);

		if ($keepExistingData)
		{
			$this->data = $data;
		}

		return $this;
	}

	public function changeUser(\XF\Entity\User $user)
	{
		$passwordDate = $user->Profile ? $user->Profile->password_date : 0;

		if ($this->exists)
		{
			$this->regenerate(false);
		}

		$this->__set('userId', $user->user_id);
		$this->__set('passwordDate', intval($passwordDate));

		return $this;
	}

	public function logoutUser()
	{
		if ($this->exists)
		{
			$this->regenerate(false);
		}

		$this->__set('userId', 0);
		$this->__unset('passwordDate');

		return $this;
	}

	public function setHasContentPendingApproval($until = null)
	{
		if (!$until || $until < \XF::$time)
		{
			$until = \XF::$time + 3600;
		}

		$this->set('hasContentPendingUntil', $until);
	}

	public function setConfig(array $config)
	{
		$this->config = array_merge($this->config, $config);
	}

	public function getCookieName()
	{
		return $this->config['cookie'];
	}

	public function applyToResponse(Response $response)
	{
		if ($this->fromCookie !== $this->sessionId)
		{
			$response->setCookie($this->config['cookie'], $this->sessionId);
		}
	}
}