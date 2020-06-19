<?php

namespace XF\Entity;

use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Structure;

/**
 * COLUMNS
 * @property int user_id
 * @property string push
 */
class UserPushOptOut extends Entity
{
	public static function getStructure(Structure $structure)
	{
		$structure->table = 'xf_user_push_optout';
		$structure->shortName = 'XF:UserPushOptOut';
		$structure->primaryKey = ['user_id', 'push'];
		$structure->columns = [
			'user_id' => ['type' => self::UINT, 'required' => true],
			'push' => ['type' => self::STR, 'required' => true]
		];
		return $structure;
	}
}