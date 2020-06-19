<?php

namespace XF\AddOn\DataType;

class Route extends AbstractDataType
{
	public function getShortName()
	{
		return 'XF:Route';
	}

	public function getContainerTag()
	{
		return 'routes';
	}

	public function getChildTag()
	{
		return 'route';
	}

	public function exportAddOnData($addOnId, \DOMElement $container)
	{
		$entries = $this->finder()
			->where('addon_id', $addOnId)
			->order(['route_type', 'route_prefix', 'sub_name'])->fetch();
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
		$existing = [];
		foreach ($entries AS $entry)
		{
			$conditions[] = [
				'route_type' => (string)$entry['route_type'],
				'route_prefix' => (string)$entry['route_prefix'],
				'sub_name' => (string)$entry['sub_name'],
			];
		}
		if ($conditions)
		{
			foreach ($this->finder()->whereOr($conditions)->fetch() AS $route)
			{
				$existing[$route->route_type][$route->route_prefix][$route->sub_name] = $route;
			}
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

			$type = (string)$entry['route_type'];
			$prefix = (string)$entry['route_prefix'];
			$subName = (string)$entry['sub_name'];
			$entity = isset($existing[$type][$prefix][$subName]) ? $existing[$type][$prefix][$subName] : $this->create();

			$entity->setOptions([
				'check_duplicate' => false
			]);

			$entity->getBehavior('XF:DevOutputWritable')->setOption('write_dev_output', false);

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
		$existing = [];
		foreach ($this->findAllForType($addOnId)->fetch() AS $route)
		{
			$existing[$route->route_type][$route->route_prefix][$route->sub_name] = $route;
		}
		if (!$existing)
		{
			return;
		}

		$entries = $this->getEntries($container) ?: [];

		foreach ($entries AS $entry)
		{
			// this approach is used to workaround what appears to be a potential PHP 7.3 bug
			$attributes = $this->getSimpleAttributes($entry);
			$type = $attributes['route_type'];
			$prefix = $attributes['route_prefix'];
			$subName = isset($attributes['sub_name']) ? $attributes['sub_name'] : '';

			if (isset($existing[$type][$prefix][$subName]))
			{
				unset($existing[$type][$prefix][$subName]);
			}
		}

		array_walk_recursive($existing, function($entity)
		{
			if ($entity instanceof \XF\Entity\Route)
			{
				$entity->getBehavior('XF:DevOutputWritable')->setOption('write_dev_output', false);
				$entity->delete();
			}
		});
	}

	public function rebuildActiveChange(\XF\Entity\AddOn $addOn, array &$jobList)
	{
		\XF::runOnce('rebuild_active_' . $this->getContainerTag(), function()
		{
			/** @var \XF\Repository\Route $repo */
			$repo = $this->em->getRepository('XF:Route');
			$repo->rebuildRouteCache('public');
			$repo->rebuildRouteCache('admin');
		});
	}

	protected function getMappedAttributes()
	{
		return [
			'route_type',
			'route_prefix',
			'sub_name',
			'format',
			'build_class',
			'build_method',
			'controller',
			'context',
			'action_prefix'
		];
	}
}