<?php

namespace XF\AddOn\DataType;

class Navigation extends AbstractDataType
{
	public function getShortName()
	{
		return 'XF:Navigation';
	}

	public function getContainerTag()
	{
		return 'navigation';
	}

	public function getChildTag()
	{
		return 'navigation_entry';
	}

	public function exportAddOnData($addOnId, \DOMElement $container)
	{
		$entries = $this->finder()
			->where('addon_id', $addOnId)
			->order('navigation_id')->fetch();

		foreach ($entries AS $entry)
		{
			$node = $container->ownerDocument->createElement($this->getChildTag());

			$this->exportMappedAttributes($node, $entry);

			$this->exportCdata($node, json_encode($entry->type_config));

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

		$ids = $this->pluckXmlAttribute($entries, 'navigation_id');
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
			$entity->setOption('master_import', true);

			$typeConfig = json_decode($this->getCdataValue($entry), true);

			if ($entity->is_customized)
			{
				// We aren't going to import any data as we need to maintain the parent, navigation_type_id and type_config
				// customizations. However, we do need to update the default value.

				// changes here need to be matched in \XF\Entity\Navigation
				$entity->default_value = [
					'parent_navigation_id' => (string)$entry['parent_navigation_id'],
					'navigation_type_id' => (string)$entry['navigation_type_id'],
					'type_config' => $typeConfig
				];
			}
			else
			{
				// no customizations, import everything we can
				$this->importMappedAttributes($entry, $entity);
				$entity->type_config = $typeConfig;
			}

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
		$this->deleteOrphanedSimple($addOnId, $container, 'navigation_id');
	}

	public function rebuildActiveChange(\XF\Entity\AddOn $addOn, array &$jobList)
	{
		\XF::runOnce('rebuild_active_' . $this->getContainerTag(), function()
		{
			/** @var \XF\Repository\Navigation $repo */
			$repo = $this->em->getRepository('XF:Navigation');
			$repo->rebuildNavigationCache();
		});
	}

	protected function getMappedAttributes()
	{
		return [
			'navigation_id',
			'parent_navigation_id',
			'display_order',
			'navigation_type_id',
			'enabled'
		];
	}

	protected function getMaintainedAttributes()
	{
		return [
			// 'parent_navigation_id', - this is considered a customization so should be updated if not edited
			'display_order',
			'enabled'
		];
	}
}