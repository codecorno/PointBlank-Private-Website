<?php

namespace XF\Entity;

use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Structure;

/**
 * COLUMNS
 * @property string option_id
 * @property string group_id
 * @property int display_order
 *
 * RELATIONS
 * @property \XF\Entity\Option Option
 * @property \XF\Entity\OptionGroup OptionGroup
 */
class OptionGroupRelation extends Entity
{
	public static function getStructure(Structure $structure)
	{
		$structure->table = 'xf_option_group_relation';
		$structure->shortName = 'XF:OptionGroupRelation';
		$structure->primaryKey = ['option_id', 'group_id'];
		$structure->columns = [
			'option_id' => ['type' => self::STR, 'maxLength' => 50, 'required' => true],
			'group_id' => ['type' => self::STR, 'maxLength' => 50, 'required' => true],
			'display_order' => ['type' => self::UINT, 'default' => 1],
		];
		$structure->getters = [];
		$structure->relations = [
			'Option' => [
				'entity' => 'XF:Option',
				'type' => self::TO_ONE,
				'conditions' => 'option_id',
				'primary' => true
			],
			'OptionGroup' => [
				'entity' => 'XF:OptionGroup',
				'type' => self::TO_ONE,
				'conditions' => 'group_id',
				'primary' => true
			]
		];

		return $structure;
	}
}