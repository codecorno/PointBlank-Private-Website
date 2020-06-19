<?php

namespace XF\Repository;

use XF\Mvc\Entity\Finder;
use XF\Mvc\Entity\Repository;

class UserTfaTrusted extends Repository
{
	public function createTrustedKey($userId, $trustedUntil = null)
	{
		$userTrusted = $this->em->create('XF:UserTfaTrusted');
		$userTrusted->user_id = $userId;
		if ($trustedUntil)
		{
			$userTrusted->trusted_until = $trustedUntil;
		}
		$userTrusted->save();

		return $userTrusted->trusted_key;
	}

	/**
	 * @param int $userId
	 * @param string $key
	 *
	 * @return \XF\Entity\UserTfaTrusted|null
	 */
	public function getTfaTrustRecord($userId, $key)
	{
		return $this->finder('XF:UserTfaTrusted')
			->where(['user_id' => $userId, 'trusted_key' => $key])
			->where('trusted_until', '>=', \XF::$time)
			->fetchOne();
	}

	public function hasOtherTrustedDevices($userId, $thisDeviceTrustKey = null)
	{
		$total = $this->db()->fetchOne("
			SELECT COUNT(*)
			FROM xf_user_tfa_trusted
			WHERE user_id = ?
				" . ($thisDeviceTrustKey ? "AND trusted_key <> " . $this->db()->quote($thisDeviceTrustKey) : '') . "
		", $userId);

		return ($total > 0);
	}

	public function untrustDevice($userId, $trustKey)
	{
		if ($trustKey)
		{
			$this->db()->delete('xf_user_tfa_trusted', 'user_id = ? AND trusted_key = ?', [$userId, $trustKey]);
		}
	}

	public function untrustOtherDevices($userId, $thisDeviceTrustKey = null)
	{
		$this->db()->query("
			DELETE FROM xf_user_tfa_trusted
			WHERE user_id = ?
				" . ($thisDeviceTrustKey ? "AND trusted_key <> " . $this->db()->quote($thisDeviceTrustKey) : '') . "
		", $userId);
	}

	public function pruneTrustedKeys($cutOff = null)
	{
		if ($cutOff === null)
		{
			$cutOff = \XF::$time;
		}

		return $this->db()->delete('xf_user_tfa_trusted', 'trusted_until < ?', $cutOff);
	}
}