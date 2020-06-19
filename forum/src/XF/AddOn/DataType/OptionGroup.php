<?php

namespace XF\AddOn\DataType;

class OptionGroup extends AbstractDataType
{
	public function getShortName()
	{
		return 'XF:OptionGroup';
	}

	public function getContainerTag()
	{
		return 'option_groups';
	}

	public function getChildTag()
	{
		return 'group';
	}

	public function exportAddOnData($addOnId, \DOMElement $container)
	{
		$entries = $this->finder()
			->where('addon_id', $addOnId)
			->order('group_id')->fetch();

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

		$ids = $this->pluckXmlAttribute($entries, 'group_id');
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
		$this->deleteOrphanedSimple($addOnId, $container, 'group_id');
	}

	protected function getMappedAttributes()
	{
		return [
			'group_id',
			'icon',
			'display_order',
			'debug_only'
		];
	}
}