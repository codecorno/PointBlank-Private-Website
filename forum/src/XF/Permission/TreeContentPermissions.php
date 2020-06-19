<?php

namespace XF\Permission;

use XF\Mvc\Entity\Entity;

abstract class TreeContentPermissions extends AbstractContentPermissions
{
	/**
	 * @var \XF\Tree
	 */
	protected $tree;

	abstract public function getContentTree();

	abstract public function getContentTitle(Entity $entity);

	abstract protected function getFinalPerms($contentId, array $calculated, array &$childPerms);
	abstract protected function getFinalAnalysisPerms($contentId, array $calculated, array &$childPerms);

	protected function setupBuildTypeData()
	{
		$this->tree = $this->getContentTree();
	}

	public function getAnalysisContentPairs()
	{
		$pairs = [];
		foreach ($this->tree->getFlattened() AS $id => $value)
		{
			$prefix = $value['depth'] ? str_repeat('--', $value['depth']) . ' ' : '';
			$pairs[$id] = $prefix . $this->getContentTitle($value['record']);
		}

		return $pairs;
	}

	public function rebuildCombination(\XF\Entity\PermissionCombination $combination, array $basePerms)
	{
		$built = $this->buildForChildrenOf(null, $combination->user_group_list, $combination->user_id, $basePerms);
		$this->writeBuiltCombination($combination, $built);
	}

	public function analyzeCombination(
		\XF\Entity\PermissionCombination $combination, $contentId, array $basePerms, array $baseIntermediates
	)
	{
		$parents = $this->tree->getPathTo($contentId);
		if ($parents === null)
		{
			// content ID isn't in the tree
			return [];
		}

		$groupIds = $combination->user_group_list;
		$userId = $combination->user_id;

		$intermediates = $baseIntermediates;
		$permissions = $basePerms;
		$finalPerms = [];
		$dependChanges = [];

		$treePath = array_keys($parents);
		$treePath[] = $contentId;

		$titles = $this->getAnalysisContentPairs();

		foreach ($treePath AS $treeId)
		{
			$permissions = $this->adjustBasePermissionAllows($permissions);

			$sets = $this->getApplicablePermissionSets($treeId, $groupIds, $userId);
			$permissions = $this->builder->calculatePermissions($sets, $this->permissionsGrouped, $permissions);

			$calculated = $this->builder->applyPermissionDependencies(
				$permissions, $this->permissionsGrouped, $dependChanges
			);
			$finalPerms = $this->getFinalAnalysisPerms($contentId, $calculated, $permissions);

			$thisIntermediates = $this->builder->collectIntermediates(
				$combination, $permissions, $sets, $treeId, $titles[$treeId]
			);
			$intermediates = $this->builder->pushIntermediates($intermediates, $thisIntermediates);
		}

		return $this->builder->getFinalAnalysis($finalPerms, $intermediates, $dependChanges);
	}

	protected function buildForChildrenOf($contentId, array $userGroupIds, $userId = 0, array $basePerms)
	{
		$childIds = $this->tree->childIds($contentId);
		if (!$childIds)
		{
			return [];
		}

		$basePerms = $this->adjustBasePermissionAllows($basePerms);

		$output = [];
		foreach ($childIds AS $childId)
		{
			$output += $this->buildForContent($childId, $userGroupIds, $userId, $basePerms);
		}

		return $output;
	}

	protected function buildForContent($contentId, array $userGroupIds, $userId = 0, array $basePerms)
	{
		$sets = $this->getApplicablePermissionSets($contentId, $userGroupIds, $userId);
		$childPerms = $this->builder->calculatePermissions($sets, $this->permissionsGrouped, $basePerms);

		$calculated = $this->builder->applyPermissionDependencies($childPerms, $this->permissionsGrouped);
		$finalPerms = $this->getFinalPerms($contentId, $calculated, $childPerms);

		$output = [];
		$output[$contentId] = $finalPerms;
		$output += $this->buildForChildrenOf($contentId, $userGroupIds, $userId, $childPerms);

		return $output;
	}
}