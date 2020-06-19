<?php

namespace XF\AddOn\DataType;

use XF\Mvc\Entity\Entity;

class Template extends AbstractDataType
{
	public function getShortName()
	{
		return 'XF:Template';
	}

	public function getContainerTag()
	{
		return 'templates';
	}

	public function getChildTag()
	{
		return 'template';
	}

	public function exportAddOnData($addOnId, \DOMElement $container)
	{
		$entries = $this->finder()
			->where('addon_id', $addOnId)
			->where('style_id', 0)
			->order(['type', 'title'])->fetch();
		foreach ($entries AS $entry)
		{
			$node = $container->ownerDocument->createElement($this->getChildTag());

			$this->exportMappedAttributes($node, $entry);
			$this->exportCdata($node, $entry->template);

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

		$conditions = [];
		foreach ($entries AS $entry)
		{
			$conditions[] = [
				'type' => (string)$entry['type'],
				'title' => (string)$entry['title'],
			];
		}
		if ($conditions)
		{
			$existing = $this->finder()
				->whereOr($conditions)
				->where('style_id', 0)
				->fetch()
				->groupBy('type', 'title');
		}
		else
		{
			$existing = [];
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

			$type = (string)$entry['type'];
			$title = (string)$entry['title'];
			$entity = isset($existing[$type][$title]) ? $existing[$type][$title] : $this->create();
			$entity->setOption('check_duplicate', false);
			$entity->setOption('report_modification_errors', false);
			$entity->getBehavior('XF:DevOutputWritable')->setOption('write_dev_output', false);

			$this->importMappedAttributes($entry, $entity);
			$entity->style_id = 0;
			$entity->template = $this->getCdataValue($entry);
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
		$existing = $this->findAllForType($addOnId)->where('style_id', 0)->fetch()->groupBy('type', 'title');
		if (!$existing)
		{
			return;
		}

		$entries = $this->getEntries($container) ?: [];

		foreach ($entries AS $entry)
		{
			// this approach is used to workaround what appears to be a potential PHP 7.3 bug
			$attributes = $this->getSimpleAttributes($entry);
			$type = $attributes['type'];
			$title = $attributes['title'];

			if (isset($existing[$type][$title]))
			{
				unset($existing[$type][$title]);
			}
		}

		array_walk_recursive($existing, function($entity)
		{
			if ($entity instanceof \XF\Entity\Template)
			{
				$entity->getBehavior('XF:DevOutputWritable')->setOption('write_dev_output', false);
				$entity->delete();
			}
		});
	}

	/**
	 * @param \XF\Entity\Template $entity
	 */
	protected function deleteEntity(Entity $entity)
	{
		if ($entity->style_id > 0)
		{
			return;
		}
		parent::deleteEntity($entity);
	}

	protected function getMappedAttributes()
	{
		return [
			'type',
			'title',
			'version_id',
			'version_string'
		];
	}
}