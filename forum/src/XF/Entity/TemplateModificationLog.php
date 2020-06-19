<?php

namespace XF\Entity;

use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Structure;

/**
 * COLUMNS
 * @property int template_id
 * @property int modification_id
 * @property string status
 * @property int apply_count
 *
 * RELATIONS
 * @property \XF\Entity\Template Template
 */
class TemplateModificationLog extends Entity
{
	public static function getStructure(Structure $structure)
	{
		$structure->table = 'xf_template_modification_log';
		$structure->shortName = 'XF:TemplateModificationLog';
		$structure->primaryKey = ['template_id', 'modification_id'];
		$structure->columns = [
			'template_id' => ['type' => self::UINT, 'required' => true],
			'modification_id' => ['type' => self::UINT, 'required' => true],
			'status' => ['type' => self::STR, 'maxLength' => 25, 'required' => true],
			'apply_count' => ['type' => self::UINT, 'default' => 0]
		];
		$structure->relations = [
			'Template' => [
				'entity' => 'XF:Template',
				'type' => self::TO_ONE,
				'conditions' => 'template_id'
			]
		];
		return $structure;
	}
}