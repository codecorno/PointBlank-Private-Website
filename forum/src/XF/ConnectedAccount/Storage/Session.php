<?php

namespace XF\ConnectedAccount\Storage;

use OAuth\Common\Token\TokenInterface;
use OAuth\Common\Storage\TokenStorageInterface;
use OAuth\Common\Storage\Exception\TokenNotFoundException;
use OAuth\Common\Storage\Exception\AuthorizationStateNotFoundException;

class Session implements TokenStorageInterface
{
	/**
	 * @var \XF\Session\Session
	 */
	protected $session;

	protected $sessionVariableName;
	protected $stateVariableName;
	protected $dataVariableName;

	public function __construct(\XF\Session\Session $session, $sessionVariableName = 'oauthToken', $stateVariableName = 'oauthState', $dataVariableName = 'oauthData')
	{
		$this->session = $session;
		$this->sessionVariableName = $sessionVariableName;
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
		throw new TokenNotFoundException('Cannot find token for ' . htmlspecialchars($service) .  ' inside \XF\Session\Session');
	}

	public function storeAccessToken($service, TokenInterface $token)
	{
		$tokens = $this->getTokens();
		$tokens[$service] = $token;
		$this->session[$this->sessionVariableName] = $tokens;

		$this->session->save();

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
			$this->session[$this->sessionVariableName] = $tokens;
			$this->session->save();
		}

		$this->clearData($service);

		return $this;
	}

	public function clearAllTokens()
	{
		$this->session->remove($this->sessionVariableName);
		$this->session->remove($this->dataVariableName);
		$this->session->save();
		return $this;
	}

	public function retrieveAuthorizationState($service)
	{
		if ($this->hasAuthorizationState($service))
		{
			$states = $this->getStates();
			return $states[$service];
		}
		throw new AuthorizationStateNotFoundException('Cannot find state for ' . htmlspecialchars($service) .  ' inside \XF\Session\Session');
	}

	public function storeAuthorizationState($service, $state)
	{
		$states = $this->getStates();
		$states[$service] = $state;

		$this->session[$this->stateVariableName] = $states;
		$this->session->save();

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
			$this->session[$this->stateVariableName] = $states;
			$this->session->save();
		}

		return $this;
	}

	public function clearAllAuthorizationStates()
	{
		$this->session->remove($this->stateVariableName);
		$this->session->save();
		return $this;
	}

	public function retrieveData($service)
	{
		if ($this->hasData($service))
		{
			$data = $this->getData();
			return $data[$service];
		}
		throw new TokenNotFoundException('Cannot find data for ' . htmlspecialchars($service) .  ' inside \XF\Session\Session');
	}

	public function storeData($service, $value)
	{
		$data = $this->getData();
		$data[$service] = $value;
		$this->session[$this->dataVariableName] = $data;

		$this->session->save();

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
			$this->session[$this->dataVariableName] = $data;
			$this->session->save();
		}
	}

	public function clearAllData()
	{
		$this->session->remove($this->dataVariableName);
		$this->session->save();
		return $this;
	}

	/**
	 * Get tokens from \XF\Session\Session
	 *
	 * @return mixed|null
	 */
	public function getTokens()
	{
		$tokens = $this->session[$this->sessionVariableName];
		return $tokens ?: [];
	}

	/**
	 * Get data from \XF\Session\Session
	 *
	 * @return mixed|null
	 */
	public function getData()
	{
		$data = $this->session[$this->dataVariableName];
		return $data ?: [];
	}

	/**
	 * Get states from \XF\Session\Session
	 *
	 * @return mixed|null
	 */
	public function getStates()
	{
		$states = $this->session[$this->stateVariableName];
		return $states ?: [];
	}
}