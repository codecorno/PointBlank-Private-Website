<?php

namespace XF\Entity;

use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Structure;

/**
 * COLUMNS
 * @property int user_id
 * @property string alert
 */
class UserAlertOptOut extends Entity
{
	public static function getStructure(Structure $structure)
	{
		$structure->table = 'xf_user_alert_optout';
		$structure->shortName = 'XF:UserAlertOptOut';
		$structure->primaryKey = ['user_id', 'alert'];
		$structure->columns = [
			'user_id' => ['type' => self::UINT, 'required' => true],
			'alert' => ['type' => self::STR, 'required' => true]
		];
		return $structure;
	}
}