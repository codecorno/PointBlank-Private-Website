<?php

namespace XF\Permission;

use XF\Mvc\Entity\Entity;

abstract class FlatContentPermissions extends AbstractContentPermissions
{
	/**
	 * @var \XF\Mvc\Entity\ArrayCollection
	 */
	protected $entries;

	abstract public function getContentList();

	abstract protected function getFinalPerms($contentId, array $calculated, array &$childPerms);
	abstract protected function getFinalAnalysisPerms($contentId, array $calculated, array &$childPerms);

	protected function setupBuildTypeData()
	{
		$this->entries = $this->getContentList();
	}

	public function rebuildCombination(\XF\Entity\PermissionCombination $combination, array $basePerms)
	{
		$entryIds = $this->entries->keys();
		if (!$entryIds)
		{
			return;
		}

		$basePerms = $this->adjustBasePermissionAllows($basePerms);

		$built = [];
		foreach ($entryIds AS $entryId)
		{
			$built += $this->buildForContent($entryId, $combination->user_group_list, $combination->user_id, $basePerms);
		}
		$this->writeBuiltCombination($combination, $built);
	}

	/**
	 * @param $contentId
	 * @param array $userGroupIds
	 * @param int $userId
	 * @param array $basePerms
	 *
	 * @return array
	 */
	protected function buildForContent($contentId, array $userGroupIds, $userId = 0, array $basePerms)
	{
		$sets = $this->getApplicablePermissionSets($contentId, $userGroupIds, $userId);
		$childPerms = $this->builder->calculatePermissions($sets, $this->permissionsGrouped, $basePerms);

		$calculated = $this->builder->applyPermissionDependencies($childPerms, $this->permissionsGrouped);
		$finalPerms = $this->getFinalPerms($contentId, $calculated, $childPerms);

		$output = [];
		$output[$contentId] = $finalPerms;

		return $output;
	}

	/**
	 * @param \XF\Entity\PermissionCombination $combination
	 * @param $contentId
	 * @param array $basePerms
	 * @param array $baseIntermediates
	 *
	 * @return array
	 */
	public function analyzeCombination(
		\XF\Entity\PermissionCombination $combination, $contentId, array $basePerms, array $baseIntermediates
	)
	{
		$groupIds = $combination->user_group_list;
		$userId = $combination->user_id;

		$intermediates = $baseIntermediates;
		$permissions = $basePerms;
		$dependChanges = [];

		$titles = $this->getAnalysisContentPairs();

		$permissions = $this->adjustBasePermissionAllows($permissions);

		$sets = $this->getApplicablePermissionSets($contentId, $groupIds, $userId);
		$permissions = $this->builder->calculatePermissions($sets, $this->permissionsGrouped, $permissions);

		$calculated = $this->builder->applyPermissionDependencies(
			$permissions, $this->permissionsGrouped, $dependChanges
		);
		$finalPerms = $this->getFinalAnalysisPerms($contentId, $calculated, $permissions);

		$thisIntermediates = $this->builder->collectIntermediates(
			$combination, $permissions, $sets, $contentId, $titles[$contentId]
		);
		$intermediates = $this->builder->pushIntermediates($intermediates, $thisIntermediates);

		return $this->builder->getFinalAnalysis($finalPerms, $intermediates, $dependChanges);
	}

	/**
	 * @return array
	 */
	public function getAnalysisContentPairs()
	{
		$pairs = [];
		foreach ($this->entries AS $id => $value)
		{
			$pairs[$id] = $this->getContentTitle($value);
		}

		return $pairs;
	}

	/**
	 * @param Entity $entity
	 *
	 * @return mixed|null
	 */
	public function getContentTitle(Entity $entity)
	{
		return $entity->title;
	}
}