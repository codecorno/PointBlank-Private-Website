<?php

namespace XF\Repository;

use XF\Mvc\Entity\Repository;

class UserRemember extends Repository
{
	public function createRememberRecord($userId, &$remember = null)
	{
		/** @var \XF\Entity\UserRemember $remember */
		$remember = $this->em->create('XF:UserRemember');
		$key = $remember->generateForUserId($userId);
		$remember->extendExpiryDate();
		$remember->save();

		return $key;
	}

	public function getCookieValue($userId, $cookieKey)
	{
		return $userId . ',' . $cookieKey;
	}

	/**
	 * @param string $cookie
	 * @param null|\XF\Entity\UserRemember $matched If matched, returns remember record
	 * @return bool
	 */
	public function validateByCookieValue($cookie, &$matched = null)
	{
		$matched = null;

		if (!$cookie || !is_string($cookie) || !strpos($cookie, ','))
		{
			return false;
		}

		list($userId, $key) = explode(',', $cookie, 2);

		$userId = intval($userId);
		if (!$userId)
		{
			return false;
		}

		return $this->validateRememberRecord($userId, $key, $matched);
	}

	public function validateRememberRecord($userId, $key, &$matched = null)
	{
		$matched = null;

		$records = $this->finder('XF:UserRemember')->where('user_id', $userId)->fetch();
		foreach ($records AS $record)
		{
			/** @var $record \XF\Entity\UserRemember */
			if ($record->isKeyCorrect($key))
			{
				if ($record->isValid())
				{
					$matched = $record;
					return true;
				}
				else
				{
					return false;
				}
			}
		}

		return false;
	}

	public function clearUserRememberRecords($userId)
	{
		$this->db()->delete('xf_user_remember', 'user_id = ?', $userId);
	}

	public function pruneExpiredRememberRecords()
	{
		$this->db()->delete('xf_user_remember', 'expiry_date < ?', \XF::$time);
	}

	/**
	 * Only allows a user to have so many remember keys at a given time, deleting soonest expiring ones first.
	 *
	 * @param int $userId
	 *
	 * @return int Number of rows deleted
	 */
	public function applyUserRememberRecordLimit($userId)
	{
		$expiryDate = $this->db()->fetchOne("
			SELECT expiry_date
			FROM xf_user_remember
			WHERE user_id = ?
			ORDER BY expiry_date DESC 
			LIMIT 1 OFFSET 500
		", $userId);
		if ($expiryDate)
		{
			return $this->db()->delete('xf_user_remember',
				'user_id = ? AND expiry_date <= ?',
				[$userId, $expiryDate]
			);
		}

		return 0;
	}
}