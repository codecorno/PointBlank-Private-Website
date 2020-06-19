<?php

namespace XF\Authentication;

class vBulletin extends AbstractAuth
{
	protected function createHash($password, $salt)
	{
		return md5(md5($password) . $salt);
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

		$userHash = $this->createHash($password, $this->data['salt']);
		return \XF\Util\Php::hashEquals($this->data['hash'], $userHash);
	}

	public function getAuthenticationName()
	{
		return 'XF:vBulletin';
	}
}