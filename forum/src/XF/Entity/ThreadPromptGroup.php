<?php

namespace XF\Entity;

use XF\Mvc\Entity\Structure;

/**
 * COLUMNS
 * @property int|null prompt_group_id
 * @property int display_order
 *
 * GETTERS
 * @property \XF\Phrase|string title
 *
 * RELATIONS
 * @property \XF\Entity\Phrase MasterTitle
 * @property \XF\Mvc\Entity\AbstractCollection|\XF\Entity\ThreadPrompt[] Prompts
 */
class ThreadPromptGroup extends AbstractPromptGroup
{
	protected function getClassIdentifier()
	{
		return 'XF:ThreadPrompt';
	}

	protected static function getContentType()
	{
		return 'thread';
	}

	public static function getStructure(Structure $structure)
	{
		self::setupDefaultStructure(
			$structure,
			'xf_thread_prompt_group',
			'XF:ThreadPromptGroup',
			'XF:ThreadPrompt'
		);

		return $structure;
	}
}