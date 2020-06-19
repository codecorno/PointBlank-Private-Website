<?php

namespace XF\Entity;

use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Structure;

/**
 * COLUMNS
 * @property int user_id
 * @property int trophy_id
 * @property int award_date
 *
 * RELATIONS
 * @property \XF\Entity\User User
 * @property \XF\Entity\Trophy Trophy
 */
class UserTrophy extends Entity
{
	public static function getStructure(Structure $structure)
	{
		$structure->table = 'xf_user_trophy';
		$structure->shortName = 'XF:UserTrophy';
		$structure->primaryKey = ['user_id', 'trophy_id'];
		$structure->columns = [
			'user_id' => ['type' => self::UINT, 'required' => true],
			'trophy_id' => ['type' => self::UINT, 'required' => true],
			'award_date' => ['type' => self::UINT, 'default' => \XF::$time]
		];
		$structure->getters = [];
		$structure->relations = [
			'User' => [
				'entity' => 'XF:User',
				'type' => self::TO_ONE,
				'conditions' => 'user_id',
				'primary' => true
			],
			'Trophy' => [
				'entity' => 'XF:Trophy',
				'type' => self::TO_ONE,
				'conditions' => 'trophy_id',
				'primary' => true
			]
		];

		return $structure;
	}
}