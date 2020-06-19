<?php

namespace XF\Entity;

use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Structure;

/**
 * COLUMNS
 * @property int|null error_id
 * @property int exception_date
 * @property int|null user_id
 * @property string ip_address
 * @property string exception_type
 * @property string message
 * @property string filename
 * @property int line
 * @property string trace_string
 * @property array request_state
 *
 * RELATIONS
 * @property \XF\Entity\User User
 */
class ErrorLog extends Entity
{
	public static function getStructure(Structure $structure)
	{
		$structure->table = 'xf_error_log';
		$structure->shortName = 'XF:ErrorLog';
		$structure->primaryKey = 'error_id';
		$structure->columns = [
			'error_id' => ['type' => self::UINT, 'autoIncrement' => true, 'nullable' => true],
			'exception_date' => ['type' => self::UINT, 'default' => \XF::$time],
			'user_id' => ['type' => self::UINT, 'nullable' => true, 'default' => null],
			'ip_address' => ['type' => self::BINARY, 'maxLength' => 16, 'default' => ''],
			'exception_type' => ['type' => self::STR, 'maxLength' => 75],
			'message' => ['type' => self::STR, 'default' => ''],
			'filename' => ['type' => self::STR, 'maxLength' => 255, 'required' => true],
			'line' => ['type' => self::UINT, 'required' => true],
			'trace_string' => ['type' => self::STR, 'default' => ''],
			'request_state' => ['type' => self::JSON_ARRAY, 'default' => []]
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