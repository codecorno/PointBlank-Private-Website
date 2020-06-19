<?php

namespace XF\Entity;

use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Structure;

/**
 * COLUMNS
 * @property int|null user_change_temp_id
 * @property int user_id
 * @property string|null change_key
 * @property string action_type
 * @property string|null action_modifier
 * @property string|null new_value
 * @property string|null old_value
 * @property int create_date
 * @property int|null expiry_date
 *
 * RELATIONS
 * @property \XF\Entity\User User
 */
class UserChangeTemp extends Entity
{
	protected function _preSave()
	{
		if (!$this->expiry_date)
		{
			$this->expiry_date = null;
		}
	}

	public static function getStructure(Structure $structure)
	{
		$structure->table = 'xf_user_change_temp';
		$structure->shortName = 'XF:UserChangeTemp';
		$structure->primaryKey = 'user_change_temp_id';
		$structure->columns = [
			'user_change_temp_id' => ['type' => self::UINT, 'autoIncrement' => true, 'nullable' => true],
			'user_id' => ['type' => self::UINT, 'required' => true],
			'change_key' => ['type' => self::STR, 'maxLength' => 50, 'required' => true, 'nullable' => true],
			'action_type' => ['type' => self::STR, 'required' => true,
				'allowedValues' => ['field', 'groups']
			],
			'action_modifier' => ['type' => self::STR, 'required' => true, 'nullable' => true],
			'new_value' => ['type' => self::BINARY, 'nullable' => true],
			'old_value' => ['type' => self::BINARY, 'nullable' => true],
			'create_date' => ['type' => self::UINT, 'default' => \XF::$time],
			'expiry_date' => ['type' => self::UINT, 'required' => true, 'nullable' => true]
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