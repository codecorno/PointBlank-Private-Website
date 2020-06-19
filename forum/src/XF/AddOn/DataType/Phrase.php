<?php

namespace XF\AddOn\DataType;

class Phrase extends AbstractDataType
{
	public function getShortName()
	{
		return 'XF:Phrase';
	}

	public function getContainerTag()
	{
		return 'phrases';
	}

	public function getChildTag()
	{
		return 'phrase';
	}

	public function exportAddOnData($addOnId, \DOMElement $container)
	{
		$entries = $this->finder()
			->where('addon_id', $addOnId)
			->where('language_id', 0)
			->order('title')->fetch();
		foreach ($entries AS $entry)
		{
			$node = $container->ownerDocument->createElement($this->getChildTag());

			$this->exportMappedAttributes($node, $entry);
			if ($entry['global_cache'])
			{
				$node->setAttribute('global_cache', '1');
			}
			$this->exportCdata($node, $entry->phrase_text);

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

		$ids = $this->pluckXmlAttribute($entries, 'title');
		$existing = $this->finder()->where('title', $ids)->where('language_id', 0)->keyedBy('title')->fetch();

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
			$entity->setOption('check_duplicate', false);
			$entity->getBehavior('XF:DevOutputWritable')->setOption('write_dev_output', false);

			$this->importMappedAttributes($entry, $entity);
			$entity->language_id = 0;
			$entity->global_cache = (int)$entry['global_cache'];
			$entity->phrase_text = $this->getCdataValue($entry);
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
		$this->deleteOrphanedSimple($addOnId, $container, 'title');
	}

	protected function getMappedAttributes()
	{
		return [
			'title',
			'version_id',
			'version_string'
		];
	}
}