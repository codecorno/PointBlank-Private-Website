<?php

namespace XF\Entity;

use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Structure;

/**
 * COLUMNS
 * @property int user_group_id
 * @property int permission_combination_id
 *
 * RELATIONS
 * @property \XF\Entity\UserGroup UserGroup
 * @property \XF\Entity\PermissionCombination PermissionCombination
 */
class PermissionCombinationUserGroup extends Entity
{
	public static function getStructure(Structure $structure)
	{
		$structure->table = 'xf_permission_combination_user_group';
		$structure->shortName = 'XF:PermissionCombinationUserGroup';
		$structure->primaryKey = ['user_group_id', 'permission_combination_id'];
		$structure->columns = [
			'user_group_id' => ['type' => self::UINT, 'required' => true],
			'permission_combination_id' => ['type' => self::UINT, 'required' => true]
		];
		$structure->getters = [];
		$structure->relations = [
			'UserGroup' => [
				'entity' => 'XF:UserGroup',
				'type' => self::TO_ONE,
				'conditions' => 'user_group_id',
				'primary' => true
			],
			'PermissionCombination' => [
				'entity' => 'XF:PermissionCombination',
				'type' => self::TO_ONE,
				'conditions' => 'permission_combination_id',
				'primary' => true
			],
		];

		return $structure;
	}
}