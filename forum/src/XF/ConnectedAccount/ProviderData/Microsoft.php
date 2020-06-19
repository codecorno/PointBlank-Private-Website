<?php

namespace XF\ConnectedAccount\ProviderData;

class Microsoft extends AbstractProviderData
{
	public function getDefaultEndpoint()
	{
		return 'me';
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
		$emails = $this->requestFromEndpoint('emails');
		if (empty($emails['preferred']))
		{
			return null;
		}

		$validator = \XF::app()->validator('Email');
		if (!$validator->isValid($emails['preferred']))
		{
			return null;
		}

		return $emails['preferred'];
	}

	public function getDob()
	{
		return $this->prepareBirthday(
			$this->requestFromEndpoint('birth_day') . '/' .
			$this->requestFromEndpoint('birth_month') . '/' .
			$this->requestFromEndpoint('birth_year'), 'd/m/y'
		);
	}

	public function getAvatarUrl()
	{
		$picture = $this->requestFromEndpoint(null, 'GET', 'me/picture?suppress_redirects=true');

		if (empty($picture['location']))
		{
			return null;
		}

		return $picture['location'];
	}
}