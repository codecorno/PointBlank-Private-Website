<?php

namespace XF\Repository;

use XF\Mvc\Entity\Finder;
use XF\Mvc\Entity\Repository;

abstract class AbstractPromptMap extends Repository
{
	abstract protected function getAssociations(\XF\Entity\AbstractPrompt $prompt);

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

	public function updatePromptAssociations(\XF\Entity\AbstractPrompt $item, array $contentIds)
	{
		$emptyKey = array_search(0, $contentIds);
		if ($emptyKey !== false)
		{
			unset($contentIds[$emptyKey]);
		}
		$contentIds = array_unique($contentIds);

		$structureData = $this->getStructureData();

		$existingAssociations = $this->getAssociations($item);
		if (!count($existingAssociations) && !$contentIds)
		{
			return;
		}

		$db = $this->db();
		$db->beginTransaction();

		$db->delete($structureData['table'], 'prompt_id = ?', $item->prompt_id);

		$map = [];

		foreach ($contentIds AS $id)
		{
			$map[] = [
				$structureData['key'] => $id,
				'prompt_id' => $item->prompt_id
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

	public function removePromptAssociations(\XF\Entity\AbstractPrompt $item)
	{
		$structureData = $this->getStructureData();

		$rebuildIds = $this->db()->fetchAllColumn("
			SELECT $structureData[key]
			FROM $structureData[table]
			WHERE prompt_id = ?
		", $item->prompt_id);

		if (!$rebuildIds)
		{
			return;
		}

		$db = $this->db();
		$db->beginTransaction();

		$db->delete($structureData['table'], 'prompt_id = ?', $item->prompt_id);

		$this->rebuildContentAssociationCache($rebuildIds);

		$db->commit();
	}

	public function updateContentAssociations($contentId, array $itemIds)
	{
		$structureData = $this->getStructureData();

		$db = $this->db();
		$db->beginTransaction();

		$db->delete($structureData['table'], $structureData['key'] . ' = ?', $contentId);

		$map = [];

		foreach ($itemIds AS $itemId)
		{
			$map[] = [
				$structureData['key'] => $contentId,
				'prompt_id' => $itemId
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

		$associations = $this->finder($structureData['mapEntity'])
			->with('Prompt')
			->where($structureData['key'], $contentIds)
			->order('Prompt.materialized_order');
		foreach ($associations->fetch() AS $map)
		{
			$key = $map->get($structureData['key']);
			$newCache[$key][$map->prompt_id] = $map->prompt_id;
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

	public function getItemIdsInContext($contentIds)
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
			SELECT prompt_id
			FROM $structureData[table]
			WHERE $structureData[key] IN (" . $db->quote($contentIds) . ")
		");
	}
}