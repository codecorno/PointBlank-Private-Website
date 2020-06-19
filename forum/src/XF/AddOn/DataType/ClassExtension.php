<?php

namespace XF\AddOn\DataType;

class ClassExtension extends AbstractDataType
{
	public function getShortName()
	{
		return 'XF:ClassExtension';
	}

	public function getContainerTag()
	{
		return 'class_extensions';
	}

	public function getChildTag()
	{
		return 'extension';
	}

	public function exportAddOnData($addOnId, \DOMElement $container)
	{
		$entries = $this->finder()
			->where('addon_id', $addOnId)
			->order(['from_class', 'to_class'])->fetch();

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
				'from_class' => (string)$entry['from_class'],
				'to_class' => (string)$entry['to_class'],
			];
		}
		if ($conditions)
		{
			$existing = $this->finder()->whereOr($conditions)->fetch()->groupBy('from_class', 'to_class');
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

			$from = (string)$entry['from_class'];
			$to = (string)$entry['to_class'];
			$entity = isset($existing[$from][$to]) ? $existing[$from][$to] : $this->create();

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
		$existing = $this->findAllForType($addOnId)->fetch()->groupBy('from_class', 'to_class');
		if (!$existing)
		{
			return;
		}

		$entries = $this->getEntries($container) ?: [];

		foreach ($entries AS $entry)
		{
			// this approach is used to workaround what appears to be a potential PHP 7.3 bug
			$attributes = $this->getSimpleAttributes($entry);
			$from = $attributes['from_class'];
			$to = $attributes['to_class'];

			if (isset($existing[$from][$to]))
			{
				unset($existing[$from][$to]);
			}
		}

		array_walk_recursive($existing, function($entity)
		{
			if ($entity instanceof \XF\Entity\ClassExtension)
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
			/** @var \XF\Repository\ClassExtension $repo */
			$repo = $this->em->getRepository('XF:ClassExtension');
			$repo->rebuildExtensionCache();
		});
	}

	protected function getMappedAttributes()
	{
		return [
			'from_class',
			'to_class',
			'execute_order',
			'active'
		];
	}
}