<?php

namespace XF\Search\Data;

use XF\Mvc\Entity\Entity;
use XF\Search\IndexRecord;
use XF\Search\MetadataStructure;

class Thread extends AbstractData
{
	public function getEntityWith($forView = false)
	{
		$get = ['Forum', 'FirstPost'];
		if ($forView)
		{
			$get[] = 'User';

			$visitor = \XF::visitor();
			$get[] = 'Forum.Node.Permissions|' . $visitor->permission_combination_id;
		}

		return $get;
	}

	public function getIndexData(Entity $entity)
	{
		/** @var \XF\Entity\Thread $entity */

		if (!$entity->Forum || $entity->discussion_type == 'redirect')
		{
			return null;
		}

		/** @var \XF\Entity\Post|null $firstPost */
		$firstPost = $entity->FirstPost;

		$index = IndexRecord::create('thread', $entity->thread_id, [
			'title' => $entity->title_,
			'message' => $firstPost ? $firstPost->message_ : '',
			'date' => $entity->post_date,
			'user_id' => $entity->user_id,
			'discussion_id' => $entity->thread_id,
			'metadata' => $this->getMetaData($entity)
		]);

		if (!$entity->isVisible())
		{
			$index->setHidden();
		}

		if ($entity->tags)
		{
			$index->indexTags($entity->tags);
		}

		return $index;
	}

	protected function getMetaData(\XF\Entity\Thread $entity)
	{
		$metadata = [
			'node' => $entity->node_id,
			'thread' => $entity->thread_id
		];
		if ($entity->prefix_id)
		{
			$metadata['prefix'] = $entity->prefix_id;
		}

		return $metadata;
	}

	public function setupMetadataStructure(MetadataStructure $structure)
	{
		$structure->addField('node', MetadataStructure::INT);
		$structure->addField('thread', MetadataStructure::INT);
		$structure->addField('prefix', MetadataStructure::INT);
	}

	public function getResultDate(Entity $entity)
	{
		return $entity->post_date;
	}

	public function getTemplateData(Entity $entity, array $options = [])
	{
		return [
			'thread' => $entity,
			'options' => $options
		];
	}

	public function canUseInlineModeration(Entity $entity, &$error = null)
	{
		/** @var \XF\Entity\Thread $entity */
		return $entity->canUseInlineModeration($error);
	}
}