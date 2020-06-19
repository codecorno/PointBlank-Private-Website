<?php

namespace XF\Entity;

use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Structure;

/**
 * COLUMNS
 * @property int node_id
 * @property string field_id
 *
 * RELATIONS
 * @property \XF\Entity\ThreadField Field
 * @property \XF\Entity\Forum Forum
 */
class ForumField extends AbstractFieldMap
{
	public static function getContainerKey()
	{
		return 'node_id';
	}

	public static function getStructure(Structure $structure)
	{
		self::setupDefaultStructure($structure, 'xf_forum_field', 'XF:ForumField', 'XF:ThreadField');

		$structure->relations['Forum'] = [
			'entity' => 'XF:Forum',
			'type' => self::TO_ONE,
			'conditions' => 'node_id',
			'primary' => true
		];

		return $structure;
	}
}