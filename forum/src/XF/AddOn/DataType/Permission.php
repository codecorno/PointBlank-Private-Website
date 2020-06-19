<?php

namespace XF\AddOn\DataType;

class Permission extends AbstractDataType
{
	public function getShortName()
	{
		return 'XF:Permission';
	}

	public function getContainerTag()
	{
		return 'permissions';
	}

	public function getChildTag()
	{
		return 'permission';
	}

	public function exportAddOnData($addOnId, \DOMElement $container)
	{
		$entries = $this->finder()
			->where('addon_id', $addOnId)
			->order(['permission_group_id', 'permission_id'])->fetch();

		foreach ($entries AS $entry)
		{
			$node = $container->ownerDocument->createElement($this->getChildTag());

			$this->exportMappedAttributes($node, $entry);

			$container->appendChild($node);
		}

		return $entries->count() ? true : false;
	}

	public function importAddOnData($addOnId, \SimpleXMLElement $container, $start = 0, $maxRunTime = 0)
	{
		$startTime = microtime(true);

		$entries = $this->getEntries($container, $start);
		if (!$entries)
		{
			return false;
		}

		$conditions = [];
		foreach ($entries AS $entry)
		{
			$conditions[] = [
				'permission_group_id' => (string)$entry['permission_group_id'],
				'permission_id' => (string)$entry['permission_id'],
			];
		}
		if ($conditions)
		{
			$existing = $this->finder()->whereOr($conditions)->fetch()->groupBy('permission_group_id', 'permission_id');
		}
		else
		{
			$existing = [];
		}

		$i = 0;
		$last = 0;
		foreach ($entries AS $entry)
		{
			$i++;

			if ($i <= $start)
			{
				continue;
			}

			$groupId = (string)$entry['permission_group_id'];
			$permissionId = (string)$entry['permission_id'];
			$entity = isset($existing[$groupId][$permissionId]) ? $existing[$groupId][$permissionId] : $this->create();

			$entity->getBehavior('XF:DevOutputWritable')->setOption('write_dev_output', false);
			$entity->setOption('dependent_check', false);
			$this->importMappedAttributes($entry, $entity);
			$entity->addon_id = $addOnId;

			$entity->save(true, false);

			if ($this->resume($maxRunTime, $startTime))
			{
				$last = $i;
				break;
			}
		}
		return ($last ?: false);
	}

	public function deleteOrphanedAddOnData($addOnId, \SimpleXMLElement $container)
	{
		$existing = $this->findAllForType($addOnId)->fetch()->groupBy('permission_group_id', 'permission_id');
		if (!$existing)
		{
			return;
		}

		$entries = $this->getEntries($container) ?: [];

		foreach ($entries AS $entry)
		{
			// this approach is used to workaround what appears to be a potential PHP 7.3 bug
			$attributes = $this->getSimpleAttributes($entry);
			$groupId = $attributes['permission_group_id'];
			$permissionId = $attributes['permission_id'];

			if (isset($existing[$groupId][$permissionId]))
			{
				unset($existing[$groupId][$permissionId]);
			}
		}

		array_walk_recursive($existing, function($entity)
		{
			if ($entity instanceof \XF\Entity\Permission)
			{
				$entity->getBehavior('XF:DevOutputWritable')->setOption('write_dev_output', false);
				$entity->delete();
			}
		});
	}

	public function rebuildActiveChange(\XF\Entity\AddOn $addOn, array &$jobList)
	{
		$hasPerms = $this->finder()
			->where('addon_id', $addOn->addon_id)
			->total();

		if (boolval($hasPerms))
		{
			$jobList[] = 'XF:PermissionRebuild';
		}
	}

	protected function getMappedAttributes()
	{
		return [
			'permission_group_id',
			'permission_id',
			'permission_type',
			'depend_permission_id',
			'interface_group_id',
			'display_order'
		];
	}
}