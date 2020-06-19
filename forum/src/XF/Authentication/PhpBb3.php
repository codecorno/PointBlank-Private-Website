<?php

namespace XF\Authentication;

class PhpBb3 extends AbstractAuth
{
	public function generate($password)
	{
		throw new \LogicException('Cannot generate authentication for this type.');
	}

	public function authenticate($userId, $password)
	{
		if (!is_string($password) || $password === '' || empty($this->data))
		{
			return false;
		}

		$passwordHash = new PasswordHash(8, true);
		return $passwordHash->CheckPassword($password, $this->data['hash']);
	}

	public function getAuthenticationName()
	{
		return 'XF:PhpBb3';
	}
}