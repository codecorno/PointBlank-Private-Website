<?php

namespace XF\Entity;

use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Structure;

/**
 * COLUMNS
 * @property int|null find_new_default_id
 * @property int user_id
 * @property string content_type
 * @property array filters
 *
 * RELATIONS
 * @property \XF\Entity\User User
 */
class FindNewDefault extends Entity
{
	public static function getStructure(Structure $structure)
	{
		$structure->table = 'xf_find_new_default';
		$structure->shortName = 'XF:FindNewDefault';
		$structure->primaryKey = 'find_new_default_id';
		$structure->columns = [
			'find_new_default_id' => ['type' => self::UINT, 'autoIncrement' => true, 'nullable' => true],
			'user_id' => ['type' => self::UINT, 'required' => true],
			'content_type' => ['type' => self::STR, 'maxLength' => 25, 'required' => true],
			'filters' => ['type' => self::JSON_ARRAY, 'default' => []]
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