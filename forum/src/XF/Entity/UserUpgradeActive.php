<?php

namespace XF\Entity;

use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Structure;

/**
 * COLUMNS
 * @property int|null user_upgrade_record_id
 * @property int user_id
 * @property string|null purchase_request_key
 * @property int user_upgrade_id
 * @property array extra
 * @property int start_date
 * @property int end_date
 *
 * RELATIONS
 * @property \XF\Entity\UserUpgrade Upgrade
 * @property \XF\Entity\User User
 * @property \XF\Entity\PurchaseRequest PurchaseRequest
 */
class UserUpgradeActive extends Entity
{
	protected function _postSave()
	{
		if ($this->isInsert())
		{
			// there is a situation involving MySQL restarts where the auto increment value in xf_user_upgrade_active
			// is lower than the highest value in xf_user_upgrade_expired, so attempt to workaround that.

			$maxExpiredId = intval($this->db()->fetchOne("
				SELECT MAX(user_upgrade_record_id)
				FROM xf_user_upgrade_expired
			"));


			if ($this->user_upgrade_record_id <= $maxExpiredId)
			{
				try
				{
					$newAi = $maxExpiredId + 2; // what we're updating to + 1
					$this->db()->query("
						ALTER TABLE xf_user_upgrade_active AUTO_INCREMENT = $newAi
					");
					$this->fastUpdate('user_upgrade_record_id', $maxExpiredId + 1);
				}
				catch (\XF\Db\Exception $e)
				{
				}
			}
		}
	}

	public static function getStructure(Structure $structure)
	{
		$structure->table = 'xf_user_upgrade_active';
		$structure->shortName = 'XF:UserUpgradeActive';
		$structure->primaryKey = 'user_upgrade_record_id';
		$structure->columns = [
			'user_upgrade_record_id' => ['type' => self::UINT, 'autoIncrement' => true, 'nullable' => true],
			'user_id' => ['type' => self::UINT, 'required' => true],
			'purchase_request_key' => ['type' => self::STR, 'maxLength' => 32, 'nullable' => true],
			'user_upgrade_id' => ['type' => self::UINT, 'required' => true],
			'extra' => ['type' => self::JSON_ARRAY, 'default' => []],
			'start_date' => ['type' => self::UINT, 'default' => 0],
			'end_date' => ['type' => self::UINT, 'default' => 0]
		];
		$structure->getters = [];
		$structure->relations = [
			'Upgrade' => [
				'entity' => 'XF:UserUpgrade',
				'type' => self::TO_ONE,
				'conditions' => 'user_upgrade_id',
				'primary' => true
			],
			'User' => [
				'entity' => 'XF:User',
				'type' => self::TO_ONE,
				'conditions' => 'user_id',
				'primary' => true
			],
			'PurchaseRequest' => [
				'entity' => 'XF:PurchaseRequest',
				'type' => self::TO_ONE,
				'conditions' => [
					['request_key', '=', '$purchase_request_key']
				]
			]
		];

		return $structure;
	}
}