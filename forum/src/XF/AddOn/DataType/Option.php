<?php

namespace XF\AddOn\DataType;

use XF\Util\Xml;

class Option extends AbstractDataType
{
	public function getShortName()
	{
		return 'XF:Option';
	}

	public function getContainerTag()
	{
		return 'options';
	}

	public function getChildTag()
	{
		return 'option';
	}

	public function exportAddOnData($addOnId, \DOMElement $container)
	{
		$entries = $this->finder()
			->where('addon_id', $addOnId)
			->order('option_id')->fetch();

		$doc = $container->ownerDocument;

		foreach ($entries AS $entry)
		{
			$node = $doc->createElement($this->getChildTag());

			$this->exportMappedAttributes($node, $entry);
			$node->appendChild(Xml::createDomElement($doc, 'default_value', $entry->getValue('default_value')));
			if ($entry->edit_format_params !== '')
			{
				$node->appendChild(Xml::createDomElement($doc, 'edit_format_params', $entry->edit_format_params));
			}
			if ($entry->sub_options)
			{
				$node->appendChild(Xml::createDomElement($doc, 'sub_options', implode("\n", $entry->sub_options)));
			}

			foreach ($entry->Relations AS $relation)
			{
				$relationNode = $doc->createElement('relation');
				$relationNode->setAttribute('group_id', $relation->group_id);
				$relationNode->setAttribute('display_order', $relation->display_order);
				$node->appendChild($relationNode);
			}

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

		$ids = $this->pluckXmlAttribute($entries, 'option_id');
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

			$entity->setOptions([
				'verify_validation_callback' => false,
				'verify_value' => false
			]);
			$entity->getBehavior('XF:DevOutputWritable')->setOption('write_dev_output', false);
			$this->importMappedAttributes($entry, $entity);
			$entity->addon_id = $addOnId;
			$entity->default_value = (string)$entry->default_value;
			$entity->edit_format_params = (string)$entry->edit_format_params;

			$subOptions = (string)$entry->sub_options;
			$entity->sub_options = strlen($subOptions) ? explode("\n", $subOptions) : [];

			$entity->save(true, false);

			$relations = [];
			foreach ($entry->relation AS $relation)
			{
				$relations[(string)$relation['group_id']] = (string)$relation['display_order'];
			}

			$entity->updateRelations($relations);

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
		$this->deleteOrphanedSimple($addOnId, $container, 'option_id');
	}

	protected function getMappedAttributes()
	{
		return [
			'option_id',
			'edit_format',
			'data_type',
			'validation_class',
			'validation_method'
		];
	}
}