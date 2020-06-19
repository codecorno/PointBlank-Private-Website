<?php

namespace XF\Entity;

use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Structure;

/**
 * COLUMNS
 * @property int|null action_trigger_id
 * @property int warning_action_id
 * @property int user_id
 * @property int trigger_points
 * @property int action_date
 * @property string action
 * @property int min_unban_date
 */
class WarningActionTrigger extends Entity
{
	public static function getStructure(Structure $structure)
	{
		$structure->table = 'xf_warning_action_trigger';
		$structure->shortName = 'XF:WarningActionTrigger';
		$structure->primaryKey = 'action_trigger_id';
		$structure->columns = [
			'action_trigger_id' => ['type' => self::UINT, 'autoIncrement' => true, 'nullable' => true],
			'warning_action_id' => ['type' => self::UINT, 'required' => true],
			'user_id' => ['type' => self::UINT, 'required' => true],
			'trigger_points' => ['type' => self::UINT, 'required' => true],
			'action_date' => ['type' => self::UINT, 'default' => \XF::$time],
			'action' => ['type' => self::STR, 'maxLength' => 25, 'required' => true],
			'min_unban_date' => ['type' => self::UINT, 'default' => 0],
		];
		$structure->getters = [];
		$structure->relations = [];

		return $structure;
	}
}