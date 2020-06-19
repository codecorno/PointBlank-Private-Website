<?php

namespace XF\Authentication;

class SMF extends AbstractAuth
{
	protected function createHash($password, $username)
	{
		return sha1($username . utf8_unhtml($password));
	}

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

		$userHash = $this->createHash($password, $this->data['username']);
		return \XF\Util\Php::hashEquals($this->data['hash'], $userHash);
	}

	public function getAuthenticationName()
	{
		return 'XF:SMF';
	}
}