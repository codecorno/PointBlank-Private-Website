<?php

namespace XF\Entity;

use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Structure;

/**
 * COLUMNS
 * @property string|null attempt_id
 * @property int user_id
 * @property int attempt_date
 *
 * RELATIONS
 * @property \XF\Entity\User User
 */
class TfaAttempt extends Entity
{
	public static function getStructure(Structure $structure)
	{
		$structure->table = 'xf_tfa_attempt';
		$structure->shortName = 'XF:TfaAttempt';
		$structure->primaryKey = 'attempt_id';
		$structure->columns = [
			'attempt_id' => ['type' => self::STR, 'autoIncrement' => true, 'nullable' => true],
			'user_id' => ['type' => self::UINT,  'required' => true],
			'attempt_date' => ['type' => self::UINT, 'default' => time()]
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