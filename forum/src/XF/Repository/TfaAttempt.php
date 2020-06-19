<?php

namespace XF\Repository;

use XF\Mvc\Entity\Finder;
use XF\Mvc\Entity\Repository;

class TfaAttempt extends Repository
{
	public function logFailedTfaAttempt($userId)
	{
		$loginAttempt = $this->em->create('XF:TfaAttempt');
		$loginAttempt->bulkSet([
			'user_id' => $userId,
			'attempt_date' => time()
		]);
		$loginAttempt->save();
	}

	public function countTfaAttemptsSince($cutOff, $userId)
	{
		return $this->db()->fetchOne("
			SELECT COUNT(*)
			FROM xf_tfa_attempt
			WHERE attempt_date >= ?
				AND user_id = ?
		", [$cutOff, $userId]);
	}

	public function clearTfaAttempts($userId)
	{
		/** @var Finder $finder */
		$finder = $this->finder('XF:TfaAttempt');

		$attempts = $finder->where('user_id', $userId)
			->fetch();

		foreach ($attempts AS $attempt)
		{
			$attempt->delete();
		}
	}

	public function cleanUpTfaAttempts()
	{
		$this->db()->delete('xf_tfa_attempt', 'attempt_date < ?', time() - 86400);
	}
}