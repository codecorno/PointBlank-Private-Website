<?php

namespace XF\Entity;

use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Structure;

/**
 * COLUMNS
 * @property string|null attempt_id
 * @property string login
 * @property string ip_address
 * @property int attempt_date
 */
class LoginAttempt extends Entity
{
	public static function getStructure(Structure $structure)
	{
		$structure->table = 'xf_login_attempt';
		$structure->shortName = 'XF:LoginAttempt';
		$structure->primaryKey = 'attempt_id';
		$structure->columns = [
			'attempt_id' => ['type' => self::STR, 'autoIncrement' => true, 'nullable' => true],
			'login' => ['type' => self::STR, 'maxLength' => 60, 'required' => true],
			'ip_address' => ['type' => self::BINARY, 'maxLength' => 16, 'default' => ''],
			'attempt_date' => ['type' => self::UINT, 'default' => time()]
		];
		$structure->getters = [];
		$structure->relations = [];

		return $structure;
	}
}