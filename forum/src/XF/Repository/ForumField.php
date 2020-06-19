<?php

namespace XF\Repository;

use XF\Mvc\Entity\Finder;
use XF\Mvc\Entity\Repository;

class ForumField extends AbstractFieldMap
{
	protected function getMapEntityIdentifier()
	{
		return 'XF:ForumField';
	}

	protected function getAssociationsForField(\XF\Entity\AbstractField $field)
	{
		return $field->getRelation('ForumFields');
	}

	protected function updateAssociationCache(array $cache)
	{
		$nodeIds = array_keys($cache);
		$forums = $this->em->findByIds('XF:Forum', $nodeIds);

		foreach ($forums AS $forum)
		{
			/** @var \XF\Entity\Forum $forum */
			$forum->field_cache = $cache[$forum->node_id];
			$forum->saveIfChanged();
		}
	}
}