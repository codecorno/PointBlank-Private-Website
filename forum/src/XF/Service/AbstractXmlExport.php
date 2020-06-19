<?php

namespace XF\Service;

use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Finder;

abstract class AbstractXmlExport extends AbstractService
{
	abstract public function getRootName();

	public function getChildName()
	{
		return 'entry';
	}

	public function export(Finder $finder)
	{
		$document = $this->createXml();
		$root = $document->createElement($this->getRootName());

		$entities = $finder->fetch();
		if ($entities->count())
		{
			foreach ($entities AS $entity)
			{
				$node = $root->ownerDocument->createElement($this->getChildName());
				foreach ($this->getAttributes() AS $attr)
				{
					$value = $this->getValue($entity, $attr);
					if ($value === false)
					{
						continue;
					}
					$node->setAttribute($attr, $value);
				}

				$this->exportEntry($entity, $node);
				$root->appendChild($node);
			}
		}
		else
		{
			throw new \XF\PrintableException(\XF::phrase('please_select_at_least_one_x_to_export', ['tag_name' => $this->getChildName()]));
		}

		$document->appendChild($root);

		return $document;
	}

	protected function exportEntry(Entity $entity, \DOMElement $node)
	{
		return;
	}

	protected function getAttributes()
	{
		return [];
	}

	/**
	 * @return \DOMDocument
	 */
	protected function createXml()
	{
		$document = new \DOMDocument('1.0', 'utf-8');
		$document->formatOutput = true;

		return $document;
	}

	protected function getValue(Entity $entity, $attr)
	{
		$value = $entity->getValue($attr);
		if ($value === '')
		{
			return false;
		}
		if (is_bool($value))
		{
			$value = $value ? 1 : 0;
		}
		return $value;
	}

	protected function getPhrase(Entity $entity, $type)
	{
		$relation = 'Master' . ucwords($type);
		$phrase = $entity->{$relation};
		return $phrase ? $phrase->phrase_text : '';
	}

	protected function exportCdata(\DOMElement $parent, $value)
	{
		$cdata = \XF\Util\Xml::createDomCdataSection($parent->ownerDocument, $value);
		$parent->appendChild($cdata);
	}
}