<?php

namespace XF\Authentication;

class NoPassword extends AbstractAuth
{
	public function isUpgradable()
	{
		return false;
	}

	public function generate($password)
	{
		return [];
	}

	public function authenticate($userId, $password)
	{
		return false;
	}

	public function hasPassword()
	{
		return false;
	}

	public function getAuthenticationName()
	{
		return 'XF:NoPassword';
	}
}