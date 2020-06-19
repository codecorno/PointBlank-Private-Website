<?php

namespace XF\AddOn\DataType;

class CronEntry extends AbstractDataType
{
	public function getShortName()
	{
		return 'XF:CronEntry';
	}

	public function getContainerTag()
	{
		return 'cron';
	}

	public function getChildTag()
	{
		return 'entry';
	}

	public function exportAddOnData($addOnId, \DOMElement $container)
	{
		$entries = $this->finder()
			->where('addon_id', $addOnId)
			->order('entry_id')->fetch();

		foreach ($entries AS $entry)
		{
			$node = $container->ownerDocument->createElement($this->getChildTag());

			$this->exportMappedAttributes($node, $entry);
			$this->exportCdata($node, json_encode($entry->run_rules));

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

		$ids = $this->pluckXmlAttribute($entries, 'entry_id');
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
			$entity->run_rules = json_decode($this->getCdataValue($entry), true);
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
		$this->deleteOrphanedSimple($addOnId, $container, 'entry_id');
	}

	public function rebuildActiveChange(\XF\Entity\AddOn $addOn, array &$jobList)
	{
		\XF::runOnce('rebuild_active_' . $this->getContainerTag(), function()
		{
			/** @var \XF\Service\CronEntry\CalculateNextRun $runService */
			$runService = \XF::app()->service('XF:CronEntry\CalculateNextRun');
			$runService->updateMinimumNextRunTime();
		});
	}

	protected function getMappedAttributes()
	{
		return [
			'entry_id',
			'cron_class',
			'cron_method',
			'active'
		];
	}

	protected function getMaintainedAttributes()
	{
		return [
			'active'
		];
	}
}