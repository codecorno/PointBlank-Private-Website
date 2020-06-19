<?php

namespace XF\Permission;

class Builder
{
	/**
	 * @var \XF\Db\AbstractAdapter
	 */
	protected $db;

	/**
	 * @var \XF\Mvc\Entity\Manager
	 */
	protected $em;

	protected $permissionsGrouped;

	protected $contentHandlersMap;

	protected $userEntries;
	protected $groupEntries;
	protected $systemEntries;

	/** @var AbstractContentPermissions[] */
	protected $contentHandlers = [];

	protected $permissionPriority = [
		'deny' => 1,
		'content_allow' => 2,
		'reset' => 3,
		'allow' => 4,
		'unset' => 5,
		'use_int' => 6
	];

	public function __construct(\XF\Db\AbstractAdapter $db, \XF\Mvc\Entity\Manager $em, array $contentHandlersMap = [])
	{
		$this->db = $db;
		$this->em = $em;
		$this->contentHandlersMap = $contentHandlersMap;

		$this->setupData();
	}

	public function refreshData()
	{
		$this->setupData();

		foreach ($this->contentHandlers AS $contentBuilder)
		{
			$contentBuilder->setupBuildData();
		}
	}

	protected function setupData()
	{
		/** @var \XF\Repository\Permission $permissionRepo */
		$permissionRepo = $this->em->getRepository('XF:Permission');
		$this->permissionsGrouped = $permissionRepo->getPermissionsGrouped();

		/** @var \XF\Repository\PermissionEntry $entryRepo */
		$entryRepo = $this->em->getRepository('XF:PermissionEntry');
		$entries = $entryRepo->getGlobalPermissionEntriesGrouped();
		$this->userEntries = $entries['users'];
		$this->groupEntries = $entries['groups'];
		$this->systemEntries = $entries['system'];
	}

	/**
	 * @return AbstractContentPermissions[]
	 */
	public function getContentHandlers()
	{
		foreach ($this->contentHandlersMap AS $contentType => $handler)
		{
			if (isset($this->contentHandlers[$contentType]))
			{
				continue;
			}

			// will be cached
			$this->getContentHandler($contentType);
		}

		return $this->contentHandlers;
	}

	public function getContentHandler($contentType, $throw = false)
	{
		if (!isset($this->contentHandlers[$contentType]))
		{
			if (!isset($this->contentHandlersMap[$contentType]))
			{
				if ($throw)
				{
					throw new \InvalidArgumentException("No permission handler for '$contentType'");
				}
				return null;
			}

			$handler = $this->contentHandlersMap[$contentType];

			if (!class_exists($handler))
			{
				if ($throw)
				{
					throw new \InvalidArgumentException("Invalid permission handler for '$contentType': $handler");
				}
				return null;
			}

			$handler = \XF::extendClass($handler);
			$this->contentHandlers[$contentType] = new $handler($this);
		}

		return $this->contentHandlers[$contentType];
	}

	public function isValidPermissionContentType($contentType)
	{
		return isset($this->contentHandlersMap[$contentType]);
	}

	public function getPermissionsGrouped()
	{
		return $this->permissionsGrouped;
	}

	public function rebuildCombination(\XF\Entity\PermissionCombination $combination)
	{
		$sets = $this->getApplicablePermissionSets($combination->user_group_list, $combination->user_id);
		$calculated = $this->calculatePermissions($sets, $this->permissionsGrouped);
		$calculated = $this->applyPermissionDependencies($calculated, $this->permissionsGrouped);

		$final = $this->finalizePermissionValues($calculated);

		$this->db->beginTransaction();
		$combination->fastUpdate('cache_value', $final);
		$this->rebuildCombinationContent($combination, $calculated);
		$this->db->commit();
	}

	public function rebuildCombinationContent(\XF\Entity\PermissionCombination $combination, array $basePerms)
	{
		foreach ($this->getContentHandlers() AS $contentBuilder)
		{
			$contentBuilder->rebuildCombination($combination, $basePerms);
		}
	}

	public function analyzeCombination(\XF\Entity\PermissionCombination $combination)
	{
		$groupIds = $combination->user_group_list;
		$userId = $combination->user_id;

		$sets = $this->getApplicablePermissionSets($groupIds, $userId);
		$calculated = $this->calculatePermissions($sets, $this->permissionsGrouped);
		$calculated = $this->applyPermissionDependencies($calculated, $this->permissionsGrouped, $dependChanges);
		$final = $this->finalizePermissionValues($calculated);

		$intermediates = $this->collectIntermediates($combination, $final, $sets);
		return $this->getFinalAnalysis($final, $intermediates, $dependChanges);
	}

	public function analyzeCombinationContent(\XF\Entity\PermissionCombination $combination, $contentType, $contentId)
	{
		$contentHandlers = $this->getContentHandlers();
		if (!isset($contentHandlers[$contentType]))
		{
			throw new \InvalidArgumentException("Unknown content builder '$contentType'");
		}

		$sets = $this->getApplicablePermissionSets($combination->user_group_list, $combination->user_id);
		$calculated = $this->calculatePermissions($sets, $this->permissionsGrouped);
		$calculated = $this->applyPermissionDependencies($calculated, $this->permissionsGrouped);

		$intermediates = $this->collectIntermediates($combination, $calculated, $sets);

		$handler = $contentHandlers[$contentType];
		return $handler->analyzeCombination($combination, $contentId, $calculated, $intermediates);
	}

	public function collectIntermediates(
		\XF\Entity\PermissionCombination $combination, array $groupedPermissions, array $sets,
		$contentId = null, $contentTitle = null
	)
	{
		$groupIds = $combination->user_group_list;
		$userId = $combination->user_id;

		$intermediates = [];

		foreach ($groupedPermissions AS $permissionGroupId => $permissions)
		{
			foreach ($permissions AS $permissionId => $null)
			{
				$localIntermediates = [];

				if (isset($sets['system'][$permissionGroupId][$permissionId]))
				{
					$localIntermediates[] = new AnalysisIntermediate(
						$sets['system'][$permissionGroupId][$permissionId], 'system', null,
						$contentId, $contentTitle
					);
				}

				foreach ($groupIds AS $groupId)
				{
					if (isset($sets["group-$groupId"][$permissionGroupId][$permissionId]))
					{
						$intermediateValue = $sets["group-$groupId"][$permissionGroupId][$permissionId];
					}
					else
					{
						$permission = $this->permissionsGrouped[$permissionGroupId][$permissionId];
						$intermediateValue = $permission->permission_type == 'integer' ? 0 : 'unset';
					}

					$skipDefault = ($contentId && ($intermediateValue === 'unset' || $intermediateValue === 0));
					if (!$skipDefault)
					{
						$localIntermediates[] = new AnalysisIntermediate(
							$intermediateValue, 'group', $groupId,
							$contentId, $contentTitle
						);
					}
				}

				if ($userId && isset($sets["user-$userId"][$permissionGroupId][$permissionId]))
				{
					$localIntermediates[] = new AnalysisIntermediate(
						$sets["user-$userId"][$permissionGroupId][$permissionId], 'user', $userId,
						$contentId, $contentTitle
					);
				}

				$intermediates[$permissionGroupId][$permissionId] = $localIntermediates;
			}
		}

		return $intermediates;
	}

	public function pushIntermediates(array $existing, array $new)
	{
		foreach ($new AS $groupId => $groups)
		{
			foreach ($groups AS $permissionId => $intermediates)
			{
				if (!isset($existing[$groupId][$permissionId]))
				{
					$existing[$groupId][$permissionId] = [];
				}

				$existing[$groupId][$permissionId] = array_merge($existing[$groupId][$permissionId], $intermediates);
			}
		}

		return $existing;
	}

	public function getFinalAnalysis(array $finalPerms, array $intermediates, array $dependChanges)
	{
		$analysis = [];

		foreach ($finalPerms AS $permissionGroupId => $permissions)
		{
			foreach ($permissions AS $permissionId => $finalValue)
			{
				if (isset($dependChanges[$permissionGroupId][$permissionId]))
				{
					$dependChange = $dependChanges[$permissionGroupId][$permissionId];
				}
				else
				{
					$dependChange = null;
				}

				if (isset($intermediates[$permissionGroupId][$permissionId]))
				{
					$intermediate = $intermediates[$permissionGroupId][$permissionId];
				}
				else
				{
					$intermediate = [];
				}

				$analysis[$permissionGroupId][$permissionId] = [
					'final' => $finalValue,
					'dependChange' => $dependChange,
					'intermediates' => $intermediate
				];
			}
		}

		return $analysis;
	}

	public function getApplicablePermissionSets(array $userGroupIds, $userId = 0)
	{
		$sets = [];
		foreach ($userGroupIds AS $userGroupId)
		{
			if (isset($this->groupEntries[$userGroupId]))
			{
				$sets["group-$userGroupId"] = $this->groupEntries[$userGroupId];
			}
		}
		if ($userId && isset($this->userEntries[$userId]))
		{
			$sets["user-$userId"] = $this->userEntries[$userId];
		}
		if ($this->systemEntries)
		{
			$sets['system'] = $this->systemEntries;
		}

		return $sets;
	}

	public function calculatePermissions(array $sets, array $availablePermissions, array $baseValues = [])
	{
		$output = [];

		foreach ($availablePermissions AS $groupId => $permissions)
		{
			foreach ($permissions AS $permissionId => $permission)
			{
				$permissionType = $permission->permission_type;

				if (isset($baseValues[$groupId][$permissionId]))
				{
					$baseValue = $baseValues[$groupId][$permissionId];
				}
				else
				{
					$baseValue = $permissionType == 'flag' ? 'unset' : 0;
				}

				$values = ['base' => $baseValue];
				foreach ($sets AS $setId => $set)
				{
					if (isset($set[$groupId][$permissionId]))
					{
						$values[$setId] = $set[$groupId][$permissionId];
					}
				}

				$output[$groupId][$permissionId] = $this->pickPermissionPriorityValue($values, $permissionType);
			}
		}

		return $output;
	}

	public function pickPermissionPriorityValue(array $values, $permissionType)
	{
		if ($permissionType == 'integer')
		{
			$highest = 0;
			foreach ($values AS $value)
			{
				if ($value == -1)
				{
					return -1;
				}
				else if ($value > $highest)
				{
					$highest = $value;
				}
			}

			return $highest;
		}
		else
		{
			$priority = 5;
			$priorityName = 'unset';
			foreach ($values AS $value)
			{
				if (!isset($this->permissionPriority[$value]))
				{
					// should only happen if we have some invalid data
					continue;
				}
				$thisPriority = $this->permissionPriority[$value];
				if ($thisPriority < $priority)
				{
					$priority = $thisPriority;
					$priorityName = $value;
				}
			}

			return $priorityName;
		}
	}

	public function applyPermissionDependencies(array $calculated, array $availablePermissions, &$changed = [])
	{
		$changed = [];

		foreach ($availablePermissions AS $groupId => $permissions)
		{
			foreach ($permissions AS $permissionId => $permission)
			{
				$dependId = $permission->depend_permission_id;

				if (!$dependId || !isset($calculated[$groupId][$dependId]))
				{
					continue;
				}

				$parentValue = $calculated[$groupId][$dependId];
				if ($parentValue == 'deny' || $parentValue == 'reset')
				{
					$changed[$groupId][$permissionId] = [
						'original' => $calculated[$groupId][$permissionId],
						'by' => $dependId
					];
					$calculated[$groupId][$permissionId] = $permission->permission_type == 'integer' ? 0 : 'deny';
				}
			}
		}

		return $calculated;
	}

	public function finalizePermissionValues(array $calculated)
	{
		$finalized = [];
		foreach ($calculated AS $key => $value)
		{
			if (is_array($value))
			{
				$finalized[$key] = $this->finalizePermissionValues($value);
			}
			else
			{
				if (is_int($value))
				{
					$finalized[$key] = intval($value);
				}
				else
				{
					$finalized[$key] = ($value == 'allow' || $value == 'content_allow');
				}

			 }
		}

		return $finalized;
	}

	public function getAnalysisTypeData()
	{
		$data = [];

		foreach ($this->getContentHandlers() AS $contentType => $contentBuilder)
		{
			$data[$contentType] = [
				'title' => $contentBuilder->getAnalysisTypeTitle(),
				'content' => $contentBuilder->getAnalysisContentPairs()
			];
		}

		return $data;
	}

	public function db()
	{
		return $this->db;
	}

	public function em()
	{
		return $this->em;
	}
}