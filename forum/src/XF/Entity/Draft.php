<?php

namespace XF\Entity;

use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Structure;

/**
 * COLUMNS
 * @property int|null draft_id
 * @property string draft_key
 * @property int user_id
 * @property int last_update
 * @property string message
 * @property array extra_data
 */
class Draft extends Entity
{
	public function setExtraData($key, $value)
	{
		$extraData = $this->extra_data;
		$extraData[$key] = $value;

		$this->extra_data = $extraData;
	}

	public function unsetExtraData($key)
	{
		$extraData = $this->extra_data;
		unset($extraData[$key]);

		$this->extra_data = $extraData;
	}

	protected function _preSave()
	{
		if ($this->isUpdate() && $this->hasChanges())
		{
			$this->last_update = \XF::$time;
		}
	}

	public static function getStructure(Structure $structure)
	{
		$structure->table = 'xf_draft';
		$structure->shortName = 'XF:Draft';
		$structure->primaryKey = 'draft_id';
		$structure->columns = [
			'draft_id' => ['type' => self::UINT, 'autoIncrement' => true, 'nullable' => true],
			'draft_key' => ['type' => self::STR, 'maxLength' => 75, 'required' => true],
			'user_id' => ['type' => self::UINT, 'required' => true],
			'last_update' => ['type' => self::UINT, 'default' => \XF::$time],
			'message' => ['type' => self::STR],
			'extra_data' => ['type' => self::JSON_ARRAY, 'default' => []]
		];
		$structure->getters = [];
		$structure->relations = [];

		return $structure;
	}
}