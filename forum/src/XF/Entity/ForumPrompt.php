<?php

namespace XF\Entity;

use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Structure;

/**
 * COLUMNS
 * @property int node_id
 * @property int prompt_id
 *
 * RELATIONS
 * @property \XF\Entity\ThreadPrompt Prompt
 * @property \XF\Entity\Forum Forum
 */
class ForumPrompt extends AbstractPromptMap
{
	public static function getContainerKey()
	{
		return 'node_id';
	}

	public static function getStructure(Structure $structure)
	{
		self::setupDefaultStructure($structure, 'xf_forum_prompt', 'XF:ForumPrompt', 'XF:ThreadPrompt');

		$structure->relations['Forum'] = [
			'entity' => 'XF:Forum',
			'type' => self::TO_ONE,
			'conditions' => 'node_id',
			'primary' => true
		];

		return $structure;
	}
}