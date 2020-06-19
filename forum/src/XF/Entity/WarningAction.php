<?php

namespace XF\Entity;

use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Structure;

/**
 * COLUMNS
 * @property int|null warning_action_id
 * @property int points
 * @property string action
 * @property string action_length_type
 * @property int action_length
 * @property array extra_user_group_ids
 */
class WarningAction extends Entity
{
	public function getTempUserChangeKey()
	{
		return 'warning_action_' . $this->warning_action_id . '_' . $this->action;
	}

	protected function _preSave()
	{
		if ($this->action_length_type == 'permanent' || $this->action_length_type == 'points')
		{
			$this->action_length = 0;
		}
		else if ($this->action_length == 0)
		{
			$this->action_length_type = 'permanent';
		}
	}

	public static function getStructure(Structure $structure)
	{
		$structure->table = 'xf_warning_action';
		$structure->shortName = 'XF:WarningAction';
		$structure->primaryKey = 'warning_action_id';
		$structure->columns = [
			'warning_action_id' => ['type' => self::UINT, 'autoIncrement' => true, 'nullable' => true],
			'points' => ['type' => self::UINT, 'max' => 65535, 'min' => 1, 'default' => 1],
			'action' => ['type' => self::STR, 'default' => 'groups',
				'allowedValues' => ['ban', 'discourage', 'groups']
			],
			'action_length_type' => ['type' => self::STR, 'default' => 'permanent',
				'allowedValues' => ['points', 'permanent', 'days', 'weeks', 'months', 'years']
			],
			'action_length' => ['type' => self::UINT, 'max' => 65535, 'default' => 0],
			'extra_user_group_ids' => ['type' => self::LIST_COMMA, 'default' => [],
				'list' => ['type' => 'posint', 'unique' => true, 'sort' => SORT_NUMERIC]
			],
		];
		$structure->getters = [];
		$structure->relations = [];

		return $structure;
	}
}