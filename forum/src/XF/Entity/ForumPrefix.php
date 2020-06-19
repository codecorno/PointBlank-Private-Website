<?php

namespace XF\Entity;

use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Structure;

/**
 * COLUMNS
 * @property int node_id
 * @property int prefix_id
 *
 * RELATIONS
 * @property \XF\Entity\ThreadPrefix Prefix
 * @property \XF\Entity\Forum Forum
 */
class ForumPrefix extends AbstractPrefixMap
{
	public static function getContainerKey()
	{
		return 'node_id';
	}

	public static function getStructure(Structure $structure)
	{
		self::setupDefaultStructure($structure, 'xf_forum_prefix', 'XF:ForumPrefix', 'XF:ThreadPrefix');

		$structure->relations['Forum'] = [
			'entity' => 'XF:Forum',
			'type' => self::TO_ONE,
			'conditions' => 'node_id',
			'primary' => true
		];

		return $structure;
	}
}