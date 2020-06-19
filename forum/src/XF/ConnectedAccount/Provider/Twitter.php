<?php

namespace XF\ConnectedAccount\Provider;

use XF\Entity\ConnectedAccountProvider;
use XF\ConnectedAccount\Http\HttpResponseException;

class Twitter extends AbstractProvider
{
	public function __construct($providerId)
	{
		parent::__construct($providerId);
		$this->oAuthVersion = 1;
	}

	public function getOAuthServiceName()
	{
		return 'Twitter';
	}

	public function getDefaultOptions()
	{
		return [
			'consumer_key' => '',
			'consumer_secret' => ''
		];
	}

	public function getOAuthConfig(ConnectedAccountProvider $provider, $redirectUri = null)
	{
		return [
			'key' => $provider->options['consumer_key'],
			'secret' => $provider->options['consumer_secret'],
			'scopes' => [],
			'redirect' => $redirectUri ?: $this->getRedirectUri($provider)
		];
	}

	public function parseProviderError(HttpResponseException $e, &$error = null)
	{
		$errors = json_decode($e->getResponseContent(), true);
		if (is_array($errors) && isset($errors['errors']))
		{
			foreach ($errors['errors'] AS $errorDetails)
			{
				if (isset($errorDetails['message']))
				{
					$e->setMessage($errorDetails['message']);
					break;
				}
			}
		}
		else if (is_string($e->getResponseContent()))
		{
			$e->setMessage($e->getResponseContent());
		}
		parent::parseProviderError($e, $error);
	}
}