<?php

namespace XF\AddOn\DataType;

class AdvertisingPosition extends AbstractDataType
{
	public function getShortName()
	{
		return 'XF:AdvertisingPosition';
	}

	public function getContainerTag()
	{
		return 'advertising_positions';
	}

	public function getChildTag()
	{
		return 'advertising_position';
	}

	public function exportAddOnData($addOnId, \DOMElement $container)
	{
		$entries = $this->finder()
			->where('addon_id', $addOnId)
			->order('position_id')->fetch();

		$doc = $container->ownerDocument;

		foreach ($entries AS $entry)
		{
			$node = $doc->createElement($this->getChildTag());

			$this->exportMappedAttributes($node, $entry);

			$this->exportCdata($node, json_encode($entry->arguments));

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

		$ids = $this->pluckXmlAttribute($entries, 'position_id');
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

			/** @var \XF\Entity\Option $entity */
			$entity = isset($existing[$id]) ? $existing[$id] : $this->create();

			$entity->getBehavior('XF:DevOutputWritable')->setOption('write_dev_output', false);
			$this->importMappedAttributes($entry, $entity);

			$entity->arguments = json_decode($this->getCdataValue($entry), true);

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
		$this->deleteOrphanedSimple($addOnId, $container, 'position_id');
	}

	public function rebuildActiveChange(\XF\Entity\AddOn $addOn, array &$jobList)
	{
		\XF::runOnce('rebuild_active_' . $this->getContainerTag(), function()
		{
			/** @var \XF\Repository\Advertising $repo */
			$repo = $this->em->getRepository('XF:Advertising');
			$repo->writeAdsTemplate();
		});
	}

	protected function getMappedAttributes()
	{
		return [
			'position_id',
			'active'
		];
	}
}