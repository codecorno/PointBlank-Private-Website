<?php

namespace XF\Authentication;

class IpsForums3x extends AbstractAuth
{
	protected function createHash($password, $salt)
	{
		return md5(md5($salt) . md5($password));
	}

	public function generate($password)
	{
		throw new \LogicException('Cannot generate authentication for this type.');
	}

	public function authenticate($userId, $password)
	{
		if (!is_string($password)
			|| $password === ''
			|| empty($this->data['hash'])
			|| empty($this->data['salt'])
		)
		{
			return false;
		}

		$passwordCleaned = strtr($password, [
			'&' => '&amp;',
			'\\' => '&#092;',
			'!' => '&#33;',
			'$' => '&#036;',
			'"' => '&quot;',
			'<' => '&lt;',
			'>' => '&gt;',
			'\'' => '&#39;',
		]);
		$userHash = $this->createHash($passwordCleaned, $this->data['salt']);
		if (\XF\Util\Php::hashEquals($this->data['hash'], $userHash))
		{
			return true;
		}

		$userHash = $this->createHash($password, $this->data['salt']);
		return \XF\Util\Php::hashEquals($this->data['hash'], $userHash);
	}

	public function getAuthenticationName()
	{
		return 'XF:IpsForums3x';
	}
}