<?php

namespace XF\Repository;

use XF\Mvc\Entity\Finder;
use XF\Mvc\Entity\Repository;

abstract class AbstractFieldMap extends Repository
{
	abstract protected function getMapEntityIdentifier();

	abstract protected function getAssociationsForField(\XF\Entity\AbstractField $field);

	abstract protected function updateAssociationCache(array $cache);

	protected function getStructureData()
	{
		$mapEntity = $this->getMapEntityIdentifier();
		$structure = $this->em->getEntityStructure($mapEntity);
		$className = $this->em->getEntityClassName($mapEntity);

		return [
			'mapEntity' => $mapEntity,
			'table' => $structure->table,
			'key' => $className::getContainerKey()
		];
	}

	public function updateFieldAssociations(\XF\Entity\AbstractField $field, array $contentIds)
	{
		$emptyKey = array_search(0, $contentIds, true);
		if ($emptyKey !== false)
		{
			unset($contentIds[$emptyKey]);
		}
		$contentIds = array_unique($contentIds);

		$structureData = $this->getStructureData();

		$existingAssociations = $this->getAssociationsForField($field);
		if (!count($existingAssociations) && !$contentIds)
		{
			return;
		}

		$db = $this->db();
		$db->beginTransaction();

		$db->delete($structureData['table'], 'field_id = ?', $field->field_id);

		$map = [];

		foreach ($contentIds AS $id)
		{
			$map[] = [
				$structureData['key'] => $id,
				'field_id' => $field->field_id
			];
		}
		if ($map)
		{
			$db->insertBulk($structureData['table'], $map);
		}

		$rebuildIds = $contentIds;

		foreach ($existingAssociations AS $association)
		{
			/** @var \XF\Mvc\Entity\Entity $association */
			$rebuildIds[] = $association->getValue($structureData['key']);
		}

		$rebuildIds = array_unique($rebuildIds);
		$this->rebuildContentAssociationCache($rebuildIds);

		$db->commit();
	}

	public function removeFieldAssociations(\XF\Entity\AbstractField $field)
	{
		$structureData = $this->getStructureData();

		$rebuildIds = $this->db()->fetchAllColumn("
			SELECT $structureData[key]
			FROM $structureData[table]
			WHERE field_id = ?
		", $field->field_id);

		if (!$rebuildIds)
		{
			return;
		}

		$db = $this->db();
		$db->beginTransaction();

		$db->delete($structureData['table'], 'field_id = ?', $field->field_id);

		$this->rebuildContentAssociationCache($rebuildIds);

		$db->commit();
	}

	public function updateContentAssociations($contentId, array $fieldIds)
	{
		$structureData = $this->getStructureData();

		$db = $this->db();
		$db->beginTransaction();

		$db->delete($structureData['table'], $structureData['key'] . ' = ?', $contentId);

		$map = [];

		foreach ($fieldIds AS $fieldId)
		{
			$map[] = [
				$structureData['key'] => $contentId,
				'field_id' => $fieldId
			];
		}

		if ($map)
		{
			$db->insertBulk($structureData['table'], $map);
		}

		$this->rebuildContentAssociationCache($contentId);

		$db->commit();
	}

	public function rebuildContentAssociationCache($contentIds)
	{
		if (!is_array($contentIds))
		{
			$contentIds = [$contentIds];
		}
		if (!$contentIds)
		{
			return;
		}

		$structureData = $this->getStructureData();

		$newCache = [];

		$fieldAssociations = $this->finder($structureData['mapEntity'])
			->with('Field')
			->where($structureData['key'], $contentIds)
			->order('Field.display_order');
		foreach ($fieldAssociations->fetch() AS $fieldMap)
		{
			$key = $fieldMap->get($structureData['key']);
			$newCache[$key][$fieldMap->field_id] = $fieldMap->field_id;
		}

		foreach ($contentIds AS $contentId)
		{
			if (!isset($newCache[$contentId]))
			{
				$newCache[$contentId] = [];
			}
		}

		$this->updateAssociationCache($newCache);
	}
}