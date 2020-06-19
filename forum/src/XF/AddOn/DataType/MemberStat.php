<?php

namespace XF\AddOn\DataType;

class MemberStat extends AbstractDataType
{
	public function getShortName()
	{
		return 'XF:MemberStat';
	}

	public function getContainerTag()
	{
		return 'member_stats';
	}

	public function getChildTag()
	{
		return 'member_stat';
	}

	public function exportAddOnData($addOnId, \DOMElement $container)
	{
		$entries = $this->finder()
			->where('addon_id', $addOnId)
			->order('member_stat_key')->fetch();
		foreach ($entries AS $entry)
		{
			$node = $container->ownerDocument->createElement($this->getChildTag());

			$this->exportMappedAttributes($node, $entry);

			$this->exportCdata($node, json_encode($entry->criteria));

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

		$keys = $this->pluckXmlAttribute($entries, 'member_stat_key');
		$existing = $this->finder()
			->where('member_stat_key', $keys)
			->keyedBy('member_stat_key')
			->fetch();

		$i = 0;
		$last = 0;
		foreach ($entries AS $entry)
		{
			$i++;

			if ($i <= $start)
			{
				continue;
			}

			$id = (string)$entry['member_stat_key'];

			$entity = isset($existing[$id]) ? $existing[$id] : $this->create();
			$entity->getBehavior('XF:DevOutputWritable')->setOption('write_dev_output', false);

			$this->importMappedAttributes($entry, $entity);

			$entity->criteria = json_decode($this->getCdataValue($entry), true);

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
		$this->deleteOrphanedSimple($addOnId, $container, 'member_stat_key');
	}

	protected function getMappedAttributes()
	{
		return [
			'member_stat_key',
			'callback_class',
			'callback_method',
			'sort_order',
			'sort_direction',
			'permission_limit',
			'show_value',
			'user_limit',
			'display_order',
			'overview_display',
			'active',
			'cache_lifetime'
		];
	}

	protected function getMaintainedAttributes()
	{
		return [
			'overview_display',
			'display_order',
			'user_limit',
			'active'
		];
	}
}