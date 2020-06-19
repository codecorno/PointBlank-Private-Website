<?php

namespace XF\ConnectedAccount\ProviderData;

class Google extends AbstractProviderData
{
	public function getDefaultEndpoint()
	{
		return 'https://www.googleapis.com/oauth2/v3/userinfo';
	}

	public function getProviderKey()
	{
		return $this->requestFromEndpoint('sub');
	}

	public function getUsername()
	{
		return $this->requestFromEndpoint('name');
	}

	public function getEmail()
	{
		return $this->requestFromEndpoint('email');
	}

	public function getDob()
	{
		$birthday = $this->requestFromEndpoint('birthday');
		if ($birthday)
		{
			return $this->prepareBirthday($birthday, 'm/d/y');
		}
		return null;
	}

	public function getProfileLink()
	{
		return $this->requestFromEndpoint('profile');
	}

	public function getAvatarUrl()
	{
		return $this->requestFromEndpoint('picture');
	}
}