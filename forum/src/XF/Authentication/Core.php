<?php

namespace XF\Authentication;

class Core extends AbstractAuth
{
	/**
	 * Hash function to use for generating salts and passwords
	 *
	 * @var string
	 */
	protected $hashFunc = 'sha1';

	protected function setup()
	{
		if (!empty($this->data['hashFunc']))
		{
			$this->hashFunc = $this->data['hashFunc'];
		}
		else
		{
			$this->hashFunc = extension_loaded('hash') ? 'sha256' : 'sha1';
		}
	}

	protected function createHash($hash)
	{
		switch ($this->hashFunc)
		{
			case 'sha256': return hash('sha256', $hash);
			case 'sha1': return sha1($hash);
			default: throw new \InvalidArgumentException("Unknown hash type");
		}
	}

	protected function getPasswordHash($password, $salt)
	{
		return $this->createHash($this->createHash($password) . $salt);
	}

	public function generate($password)
	{
		if (!is_string($password) || $password === '')
		{
			return false;
		}

		$salt = $this->createHash(\XF::generateRandomString(20, true));

		return [
			'hash' => $this->getPasswordHash($password, $salt),
			'salt' => $salt,
			'hashFunc' => $this->hashFunc
		];
	}

	public function authenticate($userId, $password)
	{
		if (!is_string($password) || $password === '' || empty($this->data))
		{
			return false;
		}

		$userHash = $this->getPasswordHash($password, $this->data['salt']);
		return \XF\Util\Php::hashEquals($this->data['hash'], $userHash);
	}

	public function getAuthenticationName()
	{
		return 'XF:Core';
	}
}