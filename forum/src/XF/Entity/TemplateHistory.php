<?php

namespace XF\Entity;

use XF\Mvc\Entity\Entity;

/**
 * COLUMNS
 * @property int|null template_history_id
 * @property string type
 * @property string title
 * @property int style_id
 * @property string template
 * @property int edit_date
 * @property int log_date
 */
class TemplateHistory extends Entity
{
	public static function getStructure(\XF\Mvc\Entity\Structure $structure)
	{
		$structure->table = 'xf_template_history';
		$structure->shortName = 'XF:TemplateHistory';
		$structure->primaryKey = 'template_history_id';
		$structure->columns = [
			'template_history_id' => ['type' => self::UINT, 'autoIncrement' => true, 'nullable' => true],
			'type' => ['type' => self::STR, 'required' => true,
				'allowedValues' => ['public', 'admin', 'email']
			],
			'title' => ['type' => self::STR, 'maxLength' => 50,
				'required' => 'please_enter_valid_title',
				'match' => 'alphanumeric_dot'
			],
			'style_id' => ['type' => self::UINT, 'required' => true],
			'template' => ['type' => self::STR, 'default' => ''],
			'edit_date' => ['type' => self::UINT, 'default' => 0],
			'log_date' => ['type' => self::UINT, 'default' => 0]
		];

		return $structure;
	}
}