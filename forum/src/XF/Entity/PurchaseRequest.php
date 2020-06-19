<?php

namespace XF\Entity;

use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Structure;

/**
 * COLUMNS
 * @property int|null purchase_request_id
 * @property string request_key
 * @property int user_id
 * @property string provider_id
 * @property int payment_profile_id
 * @property string purchasable_type_id
 * @property float cost_amount
 * @property string cost_currency
 * @property array extra_data
 * @property string|null provider_metadata
 *
 * RELATIONS
 * @property \XF\Entity\PaymentProfile PaymentProfile
 * @property \XF\Entity\Purchasable Purchasable
 * @property \XF\Entity\User User
 */
class PurchaseRequest extends Entity
{
	public static function getStructure(Structure $structure)
	{
		$structure->table = 'xf_purchase_request';
		$structure->shortName = 'XF:PurchaseRequest';
		$structure->primaryKey = 'purchase_request_id';
		$structure->columns = [
			'purchase_request_id' => ['type' => self::UINT, 'autoIncrement' => true, 'nullable' => true],
			'request_key' => ['type' => self::STR, 'maxLength' => 32, 'required' => true,
				'unique' => true
			],
			'user_id' => ['type' => self::UINT, 'default' => 0],
			'provider_id' => ['type' => self::STR, 'maxLength' => 25, 'required' => true],
			'payment_profile_id' => ['type' => self::UINT, 'required' => true],
			'purchasable_type_id' => ['type' => self::STR, 'maxLength' => 50, 'required' => true],
			'cost_amount' => ['type' => self::FLOAT, 'required' => true],
			'cost_currency' => ['type' => self::STR, 'required' => true],
			'extra_data' => ['type' => self::JSON_ARRAY, 'default' => []],
			'provider_metadata' => ['type' => self::BINARY, 'default' => null, 'nullable' => true]
		];
		$structure->getters = [];
		$structure->relations = [
			'PaymentProfile' => [
				'entity' => 'XF:PaymentProfile',
				'type' => self::TO_ONE,
				'conditions' => 'payment_profile_id',
				'primary' => true
			],
			'Purchasable' => [
				'entity' => 'XF:Purchasable',
				'type' => self::TO_ONE,
				'conditions' => 'purchasable_type_id',
				'primary' => true
			],
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