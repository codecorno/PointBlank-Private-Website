<?php

namespace XF\ConnectedAccount\Storage;

use OAuth\Common\Token\TokenInterface;
use OAuth\OAuth1\Token\StdOAuth1Token;
use OAuth\OAuth2\Token\StdOAuth2Token;

use XF\Entity\ConnectedAccountProvider;
use XF\Entity\User;

class StorageState
{
	/**
	 * @var ConnectedAccountProvider
	 */
	protected $provider;

	/**
	 * @var User
	 */
	protected $user;

	protected $storageName;
	protected $storageType = 'local';

	protected $tokenData = [];

	public function __construct(ConnectedAccountProvider $provider, User $user)
	{
		$this->provider = $provider;
		$this->user = $user;
		$handler = $provider->handler;

		$storageName = $handler->getOAuthServiceName();
		if (strpos($storageName, '\\') !== false)
		{
			$parts = explode('\\', $storageName);
			$storageName = end($parts);
		}
		$this->storageName = $storageName;

		$this->storageType = ($this->user->user_id == \XF::visitor()->user_id ? 'session' : 'local');
	}

	public function getProvider()
	{
		return $this->provider;
	}

	public function getStorageName()
	{
		return $this->storageName;
	}

	public function getProviderToken()
	{
		// If a token is already stored in session, retrieve it.
		$token = $this->retrieveToken();
		if ($token)
		{
			return $token;
		}

		// If a user is already associated, create and store the token based on that association.
		$token = $this->getTokenObjectForUser();
		if ($token)
		{
			return $token;
		}

		return false;
	}

	public function hasToken()
	{
		return $this->getStorage()->hasAccessToken($this->storageName);
	}

	public function retrieveToken()
	{
		if ($this->hasToken())
		{
			return $this->getStorage()->retrieveAccessToken($this->storageName);
		}
		return false;
	}

	public function getUserToken()
	{
		$user = $this->user;
		$provider = $this->provider;

		if (!isset($user->ConnectedAccounts[$provider->provider_id]))
		{
			return false;
		}
		$connectedAccount = $user->ConnectedAccounts[$provider->provider_id];
		if (empty($connectedAccount->extra_data['token']))
		{
			return false;
		}
		$this->tokenData = [
			'token' => $connectedAccount->extra_data['token'],
			'secret' => isset($connectedAccount->extra_data['secret'])
				? $connectedAccount->extra_data['secret']
				: null
		];
		return $this->tokenData;
	}

	public function getTokenObjectForUser()
	{
		$provider = $this->provider;
		$handler = $provider->handler;

		$tokenData = $this->getUserToken();
		if (!$tokenData)
		{
			return false;
		}

		switch ($version = $handler->getOAuthVersion())
		{
			case 1:
			case 2:
				/** @var StdOAuth2Token|StdOAuth1Token $tokenObj */
				$class = "\\OAuth\\OAuth{$version}\\Token\\StdOAuth{$version}Token";
				$tokenObj = new $class();
				if ($version === 1)
				{
					$tokenObj->setAccessTokenSecret($tokenData['secret']);
				}
			break;

			default:
				throw new \InvalidArgumentException("Unknown OAuth version '$version'");
				break;
		}

		$tokenObj->setAccessToken($tokenData['token']);
		$this->storeToken($tokenObj);
		return $tokenObj;
	}

	public function storeToken(TokenInterface $token)
	{
		$this->getStorage()->storeAccessToken($this->storageName, $token);
	}

	public function clearToken()
	{
		$this->getStorage()->clearToken($this->storageName);
	}

	public function hasProviderData()
	{
		return $this->getStorage()->hasData($this->storageName);
	}

	public function retrieveProviderData()
	{
		if ($this->hasProviderData())
		{
			return $this->getStorage()->retrieveData($this->storageName);
		}
		return false;
	}

	public function storeProviderData($data)
	{
		$this->getStorage()->storeData($this->storageName, $data);
	}

	public function clearProviderData()
	{
		$this->getStorage()->clearData($this->storageName);
	}
	
	public function getStorage()
	{
		return \XF::app()->oAuth()->storage($this->storageType);
	}

	public function getStorageType()
	{
		return $this->storageType;
	}
}