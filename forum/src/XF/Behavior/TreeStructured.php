<?php

namespace XF\Behavior;

use XF\Mvc\Entity\Behavior;
use XF\Mvc\Entity\Entity;

class TreeStructured extends Behavior
{
	protected function getDefaultConfig()
	{
		return [
			'parentField' => 'parent_id',
			'orderField' => 'display_order',
			'titleField' => 'title',
			'breadcrumbField' => 'breadcrumb_data',
			'rootId' => 0,
			'rebuildExtraFields' => [],
			'rebuildService' => 'XF:RebuildNestedSet',
			
			'permissionContentType' => null
		];
	}

	protected function getDefaultOptions()
	{
		return [
			'rebuildCache' => true,
			'deleteChildAction' => 'move'
		];
	}
	
	public function preSave()
	{
		$entity = $this->entity;

		if ($entity->isUpdate() && $entity->isChanged($this->config['parentField']))
		{
			$parentId = $entity->getValue($this->config['parentField']);
			if ($parentId)
			{
				$newParent = $entity->em()->find($entity->structure()->shortName, $parentId);
				if (!$newParent || ($newParent->lft >= $entity->lft && $newParent->rgt <= $entity->rgt))
				{
					$entity->error(\XF::phrase('please_select_valid_parent'), $this->config['parentField']);
				}
			}
		}
	}

	public function postSave()
	{
		if ($this->getOption('rebuildCache'))
		{
			$rebuild = (
				!$this->entity->getValue($this->config['breadcrumbField'])
				|| $this->entity->isChanged([
					$this->config['parentField'],
					$this->config['orderField'],
					$this->config['titleField']
				])
				|| ($this->config['rebuildExtraFields'] && $this->entity->isChanged($this->config['rebuildExtraFields']))
			);
			
			if ($rebuild)
			{
				$this->scheduleNestedSetRebuild();
			}
			
			if (
				$this->config['permissionContentType']
				&& ($this->entity->isInsert() || $this->entity->isChanged($this->config['parentField']))
			)
			{
				$this->app()->jobManager()->enqueueUnique('permissionRebuild', 'XF:PermissionRebuild');
			}
		}
	}

	public function postDelete()
	{
		if ($this->getOption('deleteChildAction') == 'delete')
		{
			$this->deleteChildren();
		}
		else
		{
			$parentId = $this->entity->getValue($this->config['parentField']);
			$this->moveChildrenTo($parentId);
		}

		if ($this->getOption('rebuildCache'))
		{
			$this->scheduleNestedSetRebuild();

			if ($this->config['permissionContentType'])
			{
				$this->entity->db()->delete(
					'xf_permission_entry_content',
					'content_type = ? AND content_id = ?',
					[$this->config['permissionContentType'], $this->id()]
				);

				$this->app()->jobManager()->enqueueUnique('permissionRebuild', 'XF:PermissionRebuild');
			}
		}
	}
	
	protected function scheduleNestedSetRebuild()
	{
		$entityType = $this->entity->structure()->shortName;
		$config = [
			'parentField' => $this->config['parentField'],
			'orderField' => $this->config['orderField'],
			'titleField' => $this->config['titleField'],
			'breadcrumbField' => $this->config['breadcrumbField'],
			'rootId' => $this->config['rootId'],
		];

		\XF::runOnce('rebuildTree-' . $entityType, function() use ($entityType, $config)
		{
			/** @var \XF\Service\RebuildNestedSet $service */
			$service = $this->app()->service($this->config['rebuildService'], $entityType, $config);
			$service->rebuildNestedSetInfo();
		});
	}
	
	protected function moveChildrenTo($newParentId)
	{
		$parentField = $this->config['parentField'];

		$this->entity->db()->update(
			$this->entity->structure()->table,
			[$parentField => $newParentId],
			"`{$parentField}` = ?",
			$this->entity->getEntityId()
		);
	}
	
	protected function deleteChildren()
	{
		$finder = $this->entity->em()->getFinder($this->entity->structure()->shortName);
		$finder->where($this->config['parentField'], $this->entity->getEntityId());
		
		foreach ($finder->fetch() AS $child)
		{
			/** @var Entity $child */
			$treeStructure = $child->getBehavior('XF:TreeStructured');
			$treeStructure->setOption('deleteChildAction', $this->getOption('deleteChildAction'));
			$treeStructure->setOption('rebuildCache', false);
			$child->delete(true, false);
		}
	}
}