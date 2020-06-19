<?php

namespace XF\ConnectedAccount\ProviderData;

class GitHub extends AbstractProviderData
{
	public function getDefaultEndpoint()
	{
		return 'user';
	}

	public function getProviderKey()
	{
		return $this->requestFromEndpoint('id');
	}

	public function getUsername()
	{
		return $this->requestFromEndpoint('name');
	}

	public function getEmail()
	{
		return $this->requestFromEndpoint('email');
	}

	public function getProfileLink()
	{
		return $this->requestFromEndpoint('html_url');
	}

	public function getLocation()
	{
		return $this->requestFromEndpoint('location');
	}

	public function getWebsite()
	{
		return $this->requestFromEndpoint('blog');
	}

	public function getAvatarUrl()
	{
		return $this->requestFromEndpoint('avatar_url');
	}
}