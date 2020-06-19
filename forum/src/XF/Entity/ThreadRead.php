<?php

namespace XF\Entity;

use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Structure;

/**
 * COLUMNS
 * @property int|null thread_read_id
 * @property int user_id
 * @property int thread_id
 * @property int thread_read_date
 *
 * RELATIONS
 * @property \XF\Entity\User User
 * @property \XF\Entity\Thread Thread
 */
class ThreadRead extends Entity
{
	public static function getStructure(Structure $structure)
	{
		$structure->table = 'xf_thread_read';
		$structure->shortName = 'XF:ThreadRead';
		$structure->primaryKey = 'thread_read_id';
		$structure->columns = [
			'thread_read_id' => ['type' => self::UINT, 'autoIncrement' => true, 'nullable' => true],
			'user_id' => ['type' => self::UINT, 'required' => true],
			'thread_id' => ['type' => self::UINT, 'required' => true],
			'thread_read_date' => ['type' => self::UINT, 'required' => true]
		];
		$structure->getters = [];
		$structure->relations = [
			'User' => [
				'entity' => 'XF:User',
				'type' => self::TO_ONE,
				'conditions' => 'user_id',
				'primary' => true
			],
			'Thread' => [
				'entity' => 'XF:Thread',
				'type' => self::TO_ONE,
				'conditions' => 'thread_id',
				'primary' => true
			],
		];

		return $structure;
	}
}