<?php

namespace XF\AddOn\DataType;

class TemplateModification extends AbstractDataType
{
	public function getShortName()
	{
		return 'XF:TemplateModification';
	}

	public function getContainerTag()
	{
		return 'template_modifications';
	}

	public function getChildTag()
	{
		return 'modification';
	}

	public function exportAddOnData($addOnId, \DOMElement $container)
	{
		$entries = $this->finder()
			->where('addon_id', $addOnId)
			->order(['template', 'modification_key'])->fetch();
		foreach ($entries AS $entry)
		{
			$node = $container->ownerDocument->createElement($this->getChildTag());

			$this->exportMappedAttributes($node, $entry);

			$this->exportCdataToNewNode($node, 'find', $entry);
			$this->exportCdataToNewNode($node, 'replace', $entry);

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

		$keys = $this->pluckXmlAttribute($entries, 'modification_key');
		$existing = $this->finder()
			->where('modification_key', $keys)
			->keyedBy('modification_key')
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

			$entity = isset($existing[(string)$entry['modification_key']]) ? $existing[(string)$entry['modification_key']] : $this->create();
			$entity->getBehavior('XF:DevOutputWritable')->setOption('write_dev_output', false);
			$entity->setOption('hide_errors', true);

			$this->importMappedAttributes($entry, $entity);

			$entity->find = $this->getCdataValue($entry->find);
			$entity->replace = $this->getCdataValue($entry->replace);

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
		$this->deleteOrphanedSimple($addOnId, $container, 'modification_key');
	}

	public function rebuildActiveChange(\XF\Entity\AddOn $addOn, array &$jobList)
	{
		$templateMods = $this->em->getFinder('XF:TemplateModification')
			->where('addon_id', $addOn->addon_id)
			->fetch()
			->groupBy('type');

		$conditions = [];
		foreach (['admin', 'email', 'public'] AS $type)
		{
			if (!isset($templateMods[$type]))
			{
				continue;
			}
			foreach ($templateMods[$type] AS $templateMod)
			{
				$conditions[] = [
					'type' => $type,
					'title' => $templateMod->template
				];
			}
		}
		if (!$conditions)
		{
			return;
		}

		$templates = $this->em->getFinder('XF:Template')
			->whereOr($conditions)
			->fetch();

		if ($templates)
		{
			$jobList[] = ['XF:TemplatePartialCompile', ['templateIds' => $templates->keys()]];
		}
	}

	protected function getMappedAttributes()
	{
		return [
			'type',
			'template',
			'modification_key',
			'description',
			'execution_order',
			'enabled',
			'action'
		];
	}

	protected function getMaintainedAttributes()
	{
		return [
			'enabled'
		];
	}
}