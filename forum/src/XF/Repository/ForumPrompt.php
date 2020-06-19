<?php

namespace XF\Repository;

use XF\Mvc\Entity\Finder;
use XF\Mvc\Entity\Repository;

class ForumPrompt extends AbstractPromptMap
{
	protected function getMapEntityIdentifier()
	{
		return 'XF:ForumPrompt';
	}

	protected function getAssociations(\XF\Entity\AbstractPrompt $prompt)
	{
		return $prompt->getRelation('ForumPrompts');
	}

	protected function updateAssociationCache(array $cache)
	{
		$nodeIds = array_keys($cache);
		$forums = $this->em->findByIds('XF:Forum', $nodeIds);

		foreach ($forums AS $forum)
		{
			/** @var \XF\Entity\Forum $forum */
			$forum->prompt_cache = $cache[$forum->node_id];
			$forum->saveIfChanged();
		}
	}
}