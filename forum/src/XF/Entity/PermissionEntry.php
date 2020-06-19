<?php

namespace XF\Entity;

use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Structure;

/**
 * COLUMNS
 * @property int|null permission_entry_id
 * @property int user_group_id
 * @property int user_id
 * @property string permission_group_id
 * @property string permission_id
 * @property string permission_value
 * @property int permission_value_int
 */
class PermissionEntry extends Entity
{
	// Note: if adding permission rebuilds from here, make sure they don't happen within the
	// UpdatePermissions service while writing.

	public static function getStructure(Structure $structure)
	{
		$structure->table = 'xf_permission_entry';
		$structure->shortName = 'XF:PermissionEntry';
		$structure->primaryKey = 'permission_entry_id';
		$structure->columns = [
			'permission_entry_id' => ['type' => self::UINT, 'autoIncrement' => true, 'nullable' => true],
			'user_group_id' => ['type' => self::UINT, 'default' => 0],
			'user_id' => ['type' => self::UINT, 'default' => 0],
			'permission_group_id' => ['type' => self::STR, 'maxLength' => 25, 'required' => true],
			'permission_id' => ['type' => self::STR, 'maxLength' => 25, 'required' => true],
			'permission_value' => ['type' => self::STR, 'required' => true,
				'allowedValues' => ['unset', 'allow', 'deny', 'use_int']
			],
			'permission_value_int' => ['type' => self::INT, 'default' => 0]
		];
		$structure->getters = [];
		$structure->relations = [];

		return $structure;
	}
}