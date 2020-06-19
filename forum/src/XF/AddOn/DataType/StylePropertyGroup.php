<?php

namespace XF\AddOn\DataType;

class StylePropertyGroup extends AbstractDataType
{
	public function getShortName()
	{
		return 'XF:StylePropertyGroup';
	}

	public function getContainerTag()
	{
		return 'style_property_groups';
	}

	public function getChildTag()
	{
		return 'style_property_group';
	}

	public function exportAddOnData($addOnId, \DOMElement $container)
	{
		$entries = $this->finder()
			->where('addon_id', $addOnId)
			->where('style_id', 0)
			->order('group_name')->fetch();
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

		$ids = $this->pluckXmlAttribute($entries, 'group_name');
		$existing = $this->finder()
			->where('group_name', $ids)
			->where('style_id', 0)
			->keyedBy('group_name')
			->fetch();

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
			$entity->setOption('update_phrase', false);

			$this->importMappedAttributes($entry, $entity);
			$entity->style_id = 0;
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
		$this->deleteOrphanedSimple($addOnId, $container, 'group_name');
	}

	protected function getMappedAttributes()
	{
		return [
			'group_name',
			'title',
			'description',
			'display_order'
		];
	}
}