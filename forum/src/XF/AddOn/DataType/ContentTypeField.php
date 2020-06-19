<?php

namespace XF\AddOn\DataType;

class ContentTypeField extends AbstractDataType
{
	public function getShortName()
	{
		return 'XF:ContentTypeField';
	}

	public function getContainerTag()
	{
		return 'content_type_fields';
	}

	public function getChildTag()
	{
		return 'field';
	}

	public function exportAddOnData($addOnId, \DOMElement $container)
	{
		$entries = $this->finder()
			->where('addon_id', $addOnId)
			->order(['content_type', 'field_name'])->fetch();

		$doc = $container->ownerDocument;

		foreach ($entries AS $entry)
		{
			$node = $doc->createElement($this->getChildTag());

			$this->exportMappedAttributes($node, $entry);
			$node->appendChild($doc->createTextNode($entry->field_value));

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

		$existing = $this->finder()->fetch()->groupBy('content_type', 'field_name');

		$i = 0;
		$last = 0;
		foreach ($entries AS $entry)
		{
			$i++;

			if ($i <= $start)
			{
				continue;
			}

			$type = (string)$entry['content_type'];
			$name = (string)$entry['field_name'];
			$entity = isset($existing[$type][$name]) ? $existing[$type][$name] : $this->create();

			$entity->getBehavior('XF:DevOutputWritable')->setOption('write_dev_output', false);
			$this->importMappedAttributes($entry, $entity);
			$entity->field_value = (string)$entry;
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
		$existing = $this->findAllForType($addOnId)->fetch()->groupBy('content_type', 'field_name');
		if (!$existing)
		{
			return;
		}

		$entries = $this->getEntries($container) ?: [];

		foreach ($entries AS $entry)
		{
			// this approach is used to workaround what appears to be a potential PHP 7.3 bug
			$attributes = $this->getSimpleAttributes($entry);
			$type = $attributes['content_type'];
			$name = $attributes['field_name'];

			if (isset($existing[$type][$name]))
			{
				unset($existing[$type][$name]);
			}
		}

		array_walk_recursive($existing, function($entity)
		{
			if ($entity instanceof \XF\Entity\ContentTypeField)
			{
				$entity->getBehavior('XF:DevOutputWritable')->setOption('write_dev_output', false);
				$entity->delete();
			}
		});
	}

	public function rebuildActiveChange(\XF\Entity\AddOn $addOn, array &$jobList)
	{
		\XF::runOnce('rebuild_active_' . $this->getContainerTag(), function()
		{
			/** @var \XF\Repository\ContentTypeField $repo */
			$repo = $this->em->getRepository('XF:ContentTypeField');
			$repo->rebuildContentTypeCache();
		});
	}

	protected function getMappedAttributes()
	{
		return [
			'content_type',
			'field_name'
		];
	}
}