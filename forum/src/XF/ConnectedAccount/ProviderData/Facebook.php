<?php

namespace XF\ConnectedAccount\ProviderData;

class Facebook extends AbstractProviderData
{
	public function getDefaultEndpoint()
	{
		return 'v2.6/me?fields=id,name,email,birthday,website,location,link';
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

	public function getDob()
	{
		$birthday = $this->requestFromEndpoint('birthday');
		if ($birthday)
		{
			return $this->prepareBirthday($birthday, 'm/d/y');
		}

		return null;
	}

	public function getWebsite()
	{
		return $this->requestFromEndpoint('website');
	}

	public function getLocation()
	{
		$location = $this->requestFromEndpoint('location');
		return isset($location['name']) ? $location['name'] : null;
	}

	public function getProfileLink()
	{
		return $this->requestFromEndpoint('link');
	}

	public function getAvatarUrl()
	{
		$picture = $this->requestFromEndpoint(null, 'GET', 'v2.6/me/picture?type=large&redirect=false');

		if (!empty($picture['data']['is_silhouette']))
		{
			return null; // Default Facebook avatar, so we'll just use our own.
		}

		return isset($picture['data']['url']) ? $picture['data']['url'] : null;
	}
}