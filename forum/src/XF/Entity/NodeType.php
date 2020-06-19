<?php

namespace XF\Entity;

use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Structure;

/**
 * COLUMNS
 * @property string node_type_id
 * @property string entity_identifier
 * @property string permission_group_id
 * @property string admin_route
 * @property string public_route
 * @property string handler_class
 *
 * GETTERS
 * @property \XF\Phrase title
 */
class NodeType extends Entity
{
	/**
	 * @return \XF\Phrase
	 */
	public function getTitle()
	{
		return \XF::phrase('node_type.' . $this->node_type_id);
	}

	public static function getStructure(Structure $structure)
	{
		$structure->table = 'xf_node_type';
		$structure->shortName = 'XF:NodeType';
		$structure->primaryKey = 'node_type_id';
		$structure->columns = [
			'node_type_id' => ['type' => self::BINARY, 'maxLength' => 25,
				'required' => true,
				'match' => 'alphanumeric'
			],
			'entity_identifier' => ['type' => self::STR, 'maxLength' => 75, 'required' => true],
			'permission_group_id' => ['type' => self::STR, 'maxLength' => 25, 'required' => true],
			'admin_route' => ['type' => self::STR, 'maxLength' => 75, 'required' => true],
			'public_route' => ['type' => self::STR, 'maxLength' => 75, 'required' => true],
			'handler_class' => ['type' => self::STR, 'maxLength' => 100, 'default' => '']
		];
		$structure->getters = [
			'title' => true
		];
		$structure->relations = [];

		return $structure;
	}
}