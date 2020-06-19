<?php

namespace XF\ConnectedAccount\Provider;

use XF\Entity\ConnectedAccountProvider;
use XF\ConnectedAccount\Http\HttpResponseException;

class Facebook extends AbstractProvider
{
	public function getOAuthServiceName()
	{
		return 'Facebook';
	}

	public function getDefaultOptions()
	{
		return [
			'app_id' => '',
			'app_secret' => ''
		];
	}

	public function getOAuthConfig(ConnectedAccountProvider $provider, $redirectUri = null)
	{
		return [
			'key' => $provider->options['app_id'],
			'secret' => $provider->options['app_secret'],
			'scopes' => ['email'],
			'redirect' => $redirectUri ?: $this->getRedirectUri($provider)
		];
	}

	public function parseProviderError(HttpResponseException $e, &$error = null)
	{
		$response = json_decode($e->getResponseContent(), true);
		if (is_array($response) && isset($response['error']['message']))
		{
			$e->setMessage($response['error']['message']);
		}
		parent::parseProviderError($e, $error);
	}
}