<?php

namespace XF\Entity;

use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Structure;

/**
 * COLUMNS
 * @property int|null remember_id
 * @property int user_id
 * @property string remember_key
 * @property int start_date
 * @property int expiry_date
 *
 * RELATIONS
 * @property \XF\Entity\User User
 */
class UserRemember extends Entity
{
	public function generateRememberKey()
	{
		return \XF::generateRandomString(40);
	}

	public function getDbEncodedKey($key)
	{
		return hash('sha256', $key, true);
	}

	public function generateForUserId($userId)
	{
		do
		{
			$key = $this->generateRememberKey();
			$dbKey = $this->getDbEncodedKey($key);

			$exists = $this->db()->fetchOne("
				SELECT 1
				FROM xf_user_remember
				WHERE user_id = ?
					AND remember_key = ?
			", [$userId, $dbKey]);
		}
		while ($exists);

		$this->user_id = $userId;
		$this->remember_key = $dbKey;

		return $key;
	}

	public function getCookieValue()
	{
		return $this->user_id . ',' . $this->remember_key;
	}

	public function isValid()
	{
		return ($this->expiry_date >= \XF::$time);
	}

	public function isKeyCorrect($check)
	{
		$known = $this->remember_key;
		if (!$known)
		{
			return false;
		}

		$check = $this->getDbEncodedKey($check);
		return \XF\Util\Php::hashEquals($known, $check);
	}

	public function extendExpiryDate($amount = null)
	{
		if (!$amount)
		{
			$amount = 28 * 86400; // TODO: configurable?
		}

		$this->expiry_date = \XF::$time + $amount;
	}

	protected function _postSave()
	{
		if ($this->isInsert())
		{
			$this->repository('XF:UserRemember')->applyUserRememberRecordLimit($this->user_id);
		}
	}

	public static function getStructure(Structure $structure)
	{
		$structure->table = 'xf_user_remember';
		$structure->shortName = 'XF:UserRemember';
		$structure->primaryKey = 'remember_id';
		$structure->columns = [
			'remember_id' => ['type' => self::UINT, 'autoIncrement' => true, 'nullable' => true],
			'user_id' => ['type' => self::UINT, 'required' => true],
			'remember_key' => ['type' => self::BINARY, 'maxLength' => 32, 'required' => true],
			'start_date' => ['type' => self::UINT, 'default' => \XF::$time],
			'expiry_date' => ['type' => self::UINT, 'required' => true]
		];
		$structure->getters = [];
		$structure->relations = [
			'User' => [
				'entity' => 'XF:User',
				'type' => self::TO_ONE,
				'conditions' => 'user_id',
				'primary' => true
			]
		];

		return $structure;
	}
}