<?php

namespace XF\Entity;

use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Structure;

/**
 * COLUMNS
 * @property int user_id
 * @property int node_id
 * @property string notify_on
 * @property bool send_alert
 * @property bool send_email
 *
 * RELATIONS
 * @property \XF\Entity\Forum Forum
 * @property \XF\Entity\User User
 */
class ForumWatch extends Entity
{
	public static function getStructure(Structure $structure)
	{
		$structure->table = 'xf_forum_watch';
		$structure->shortName = 'XF:ForumWatch';
		$structure->primaryKey = ['user_id', 'node_id'];
		$structure->columns = [
			'user_id' => ['type' => self::UINT, 'required' => true],
			'node_id' => ['type' => self::UINT, 'required' => true],
			'notify_on' => ['type' => self::STR, 'default' => '',
				'allowedValues' => ['', 'thread', 'message']
			],
			'send_alert' => ['type' => self::BOOL, 'default' => false],
			'send_email' => ['type' => self::BOOL, 'default' => false]
		];
		$structure->getters = [];
		$structure->relations = [
			'Forum' => [
				'entity' => 'XF:Forum',
				'type' => self::TO_ONE,
				'conditions' => 'node_id',
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