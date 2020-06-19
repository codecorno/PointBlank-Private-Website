<?php

namespace XF\Entity;

use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Structure;

/**
 * COLUMNS
 * @property int promotion_id
 * @property int user_id
 * @property int promotion_date
 * @property string promotion_state
 *
 * RELATIONS
 * @property \XF\Entity\User User
 * @property \XF\Entity\UserGroupPromotion Promotion
 */
class UserGroupPromotionLog extends Entity
{
	public static function getStructure(Structure $structure)
	{
		$structure->table = 'xf_user_group_promotion_log';
		$structure->shortName = 'XF:UserGroupPromotionLog';
		$structure->primaryKey = ['promotion_id', 'user_id'];
		$structure->columns = [
			'promotion_id' => ['type' => self::UINT, 'required' => true],
			'user_id' => ['type' => self::UINT, 'required' => true],
			'promotion_date' => ['type' => self::UINT, 'default' => \XF::$time],
			'promotion_state' => ['type' => self::STR, 'default' => 'automatic',
				'allowedValues' => ['automatic', 'manual', 'disabled']
			]
		];
		$structure->getters = [];
		$structure->relations = [
			'User' => [
				'entity' => 'XF:User',
				'type' => self::TO_ONE,
				'conditions' => 'user_id',
				'primary' => true
			],
			'Promotion' => [
				'entity' => 'XF:UserGroupPromotion',
				'type' => self::TO_ONE,
				'conditions' => 'promotion_id',
				'primary' => true
			]
		];

		return $structure;
	}
}