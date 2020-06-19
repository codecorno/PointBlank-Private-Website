<?php

namespace XF\Repository;

use XF\Mvc\Entity\Finder;
use XF\Mvc\Entity\Repository;

class LoginAttempt extends Repository
{
	public function logFailedLogin($login, $ip)
	{
		$loginAttempt = $this->em->create('XF:LoginAttempt');
		$loginAttempt->bulkSet([
			'login' => utf8_substr($login, 0, 60),
			'ip_address' => \XF\Util\Ip::convertIpStringToBinary($ip),
			'attempt_date' => time()
		]);
		$loginAttempt->save();
	}

	public function countLoginAttemptsSince($cutOff, $ip, $login = null)
	{
		$ipAddress = \XF\Util\Ip::convertIpStringToBinary($ip);

		$db = $this->db();
		$loginWhere = ($login ? "AND login = " . $db->quote($login) : '');

		return $db->fetchOne("
			SELECT COUNT(*)
			FROM xf_login_attempt
			WHERE attempt_date >= ?
				AND ip_address = ?
				{$loginWhere}
		", [$cutOff, $ipAddress]);
	}

	public function clearLoginAttempts($login, $ip)
	{
		/** @var Finder $finder */
		$finder = $this->finder('XF:LoginAttempt');

		$attempts = $finder->where('login', $login)
			->where('ip_address', \XF\Util\Ip::convertIpStringToBinary($ip))
			->fetch();

		foreach ($attempts AS $attempt)
		{
			$attempt->delete();
		}
	}

	public function cleanUpLoginAttempts()
	{
		$this->db()->delete('xf_login_attempt', 'attempt_date < ?', time() - 86400);
	}
}