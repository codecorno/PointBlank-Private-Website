<?php

namespace XF\Entity;

use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Structure;

/**
 * COLUMNS
 * @property int|null forum_read_id
 * @property int user_id
 * @property int node_id
 * @property int forum_read_date
 *
 * RELATIONS
 * @property \XF\Entity\User User
 * @property \XF\Entity\Forum Forum
 */
class ForumRead extends Entity
{
	public static function getStructure(Structure $structure)
	{
		$structure->table = 'xf_forum_read';
		$structure->shortName = 'XF:ForumRead';
		$structure->primaryKey = 'forum_read_id';
		$structure->columns = [
			'forum_read_id' => ['type' => self::UINT, 'autoIncrement' => true, 'nullable' => true],
			'user_id' => ['type' => self::UINT, 'required' => true],
			'node_id' => ['type' => self::UINT, 'required' => true],
			'forum_read_date' => ['type' => self::UINT, 'required' => true]
		];
		$structure->getters = [];
		$structure->relations = [
			'User' => [
				'entity' => 'XF:User',
				'type' => self::TO_ONE,
				'conditions' => 'user_id',
				'primary' => true
			],
			'Forum' => [
				'entity' => 'XF:Forum',
				'type' => self::TO_ONE,
				'conditions' => 'node_id',
				'primary' => true
			],
		];

		return $structure;
	}
}