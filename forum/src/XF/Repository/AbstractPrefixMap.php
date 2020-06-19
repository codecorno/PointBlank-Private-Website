<?php

namespace XF\Repository;

use XF\Mvc\Entity\Finder;
use XF\Mvc\Entity\Repository;

abstract class AbstractPrefixMap extends Repository
{
	abstract protected function getMapEntityIdentifier();

	abstract protected function getAssociationsForPrefix(\XF\Entity\AbstractPrefix $prefix);

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

	public function updatePrefixAssociations(\XF\Entity\AbstractPrefix $prefix, array $contentIds)
	{
		$emptyKey = array_search(0, $contentIds);
		if ($emptyKey !== false)
		{
			unset($contentIds[$emptyKey]);
		}
		$contentIds = array_unique($contentIds);

		$structureData = $this->getStructureData();

		$existingAssociations = $this->getAssociationsForPrefix($prefix);
		if (!count($existingAssociations) && !$contentIds)
		{
			return;
		}

		$db = $this->db();
		$db->beginTransaction();

		$db->delete($structureData['table'], 'prefix_id = ?', $prefix->prefix_id);

		$map = [];

		foreach ($contentIds AS $id)
		{
			$map[] = [
				$structureData['key'] => $id,
				'prefix_id' => $prefix->prefix_id
			];
		}
		if ($map)
		{
			$db->insertBulk($structureData['table'], $map);
		}

		$rebuildIds = $contentIds;

		foreach ($existingAssociations AS $association)
		{
			$rebuildIds[] = $association->getValue($structureData['key']);
		}

		$rebuildIds = array_unique($rebuildIds);
		$this->rebuildContentAssociationCache($rebuildIds);

		$db->commit();
	}

	public function removePrefixAssociations(\XF\Entity\AbstractPrefix $prefix)
	{
		$structureData = $this->getStructureData();

		$rebuildIds = $this->db()->fetchAllColumn("
			SELECT $structureData[key]
			FROM $structureData[table]
			WHERE prefix_id = ?
		", $prefix->prefix_id);

		if (!$rebuildIds)
		{
			return;
		}

		$db = $this->db();
		$db->beginTransaction();

		$db->delete($structureData['table'], 'prefix_id = ?', $prefix->prefix_id);

		$this->rebuildContentAssociationCache($rebuildIds);

		$db->commit();
	}

	public function updateContentAssociations($contentId, array $prefixIds)
	{
		$structureData = $this->getStructureData();

		$db = $this->db();
		$db->beginTransaction();

		$db->delete($structureData['table'], $structureData['key'] . ' = ?', $contentId);

		$map = [];

		foreach ($prefixIds AS $prefixId)
		{
			$map[] = [
				$structureData['key'] => $contentId,
				'prefix_id' => $prefixId
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

		$prefixAssociations = $this->finder($structureData['mapEntity'])
			->with('Prefix')
			->where($structureData['key'], $contentIds)
			->order('Prefix.materialized_order');
		foreach ($prefixAssociations->fetch() AS $prefixMap)
		{
			$key = $prefixMap->get($structureData['key']);
			$newCache[$key][$prefixMap->prefix_id] = $prefixMap->prefix_id;
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

	public function getPrefixIdsInContent($contentIds)
	{
		if (!is_array($contentIds))
		{
			$contentIds = [$contentIds];
		}
		if (!$contentIds)
		{
			return [];
		}

		$db = $this->db();
		$structureData = $this->getStructureData();

		return $db->fetchAllColumn("
			SELECT prefix_id
			FROM $structureData[table]
			WHERE $structureData[key] IN (" . $db->quote($contentIds) . ")
		");
	}
}