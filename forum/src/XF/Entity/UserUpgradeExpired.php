<?php

namespace XF\Entity;

use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Structure;

/**
 * COLUMNS
 * @property int user_upgrade_record_id
 * @property int user_id
 * @property string|null purchase_request_key
 * @property int user_upgrade_id
 * @property array extra
 * @property int start_date
 * @property int end_date
 * @property int original_end_date
 *
 * RELATIONS
 * @property \XF\Entity\UserUpgrade Upgrade
 * @property \XF\Entity\User User
 * @property \XF\Entity\PurchaseRequest PurchaseRequest
 */
class UserUpgradeExpired extends Entity
{
	public static function getStructure(Structure $structure)
	{
		$structure->table = 'xf_user_upgrade_expired';
		$structure->shortName = 'XF:UserUpgradeExpired';
		$structure->primaryKey = 'user_upgrade_record_id';
		$structure->columns = [
			'user_upgrade_record_id' => ['type' => self::UINT],
			'user_id' => ['type' => self::UINT, 'required' => true],
			'purchase_request_key' => ['type' => self::STR, 'maxLength' => 32, 'nullable' => true],
			'user_upgrade_id' => ['type' => self::UINT, 'required' => true],
			'extra' => ['type' => self::JSON_ARRAY, 'default' => []],
			'start_date' => ['type' => self::UINT, 'default' => 0],
			'end_date' => ['type' => self::UINT, 'default' => 0],
			'original_end_date' => ['type' => self::UINT, 'default' => 0]
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