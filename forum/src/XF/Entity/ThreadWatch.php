<?php

namespace XF\Entity;

use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Structure;

/**
 * COLUMNS
 * @property int user_id
 * @property int thread_id
 * @property bool email_subscribe
 *
 * RELATIONS
 * @property \XF\Entity\Thread Thread
 * @property \XF\Entity\User User
 */
class ThreadWatch extends Entity
{
	public static function getStructure(Structure $structure)
	{
		$structure->table = 'xf_thread_watch';
		$structure->shortName = 'XF:ThreadWatch';
		$structure->primaryKey = ['user_id', 'thread_id'];
		$structure->columns = [
			'user_id' => ['type' => self::UINT, 'required' => true],
			'thread_id' => ['type' => self::UINT, 'required' => true],
			'email_subscribe' => ['type' => self::BOOL, 'default' => false]
		];
		$structure->getters = [];
		$structure->relations = [
			'Thread' => [
				'entity' => 'XF:Thread',
				'type' => self::TO_ONE,
				'conditions' => 'thread_id',
				'primary' => true
			],
			'User' => [
				'entity' => 'XF:User',
				'type' => self::TO_ONE,
				'conditions' => 'user_id',
				'primary' => true
			],
		];

		return $structure;
	}
}