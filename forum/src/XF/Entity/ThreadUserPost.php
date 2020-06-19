<?php

namespace XF\Entity;

use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Structure;

/**
 * COLUMNS
 * @property int thread_id
 * @property int user_id
 * @property int post_count
 *
 * RELATIONS
 * @property \XF\Entity\Thread Thread
 * @property \XF\Entity\User User
 */
class ThreadUserPost extends Entity
{
	public static function getStructure(Structure $structure)
	{
		$structure->table = 'xf_thread_user_post';
		$structure->shortName = 'XF:ThreadUserPost';
		$structure->primaryKey = ['thread_id', 'user_id'];
		$structure->columns = [
			'thread_id' => ['type' => self::UINT,  'required' => true],
			'user_id' => ['type' => self::UINT, 'required' => true],
			'post_count' => ['type' => self::UINT, 'default' => 0]
		];
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
			]
		];

		return $structure;
	}
}