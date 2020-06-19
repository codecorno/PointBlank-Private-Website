<?php

namespace XF\Search\Data;

use XF\Mvc\Entity\Entity;
use XF\Search\IndexRecord;
use XF\Search\MetadataStructure;

class Page extends AbstractData
{
	public function getEntityWith($forView = false)
	{
		$get = ['MasterTemplate'];

		if ($forView)
		{
			$visitor = \XF::visitor();
			$get[] = 'Node.Permissions|' . $visitor->permission_combination_id;
		}

		return $get;
	}

	public function getIndexData(Entity $entity)
	{
		/** @var \XF\Entity\Page $entity */

		$content = $entity->description . ' ' . ($entity->MasterTemplate ? $entity->MasterTemplate->template : '');

		return IndexRecord::create('page', $entity->node_id, [
			'title' => $entity->title,
			'message' => strip_tags($content),
			'date' => $entity->modified_date,
			'user_id' => 0,
			'discussion_id' => 0,
			'metadata' => $this->getMetaData($entity)
		]);
	}

	protected function getMetaData(\XF\Entity\Page $entity)
	{
		return [
			'node' => $entity->node_id
		];
	}

	public function setupMetadataStructure(MetadataStructure $structure)
	{
		$structure->addField('node', MetadataStructure::INT);
	}

	public function getResultDate(Entity $entity)
	{
		return $entity->modified_date;
	}

	public function getTemplateData(Entity $entity, array $options = [])
	{
		return [
			'page' => $entity,
			'options' => $options
		];
	}
}