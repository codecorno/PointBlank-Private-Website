<?php

namespace XF\ConnectedAccount\Storage;

use OAuth\Common\Token\TokenInterface;
use OAuth\Common\Storage\TokenStorageInterface;
use OAuth\Common\Storage\Exception\TokenNotFoundException;
use OAuth\Common\Storage\Exception\AuthorizationStateNotFoundException;

class Local implements TokenStorageInterface
{
	protected $storage = [];

	protected $storageVariableName;
	protected $stateVariableName;
	protected $dataVariableName;

	public function __construct($storageVariableName = 'oauthToken', $stateVariableName = 'oauthState', $dataVariableName = 'oauthData')
	{
		$this->storageVariableName = $storageVariableName;
		$this->stateVariableName = $stateVariableName;
		$this->dataVariableName = $dataVariableName;
	}

	public function retrieveAccessToken($service)
	{
		if ($this->hasAccessToken($service))
		{
			$tokens = $this->getTokens();
			return $tokens[$service];
		}
		throw new TokenNotFoundException('Cannot find token for ' . htmlspecialchars($service) .  ' inside storage.');
	}

	public function storeAccessToken($service, TokenInterface $token)
	{
		$tokens = $this->getTokens();
		$tokens[$service] = $token;
		$this->storage[$this->storageVariableName] = $tokens;

		return $this;
	}

	public function hasAccessToken($service)
	{
		$tokens = $this->getTokens();
		return ($tokens && isset($tokens[$service]) && ($tokens[$service] instanceof TokenInterface));
	}

	public function clearToken($service)
	{
		$tokens = $this->getTokens();

		if ($this->hasAccessToken($service))
		{
			unset($tokens[$service]);
			$this->storage[$this->storageVariableName] = $tokens;
		}

		$this->clearData($service);

		return $this;
	}

	public function clearAllTokens()
	{
		unset($this->storage[$this->storageVariableName]);
		unset($this->storage[$this->dataVariableName]);
		return $this;
	}

	public function retrieveAuthorizationState($service)
	{
		if ($this->hasAuthorizationState($service))
		{
			$states = $this->getStates();
			return $states[$service];
		}
		throw new AuthorizationStateNotFoundException('Cannot find state for ' . htmlspecialchars($service) .  ' inside storage.');
	}

	public function storeAuthorizationState($service, $state)
	{
		$states = $this->getStates();
		$states[$service] = $state;

		$this->storage[$this->stateVariableName] = $states;

		return $this;
	}

	public function hasAuthorizationState($service)
	{
		$states = $this->getStates();
		return ($states && isset($states[$service]));
	}

	public function clearAuthorizationState($service)
	{
		$states = $this->getStates();

		if ($this->hasAuthorizationState($service))
		{
			unset($states[$service]);
			$this->storage[$this->stateVariableName] = $states;
		}

		return $this;
	}

	public function clearAllAuthorizationStates()
	{
		unset($this->storage[$this->storageVariableName]);
		return $this;
	}

	public function retrieveData($service)
	{
		if ($this->hasData($service))
		{
			$data = $this->getData();
			return $data[$service];
		}
		throw new TokenNotFoundException('Cannot find data for ' . htmlspecialchars($service) .  ' inside storage.');
	}

	public function storeData($service, $value)
	{
		$data = $this->getData();
		$data[$service] = $value;
		$this->storage[$this->dataVariableName] = $data;

		return $this;
	}

	public function hasData($service)
	{
		$data = $this->getData();
		return ($data && isset($data[$service]));
	}

	public function clearData($service)
	{
		$data = $this->getData();

		if ($this->hasData($service))
		{
			unset($data[$service]);
			$this->storage[$this->dataVariableName] = $data;
		}
	}

	public function clearAllData()
	{
		unset($this->storage[$this->dataVariableName]);
		return $this;
	}

	/**
	 * Get tokens from \XF\Session\Session
	 *
	 * @return mixed|null
	 */
	public function getTokens()
	{
		return isset($this->storage[$this->storageVariableName]) ? $this->storage[$this->storageVariableName] : [];
	}

	/**
	 * Get data from \XF\Session\Session
	 *
	 * @return mixed|null
	 */
	public function getData()
	{
		return isset($this->storage[$this->dataVariableName]) ? $this->storage[$this->dataVariableName] : [];
	}

	/**
	 * Get states from \XF\Session\Session
	 *
	 * @return mixed|null
	 */
	public function getStates()
	{
		return isset($this->storage[$this->stateVariableName]) ? $this->storage[$this->stateVariableName] : [];
	}
}