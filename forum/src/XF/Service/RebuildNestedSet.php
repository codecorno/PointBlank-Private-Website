<?php

namespace XF\Service;

use XF\Mvc\Entity\Entity;

class RebuildNestedSet extends AbstractService
{
	/**
	 * @var \XF\Tree
	 */
	protected $tree;

	protected $entityType;
	protected $config;

	public function __construct(\XF\App $app, $entityType, array $config = [])
	{
		parent::__construct($app);
		$this->entityType = $entityType;
		$this->config = array_replace($this->getDefaultConfig(), $config);
	}

	protected function getDefaultConfig()
	{
		return [
			'parentField' => 'parent_id',
			'orderField' => 'display_order',
			'titleField' => 'title',
			'breadcrumbField' => 'breadcrumb_data',
			'rootId' => 0
		];
	}

	protected function setupTree()
	{
		if ($this->tree)
		{
			return;
		}

		$entities = $this->getEntities();
		$this->tree = new \XF\Tree($entities, $this->config['parentField'], $this->config['rootId']);
	}

	protected function getEntities()
	{
		return $this->finder($this->entityType)
			->order($this->config['orderField'])
			->fetch();
	}

	public function rebuildNestedSetInfo()
	{
		$this->setupTree();

		$passDown = $this->getBasePassableData();

		$this->db()->beginTransaction();
		$this->_rebuildNestedSetInfo($this->config['rootId'], $passDown);
		$this->db()->commit();
	}

	protected function _rebuildNestedSetInfo($id, array $passDown, $depth = -1, &$counter = 0)
	{
		/** @var \XF\Mvc\Entity\Entity $entity */
		$entity = $this->tree->getData($id);

		if ($entity)
		{
			$counter++;
		}
		$left = $counter;

		if ($entity)
		{
			$selfData = $this->getSelfData($passDown, $entity, $depth, $left);
			$childPassDown = $this->getChildPassableData($passDown, $entity, $depth, $left);
		}
		else
		{
			$selfData = [];
			$childPassDown = $passDown;
		}

		foreach ($this->tree->childIds($id) AS $childId)
		{
			$this->_rebuildNestedSetInfo($childId, $childPassDown, $depth + 1, $counter);
		}

		if ($entity)
		{
			$counter++;
		}
		$right = $counter;

		if ($entity)
		{
			$updateData = $selfData + [
				'lft' => $left,
				'rgt' => $right,
				'depth' => $depth
			];

			$entity->fastUpdate($updateData);
		}
	}

	protected function getBasePassableData()
	{
		return [
			$this->config['breadcrumbField'] => []
		];
	}

	protected function getSelfData(array $passData, Entity $entity, $depth, $left)
	{
		return $passData;
	}

	protected function getChildPassableData(array $passData, Entity $entity, $depth, $left)
	{
		$breadcrumbField = $this->config['breadcrumbField'];

		$passData[$breadcrumbField][$entity->getEntityId()] = $this->getBreadcrumbEntry($entity, $depth, $left);

		return $passData;
	}

	protected function getBreadcrumbEntry(Entity $entity, $depth, $left)
	{
		$titleField = $this->config['titleField'];
		$title = $entity[$titleField];
		$id = $entity->getEntityId();
		$idField = $entity->structure()->primaryKey;

		return [
			$idField => $id,
			$titleField => $title,
			'depth' => $depth,
			'lft' => $left
		];
	}
}