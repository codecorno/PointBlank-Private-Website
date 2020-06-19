<?php

namespace XF\ConnectedAccount\ProviderData;

class Twitter extends AbstractProviderData
{
	public function getDefaultEndpoint()
	{
		return 'account/verify_credentials.json';
	}

	public function getProviderKey()
	{
		return $this->requestFromEndpoint('id_str');
	}

	public function getUsername()
	{
		return $this->requestFromEndpoint('name');
	}

	public function getScreenName()
	{
		return $this->requestFromEndpoint('screen_name');
	}

	public function getWebsite()
	{
		return $this->requestFromEndpoint('url');
	}

	public function getLocation()
	{
		return $this->requestFromEndpoint('location');
	}

	public function getProfileLink()
	{
		return 'https://twitter.com/' . $this->getScreenName();
	}

	public function getAvatarUrl()
	{
		$url = $this->requestFromEndpoint('profile_image_url_https');
		if (!$url || $this->requestFromEndpoint('default_profile_image'))
		{
			return null;
		}

		return str_replace('_normal', '', $url);
	}
}