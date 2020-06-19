<?php

namespace XF\AddOn\DataType;

use XF\Mvc\Entity\Entity;

abstract class AbstractDataType
{
	/**
	 * @var \XF\Mvc\Entity\Manager
	 */
	protected $em;

	abstract public function getShortName();

	abstract public function getContainerTag();

	abstract public function getChildTag();

	abstract public function exportAddOnData($addOnId, \DOMElement $container);

	abstract public function importAddOnData($addOnId, \SimpleXMLElement $container, $start = 0, $maxRunTime = 0);

	abstract public function deleteOrphanedAddOnData($addOnId, \SimpleXMLElement $container);

	const USE_AUTO_CDATA = false;

	public function __construct(\XF\Mvc\Entity\Manager $em)
	{
		$this->em = $em;
		$this->structure = $em->getEntityStructure($this->getShortName());
	}

	public function getTypeFileName()
	{
		return $this->getContainerTag() . '.xml';
	}

	/**
	 * @param $dataDir
	 *
	 * @return bool|\SimpleXMLElement
	 */
	public function openTypeFile($dataDir)
	{
		$path = $dataDir . \XF::$DS . $this->getTypeFileName();

		if (!file_exists($path))
		{
			return false;
		}

		return \XF\Util\Xml::openFile($dataDir . \XF::$DS . $this->getTypeFileName());
	}

	public function rebuildActiveChange(\XF\Entity\AddOn $addOn, array &$jobList)
	{
		return;
	}

	public function updateAddOnId($oldId, $newId)
	{
		$table = $this->em->getEntityStructure($this->getShortName())->table;
		$this->em->getDb()->update(
			$table,
			['addon_id' => $newId],
			'addon_id = ?', $oldId
		);
	}

	protected function deleteOrphanedSimple($addOnId, \SimpleXMLElement $container, $idKey)
	{
		$existing = $this->findAllForType($addOnId)->keyedBy($idKey)->fetch();
		if (!$existing)
		{
			return;
		}

		$entries = $this->getEntries($container) ?: [];

		foreach ($entries AS $entry)
		{
			$id = (string)$entry[$idKey];
			if (isset($existing[$id]))
			{
				unset($existing[$id]);
			}
		}

		foreach ($existing AS $entity)
		{
			$this->deleteEntity($entity);
		}
	}

	public function deleteAddOnData($addOnId, $maxRunTime = 0)
	{
		$start = microtime(true);

		/** @var \XF\Mvc\Entity\Entity $entity */
		foreach ($this->findAllForType($addOnId)->fetch() AS $entity)
		{
			$this->deleteEntity($entity);

			if ($maxRunTime > 0 && (microtime(true) - $start) > $maxRunTime)
			{
				return false;
			}
		}

		return true;
	}

	protected function deleteEntity(Entity $entity)
	{
		$entity->getBehavior('XF:DevOutputWritable')->setOption('write_dev_output', false);
		$entity->delete();
	}

	/**
	 * @param \SimpleXMLElement $container
	 *
	 * @return bool
	 */
	public function hasEntries(\SimpleXMLElement $container)
	{
		/** @var \SimpleXMLElement $entries */
		$entries = $container->{$this->getChildTag()};
		return ($entries->count() > 0);
	}

	/**
	 * @param \SimpleXMLElement $container
	 * @param $start
	 *
	 * @return bool|\SimpleXMLElement
	 */
	public function getEntries(\SimpleXMLElement $container, $start = 0)
	{
		/** @var \SimpleXMLElement $entries */
		$entries = $container->{$this->getChildTag()};
		if (!$entries->count() || $start >= $entries->count())
		{
			return false;
		}

		return $entries;
	}

	protected function resume($maxRunTime, $startTime)
	{
		if ($maxRunTime && (microtime(true) - $startTime) > $maxRunTime)
		{
			return true;
		}
		return false;
	}

	protected function getMappedAttributes()
	{
		return [];
	}

	protected function getMaintainedAttributes()
	{
		return [];
	}

	protected function exportArrayToAttributes(\DOMElement $tag, $attr, Entity $entity)
	{
		foreach ($entity->$attr AS $key => $value)
		{
			if ($value === '')
			{
				continue;
			}
			if (is_bool($value))
			{
				$value = $value ? 1 : 0;
			}
			$tag->setAttribute($attr . '_' . $key, $value);
		}
	}

	protected function exportCdataToNewNode(\DOMElement $tag, $attr, Entity $entity)
	{
		$newNode = $tag->ownerDocument->createElement($attr);
		$this->exportCdata($newNode, $entity->{$attr});
		$tag->appendChild($newNode);
	}

	protected function exportMappedAttributes(\DOMElement $tag, Entity $entity)
	{
		foreach ($this->getMappedAttributes() AS $attr)
		{
			$value = $entity->getValue($attr);

			if ($value !== '')
			{
				if (is_bool($value))
				{
					$value = $value ? 1 : 0;
				}

				// CURRENTLY NOT USED DUE TO BC ISSUES WITH 2.0.x < 2.0.4
				if (self::USE_AUTO_CDATA && is_string($value) && !preg_match('/^[^<>&"]*$/si', $value))
				{
					// This exports CDATA, but does not escape `]]>` like \XF\Util\XML::createDomCdataSection(),
					// because it needs to be transparently importable.
					// If you need to export values containing `]]>`, use exportCdataToNewNode()
					// and remove this attribute from exportMappedAttributes()

					$newNode = $tag->ownerDocument->createElement($attr);

					$newNode->appendChild(
						$newNode->ownerDocument->createCDATASection($entity->{$attr})
					);

					$tag->appendChild($newNode);
				}
				else
				{
					$tag->setAttribute($attr, $value);
				}
			}
		}
	}

	protected function importMappedAttributes(\SimpleXMLElement $el, Entity $entity, $allowMaintained = true)
	{
		if ($entity->exists() && $allowMaintained)
		{
			$maintained = $this->getMaintainedAttributes();
		}
		else
		{
			$maintained = [];
		}

		foreach ($this->getMappedAttributes() AS $attr)
		{
			if (in_array($attr, $maintained))
			{
				continue;
			}

			if (self::USE_AUTO_CDATA)
			{
				if (isset($el[$attr]))
				{
					$entity->$attr = (string)$el[$attr];
				}
				else if (isset($el->{$attr}))
				{
					$entity->$attr = (string)$el->{$attr};
				}
			}
			else
			{
				$entity->$attr = (string)$el[$attr];
			}
		}
	}

	protected function pluckXmlAttribute(\SimpleXMLElement $els, $key)
	{
		$values = [];
		$i = 0;
		foreach ($els AS $el)
		{
			$values[$i++] = (string)$el[$key];
		}

		return $values;
	}

	protected function getSimpleAttributes(\SimpleXMLElement $el)
	{
		$output = [];
		foreach ($el->attributes() AS $key => $val)
		{
			$output[$key] = (string)$val;
		}
		return $output;
	}

	protected function exportCdata(\DOMElement $parent, $value)
	{
		$cdata = \XF\Util\Xml::createDomCdataSection($parent->ownerDocument, $value);
		$parent->appendChild($cdata);
	}

	protected function getCdataValue(\SimpleXMLElement $el)
	{
		return \XF\Util\Xml::processSimpleXmlCdata($el);
	}

	protected function finder($shortName = null)
	{
		if (!$shortName)
		{
			$shortName = $this->getShortName();
		}
		return $this->em->getFinder($shortName);
	}

	/**
	 * @param array $ids
	 *
	 * @return \XF\Mvc\Entity\ArrayCollection
	 */
	protected function findByIds(array $ids)
	{
		return $this->em->findByIds($this->getShortName(), $ids);
	}

	/**
	 * @return \XF\Mvc\Entity\Finder
	 */
	protected function findAllForType($addOnId = null, $addOnIdKey = 'addon_id')
	{
		$finder = $this->finder($this->getShortName());
		if ($addOnId)
		{
			$finder->where($addOnIdKey, $addOnId);
		}
		return $finder;
	}

	protected function create($shortName = null)
	{
		if (!$shortName)
		{
			$shortName = $this->getShortName();
		}
		return $this->em->create($shortName);
	}

	protected function db()
	{
		return $this->em->getDb();
	}
}