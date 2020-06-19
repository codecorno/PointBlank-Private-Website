<?php

namespace XF\AddOn\DataType;

use XF\Util\Xml;

class StyleProperty extends AbstractDataType
{
	public function getShortName()
	{
		return 'XF:StyleProperty';
	}

	public function getContainerTag()
	{
		return 'style_properties';
	}

	public function getChildTag()
	{
		return 'style_property';
	}

	public function exportAddOnData($addOnId, \DOMElement $container)
	{
		$entries = $this->finder()
			->where('addon_id', $addOnId)
			->where('style_id', 0)
			->order('property_name')->fetch();

		$doc = $container->ownerDocument;

		foreach ($entries AS $entry)
		{
			$node = $doc->createElement($this->getChildTag());

			$this->exportMappedAttributes($node, $entry);

			if ($entry->css_components)
			{
				$node->setAttribute('css_components', implode(',', $entry->css_components));
			}

			if ($entry->value_parameters !== '')
			{
				$node->appendChild(Xml::createDomElement($doc, 'value_parameters', $entry->value_parameters));
			}

			$node->appendChild(Xml::createDomElement($doc, 'value', \XF\Util\Json::jsonEncodePretty($entry->property_value)));

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

		$ids = $this->pluckXmlAttribute($entries, 'property_name');
		$existing = $this->finder()
			->where('property_name', $ids)
			->where('style_id', 0)
			->keyedBy('property_name')
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

			$this->importMappedAttributes($entry, $entity);

			$cssComponents = (string)$entry['css_components'];
			$entity->css_components = $cssComponents ? explode(',', $cssComponents) : [];

			$entity->value_parameters = (string)$entry->value_parameters;

			$value = (string)$entry->value;
			$entity->property_value = json_decode($value, true);

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
		$this->deleteOrphanedSimple($addOnId, $container, 'property_name');
	}

	protected function getMappedAttributes()
	{
		return [
			'property_name',
			'group_name',
			'title',
			'description',
			'property_type',
			'value_type',
			'depends_on',
			'value_group',
			'display_order'
		];
	}
}