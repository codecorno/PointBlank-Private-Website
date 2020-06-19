<?php

namespace XF\Entity;

use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Structure;

/**
 * COLUMNS
 * @property int|null template_map_id
 * @property int style_id
 * @property string type
 * @property string title
 * @property int template_id
 *
 * RELATIONS
 * @property \XF\Entity\Style Style
 * @property \XF\Entity\Template Template
 */
class TemplateMap extends Entity
{
	public static function getStructure(Structure $structure)
	{
		$structure->table = 'xf_template_map';
		$structure->shortName = 'XF:TemplateMap';
		$structure->primaryKey = 'template_map_id';
		$structure->columns = [
			'template_map_id' => ['type' => self::UINT, 'autoIncrement' => true, 'nullable' => true],
			'style_id' => ['type' => self::UINT, 'required' => true],
			'type' => ['type' => self::STR, 'maxLength' => 20, 'required' => true],
			'title' => ['type' => self::STR, 'maxLength' => 100, 'required' => true],
			'template_id' => ['type' => self::UINT, 'required' => true]
		];
		$structure->getters = [];
		$structure->relations = [
			'Style' => [
				'type' => self::TO_ONE,
				'entity' => 'XF:Style',
				'conditions' => 'style_id',
				'primary' => true
			],

			'Template' => [
				'type' => self::TO_ONE,
				'entity' => 'XF:Template',
				'conditions' => 'template_id',
				'primary' => true
			]
		];

		return $structure;
	}

	/**
	 * @return \XF\Repository\Template
	 */
	protected function getTemplateRepo()
	{
		return $this->repository('XF:Template');
	}
}