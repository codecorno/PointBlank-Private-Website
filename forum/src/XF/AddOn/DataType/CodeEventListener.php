<?php

namespace XF\AddOn\DataType;

class CodeEventListener extends AbstractDataType
{
	public function getShortName()
	{
		return 'XF:CodeEventListener';
	}

	public function getContainerTag()
	{
		return 'code_event_listeners';
	}

	public function getChildTag()
	{
		return 'listener';
	}

	public function exportAddOnData($addOnId, \DOMElement $container)
	{
		$entries = $this->finder()
			->where('addon_id', $addOnId)
			->order(['event_id', 'callback_class', 'callback_method'])->fetch();

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

		$existing = [];
		foreach ($this->findAllForType($addOnId)->fetch() AS $listener)
		{
			$existing[$listener->getAddOnUniqueKey()] = $listener;
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

			$key = $this->getAddOnUniqueKeyFromXml($entry);
			$entity = isset($existing[$key]) ? $existing[$key] : $this->create();

			$entity->getBehavior('XF:DevOutputWritable')->setOption('write_dev_output', false);
			$entity->setOption('check_duplicate', false);
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
		foreach ($this->findAllForType($addOnId)->fetch() AS $listener)
		{
			$existing[$listener->getAddOnUniqueKey()] = $listener;
		}
		if (!$existing)
		{
			return;
		}

		$entries = $this->getEntries($container) ?: [];

		foreach ($entries AS $entry)
		{
			$key = $this->getAddOnUniqueKeyFromXml($entry);

			if (isset($existing[$key]))
			{
				unset($existing[$key]);
			}
		}

		foreach ($existing AS $entity)
		{
			$this->deleteEntity($entity);
		}
	}

	public function rebuildActiveChange(\XF\Entity\AddOn $addOn, array &$jobList)
	{
		\XF::runOnce('rebuild_active_' . $this->getContainerTag(), function()
		{
			/** @var \XF\Repository\CodeEventListener $repo */
			$repo = $this->em->getRepository('XF:CodeEventListener');
			$repo->rebuildListenerCache();
		});
	}

	protected function getMappedAttributes()
	{
		return [
			'event_id',
			'execute_order',
			'callback_class',
			'callback_method',
			'active',
			'hint',
			'description'
		];
	}

	protected function getAddOnUniqueKeyFromXml(\SimpleXMLElement $entry)
	{
		// getSimpleAttributes is used to workaround what appears to be a potential PHP 7.3 bug

		// this should match XF\Entity\CodeEventListener::getAddOnUniqueKey
		$attributes = $this->getSimpleAttributes($entry);
		$event = $attributes['event_id'];
		$class = $attributes['callback_class'];
		$method = $attributes['callback_method'];
		$hint = isset($attributes['hint']) ? $attributes['hint'] : '';

		return "{$event}-{$class}-{$method}-{$hint}";
	}
}