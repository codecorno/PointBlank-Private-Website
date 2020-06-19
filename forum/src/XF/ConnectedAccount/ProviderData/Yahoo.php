<?php

namespace XF\ConnectedAccount\ProviderData;

class Yahoo extends AbstractProviderData
{
	public function getDefaultEndpoint()
	{
		return 'https://social.yahooapis.com/v1/user/me/profile?format=json';
	}

	public function getProviderKey()
	{
		$profile = $this->requestFromEndpoint('profile');
		return isset($profile['guid']) ? $profile['guid'] : null;
	}

	public function getUsername()
	{
		$profile = $this->requestFromEndpoint('profile');
		return isset($profile['nickname']) ? $profile['nickname'] : null;
	}

	public function getAvatarUrl()
	{
		$profile = $this->requestFromEndpoint('profile');
		return isset($profile['image']['imageUrl']) ? $profile['image']['imageUrl'] : null;
	}
}