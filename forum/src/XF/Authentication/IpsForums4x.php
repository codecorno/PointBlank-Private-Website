<?php

namespace XF\Authentication;

class IpsForums4x extends AbstractAuth
{
	public function generate($password)
	{
		throw new \LogicException('Cannot generate authentication for this type.');
	}

	public function authenticate($userId, $password)
	{
		if (!is_string($password) || $password === '' || empty($this->data['hash']))
		{
			return false;
		}

		$passwordHash = new PasswordHash(13, false);
		return $passwordHash->CheckPassword($password, $this->data['hash']);
	}

	public function getAuthenticationName()
	{
		return 'XF:IpsForums4x';
	}
}