<?php

namespace XF\Entity;

use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Structure;

/**
 * COLUMNS
 * @property int|null bounce_id
 * @property int log_date
 * @property int email_date
 * @property string message_type
 * @property string action_taken
 * @property int|null user_id
 * @property string|null recipient
 * @property string raw_message
 * @property string|null status_code
 * @property string|null diagnostic_info
 *
 * RELATIONS
 * @property \XF\Entity\User User
 */
class EmailBounceLog extends Entity
{
	public static function getStructure(Structure $structure)
	{
		$structure->table = 'xf_email_bounce_log';
		$structure->shortName = 'XF:EmailBounceLog';
		$structure->primaryKey = 'bounce_id';
		$structure->columns = [
			'bounce_id' => ['type' => self::UINT, 'autoIncrement' => true, 'nullable' => true],
			'log_date' => ['type' => self::UINT, 'default' => \XF::$time],
			'email_date' => ['type' => self::UINT, 'default' => \XF::$time],
			'message_type' => ['type' => self::STR, 'maxLength' => 25, 'default' => 'unknown'],
			'action_taken' => ['type' => self::STR, 'maxLength' => 25, 'default' => ''],
			'user_id' => ['type' => self::UINT, 'nullable' => true],
			'recipient' => ['type' => self::STR, 'maxLength' => 255, 'nullable' => true],
			'raw_message' => ['type' => self::BINARY, 'default' => ''],
			'status_code' => ['type' => self::STR, 'maxLength' => 25, 'nullable' => true],
			'diagnostic_info' => ['type' => self::STR, 'nullable' => true]
		];
		$structure->getters = [];
		$structure->relations = [
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