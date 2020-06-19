<?php

namespace XF\Authentication;

class Core12 extends AbstractAuth
{
	protected function getDefaultOptions()
	{
		$config = \XF::config();

		if (!empty($config['auth']))
		{
			return array_replace([
				'algo' => PASSWORD_BCRYPT,
				'options' => []
			], $config['auth']);
		}
		else
		{
			return [
				'algo' => PASSWORD_BCRYPT,
				'options' => [
					'cost' => $config['passwordIterations']
				]
			];
		}
	}

	protected function getHandler()
	{
		return new PasswordHash(\XF::config('passwordIterations'), false);
	}

	/**
	 * Detects password hashes created with the legacy PasswordHash helper that
	 * are not backwards compatible with PHP 5.5+ password_hash/password_verify.
	 *
	 * @return bool
	 */
	protected function isLegacyHash()
	{
		return boolval(preg_match('/^(?:\$(P|H)\$|[^\$])/i',  $this->data['hash']));
	}

	public function generate($password)
	{
		$options = $this->getDefaultOptions();

		$hash = password_hash($password, $options['algo'], $options['options']);

		return [
			'hash' => $hash
		];
	}

	public function authenticate($userId, $password)
	{
		if (!is_string($password) || $password === '' || empty($this->data))
		{
			return false;
		}

		if ($this->isLegacyHash())
		{
			return $this->getHandler()->CheckPassword($password, $this->data['hash']);
		}
		else
		{
			return password_verify($password, $this->data['hash']);
		}
	}

	public function isUpgradable()
	{
		if (!empty($this->data['hash']))
		{
			$hash = $this->data['hash'];
			$options = $this->getDefaultOptions();

			if ($this->isLegacyHash())
			{
				$expectedIterations = min(intval($options['options']['cost']), 30);

				preg_match('/^\$(P|H)\$(.)/i',  $hash, $match);
				$iterations = $this->getHandler()->reverseItoA64($match[2]) - 5; // 5 iterations removed in PHP 5	

				return $expectedIterations !== $iterations;
			}
			else
			{
				return password_needs_rehash($hash, $options['algo'], $options['options']);
			}
		}

		return true;
	}

	public function getAuthenticationName()
	{
		return 'XF:Core12';
	}
}