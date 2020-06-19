<?php

namespace XF\ConnectedAccount\ProviderData;

class Linkedin extends AbstractProviderData
{
	public function getDefaultEndpoint()
	{
		return '/people/~:(id,formatted-name,location,picture-url,picture-urls::(original),public-profile-url,email-address)?format=json';
	}

	public function getProviderKey()
	{
		return $this->requestFromEndpoint('id');
	}

	public function getUsername()
	{
		return $this->requestFromEndpoint('formattedName');
	}

	public function getEmail()
	{
		return $this->requestFromEndpoint('emailAddress');
	}

	public function getLocation()
	{
		$location = $this->requestFromEndpoint('location');
		return isset($location['name']) ? $location['name'] : null;
	}

	public function getProfileLink()
	{
		return $this->requestFromEndpoint('publicProfileUrl');
	}

	public function getAvatarUrl()
	{
		$avatarUrls = $this->requestFromEndpoint('pictureUrls');
		if (!isset($avatarUrls['values']) || !is_array($avatarUrls['values']))
		{
			return null;
		}
		
		foreach ($avatarUrls['values'] AS $value)
		{
			return $value;
		}

		return null;
	}
}