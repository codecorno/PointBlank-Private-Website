<?php

namespace XF\AddOn\DataType;

class CodeEvent extends AbstractDataType
{
	public function getShortName()
	{
		return 'XF:CodeEvent';
	}

	public function getContainerTag()
	{
		return 'code_events';
	}

	public function getChildTag()
	{
		return 'event';
	}

	public function exportAddOnData($addOnId, \DOMElement $container)
	{
		$entries = $this->finder()
			->where('addon_id', $addOnId)
			->order('event_id')->fetch();

		foreach ($entries AS $entry)
		{
			$node = $container->ownerDocument->createElement($this->getChildTag());

			$this->exportMappedAttributes($node, $entry);
			$this->exportCdata($node, $entry->description);

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

		$ids = $this->pluckXmlAttribute($entries, 'event_id');
		$existing = $this->findByIds($ids);

		$i = 0;
		$last = 0;
		foreach ($entries AS $entry)
		{
			$id = $ids[$i++];

			if ($i <= $start)
			{
				continue;
			}

			$entity = isset($existing[$id]) ? $existing[$id] : $this->create();

			$entity->getBehavior('XF:DevOutputWritable')->setOption('write_dev_output', false);
			$this->importMappedAttributes($entry, $entity);
			$entity->description = $this->getCdataValue($entry);
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
		$this->deleteOrphanedSimple($addOnId, $container, 'event_id');
	}

	public function rebuildActiveChange(\XF\Entity\AddOn $addOn, array &$jobList)
	{
		\XF::runOnce('rebuild_active_code_event_listeners', function()
		{
			/** @var \XF\Repository\CodeEventListener $repo */
			$repo = $this->em->getRepository('XF:CodeEventListener');
			$repo->rebuildListenerCache();
		});
	}

	protected function getMappedAttributes()
	{
		return [
			'event_id'
		];
	}
}