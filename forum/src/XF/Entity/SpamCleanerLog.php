<?php

namespace XF\Entity;

use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Structure;

/**
 * COLUMNS
 * @property int|null spam_cleaner_log_id
 * @property int user_id
 * @property string username
 * @property int applying_user_id
 * @property string applying_username
 * @property int application_date
 * @property array data
 * @property int restored_date
 *
 * RELATIONS
 * @property \XF\Entity\User User
 * @property \XF\Entity\User ApplyingUser
 */
class SpamCleanerLog extends Entity
{
	public static function getStructure(Structure $structure)
	{
		$structure->table = 'xf_spam_cleaner_log';
		$structure->shortName = 'XF:SpamCleanerLog';
		$structure->primaryKey = 'spam_cleaner_log_id';
		$structure->columns = [
			'spam_cleaner_log_id' => ['type' => self::UINT, 'autoIncrement' => true, 'nullable' => true],
			'user_id' => ['type' => self::UINT, 'required' => true],
			'username' => ['type' => self::STR, 'required' => true],
			'applying_user_id' => ['type' => self::UINT, 'required' => true],
			'applying_username' => ['type' => self::STR, 'required' => true],
			'application_date' => ['type' => self::UINT, 'default' => \XF::$time],
			'data' => ['type' => self::JSON_ARRAY, 'default' => []],
			'restored_date' => ['type' => self::UINT, 'default' => 0],
		];
		$structure->getters = [];
		$structure->relations = [
			'User' => [
				'entity' => 'XF:User',
				'type' => self::TO_ONE,
				'conditions' => 'user_id',
				'primary' => true
			],
			'ApplyingUser' => [
				'entity' => 'XF:User',
				'type' => self::TO_ONE,
				'conditions' => 'applying_user_id',
				'primary' => true
			],
		];

		return $structure;
	}
}