<?php

namespace XF\Authentication;

class vBulletin5 extends AbstractAuth
{
	public function getAuthenticationName()
	{
		return 'XF:vBulletin5';
	}

	public function generate($password)
	{
		throw new \LogicException('Cannot generate authentication for this type.');
	}
	protected function getHandler()
	{
		return new PasswordHash(\XF::config('passwordIterations'), false);
	}

	protected function createHash($password)
	{
		return md5($password);
	}

	public function authenticate($userId, $password)
	{
		if (!is_string($password) || $password === '' || empty($this->data))
		{
			return false;
		}

		$userHash = $this->createHash($password);

		if (function_exists('password_verify'))
		{
			return password_verify($userHash, $this->data['token']);
		}
		else
		{
			return $this->getHandler()->CheckPassword($userHash, $this->data['token']);
		}
	}
}