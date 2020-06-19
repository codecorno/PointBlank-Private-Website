<?php

namespace XF\ConnectedAccount\Provider;

use XF\Entity\ConnectedAccountProvider;

class GitHub extends AbstractProvider
{
	public function getOAuthServiceName()
	{
		return 'XF:Service\GitHub';
	}

	public function getProviderDataClass()
	{
		return 'XF:ProviderData\GitHub';
	}

	public function getDefaultOptions()
	{
		return [
			'client_id' => '',
			'client_secret' => ''
		];
	}

	public function getOAuthConfig(ConnectedAccountProvider $provider, $redirectUri = null)
	{
		return [
			'key' => $provider->options['client_id'],
			'secret' => $provider->options['client_secret'],
			'scopes' => ['read:user', 'user:email'],
			'redirect' => $redirectUri ?: $this->getRedirectUri($provider)
		];
	}
}