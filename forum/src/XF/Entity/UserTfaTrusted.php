<?php

namespace XF\Entity;

use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Structure;

/**
 * COLUMNS
 * @property int|null tfa_trusted_id
 * @property int user_id
 * @property string trusted_key
 * @property int trusted_until
 *
 * RELATIONS
 * @property \XF\Entity\User User
 */
class UserTfaTrusted extends Entity
{
	protected function _preSave()
	{
		if (!$this->trusted_key)
		{
			$this->trusted_key = \XF::generateRandomString(32);
		}

		if (!$this->trusted_until)
		{
			$trustedUntil = \XF::$time + 86400 * 30;

			// jitter between 0 and 96 hours (4 days). This attempts to reduce situations where multiple
			// devices all expire at almost identical times
			$offsetJitter = mt_rand(0, 4 * 24) * 3600;
			$trustedUntil += $offsetJitter;

			$this->trusted_until = $trustedUntil;
		}
	}

	public static function getStructure(Structure $structure)
	{
		$structure->table = 'xf_user_tfa_trusted';
		$structure->shortName = 'XF:UserTfaTrusted';
		$structure->primaryKey = 'tfa_trusted_id';
		$structure->columns = [
			'tfa_trusted_id' => ['type' => self::UINT, 'autoIncrement' => true, 'nullable' => true],
			'user_id' => ['type' => self::UINT, 'required' => true],
			'trusted_key' => ['type' => self::STR, 'maxLength' => 32, 'required' => true],
			'trusted_until' => ['type' => self::UINT, 'required' => true]
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

	/**
	 * @return \XF\Repository\Tfa
	 */
	protected function getTfaRepo()
	{
		return $this->repository('XF:Tfa');
	}
}