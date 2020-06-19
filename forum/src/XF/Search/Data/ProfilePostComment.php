<?php

namespace XF\Search\Data;

use XF\Mvc\Entity\Entity;
use XF\Search\IndexRecord;
use XF\Search\MetadataStructure;

class ProfilePostComment extends AbstractData
{
	public function getEntityWith($forView = false)
	{
		$get = ['ProfilePost', 'ProfilePost.ProfileUser'];
		if ($forView)
		{
			$get[] = 'ProfilePost.ProfileUser.Privacy';
			$get[] = 'User';
		}

		return $get;
	}

	public function getIndexData(Entity $entity)
	{
		/** @var \XF\Entity\ProfilePostComment $entity */

		if (!$entity->ProfilePost || !$entity->ProfilePost->ProfileUser)
		{
			return null;
		}

		$index = IndexRecord::create('profile_post_comment', $entity->profile_post_comment_id, [
			'title' => '',
			'message' => $entity->message_,
			'date' => $entity->comment_date,
			'user_id' => $entity->user_id,
			'discussion_id' => $entity->profile_post_id,
			'metadata' => $this->getMetaData($entity)
		]);

		if (!$entity->isVisible())
		{
			$index->setHidden();
		}

		return $index;
	}

	protected function getMetaData(\XF\Entity\ProfilePostComment $entity)
	{
		$metadata = [];

		$metadata['profile_user'] = $entity->ProfilePost->profile_user_id;

		return $metadata;
	}

	public function setupMetadataStructure(MetadataStructure $structure)
	{
		$structure->addField('profile_user', MetadataStructure::INT);
	}

	public function getResultDate(Entity $entity)
	{
		return $entity->comment_date;
	}

	public function getTemplateData(Entity $entity, array $options = [])
	{
		return [
			'comment' => $entity,
			'options' => $options
		];
	}
}