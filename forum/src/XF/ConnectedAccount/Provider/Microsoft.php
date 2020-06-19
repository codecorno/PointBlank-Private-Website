<?php

namespace XF\ConnectedAccount\Provider;

use XF\Entity\ConnectedAccountProvider;

class Microsoft extends AbstractProvider
{
	public function getOAuthServiceName()
	{
		return 'Microsoft';
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
			'scopes' => ['basic', 'signin', 'birthday', 'emails'],
			'redirect' => $redirectUri ?: $this->getRedirectUri($provider)
		];
	}
}