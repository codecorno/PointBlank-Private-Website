<?php

namespace XF\Service;

class UpdatePermissions extends AbstractService
{
	/**
	 * @var \XF\Entity\User|null
	 */
	protected $user = null;

	/**
	 * @var \XF\Entity\UserGroup|null
	 */
	protected $userGroup = null;

	protected $contentType = '';
	protected $contentId = 0;

	public function setUser(\XF\Entity\User $user)
	{
		if (!$user->exists())
		{
			throw new \InvalidArgumentException("User must exist");
		}

		$this->user = $user;
		$this->userGroup = null;

		return $this;
	}

	public function getUser()
	{
		return $this->user;
	}

	public function setUserGroup(\XF\Entity\UserGroup $userGroup)
	{
		$this->userGroup = $userGroup;
		$this->user = null;

		return $this;
	}

	public function getUserGroup()
	{
		return $this->userGroup;
	}

	public function setContent($contentType, $contentId)
	{
		$contentId = intval($contentId);
		if (!$contentType || !$contentId)
		{
			throw new \InvalidArgumentException("A content type and ID must be provided");
		}

		$this->contentType = $contentType;
		$this->contentId = $contentId;

		return $this;
	}

	public function getContent()
	{
		return [$this->contentType, $this->contentId];
	}

	public function setGlobal()
	{
		$this->contentType = '';
		$this->contentId = 0;

		return $this;
	}

	public function updatePermissions(array $values)
	{
		$existingGrouped = $this->getExistingPermissionEntriesGrouped();
		$permissionsGrouped = $this->getAvailablePermissionsGrouped();

		$db = $this->db();
		$db->beginTransaction();

		foreach ($values AS $groupId => $groupValues)
		{
			if (!is_array($groupValues))
			{
				continue;
			}

			foreach ($groupValues AS $permissionId => $value)
			{
				if (!isset($permissionsGrouped[$groupId][$permissionId]))
				{
					continue;
				}

				$entry = isset($existingGrouped[$groupId][$permissionId])
					? $existingGrouped[$groupId][$permissionId]
					: null;
				$permission = $permissionsGrouped[$groupId][$permissionId];

				$this->writeEntry($permission, $value, $entry);
			}
		}

		$db->commit();

		if ($this->app->container()->isCached('permission.builder'))
		{
			// permissions changing and we've already cached the data, so refresh
			$this->app->permissionBuilder()->refreshData();
		}

		$this->triggerCacheRebuild();
	}

	protected function getExistingPermissionEntriesGrouped()
	{
		if ($this->contentType)
		{
			$finder = $this->finder('XF:PermissionEntryContent');
			$finder->where([
				'content_type' => $this->contentType,
				'content_id' => $this->contentId
			]);
		}
		else
		{
			$finder = $this->finder('XF:PermissionEntry');
		}

		$finder->where([
			'user_id' => $this->user ? $this->user->user_id : 0,
			'user_group_id' => $this->userGroup ? $this->userGroup->user_group_id : 0
		]);

		return $finder->fetch()->groupBy('permission_group_id', 'permission_id');
	}

	protected function getAvailablePermissionsGrouped()
	{
		/** @var \XF\Repository\Permission $permissionRepo */
		$permissionRepo = $this->repository('XF:Permission');
		return $permissionRepo->getPermissionsGrouped();
	}

	protected function writeEntry(\XF\Entity\Permission $permission, $value, \XF\Mvc\Entity\Entity $entry = null)
	{
		if ($value == 'unset' || $value === '0' || $value === 0)
		{
			if ($entry)
			{
				$entry->delete();
			}
			return null;
		}

		if (!$entry)
		{
			if ($this->contentType)
			{
				$entry = $this->em()->create('XF:PermissionEntryContent');
				$entry->content_type = $this->contentType;
				$entry->content_id = $this->contentId;
			}
			else
			{
				$entry = $this->em()->create('XF:PermissionEntry');
			}

			$entry->permission_group_id = $permission->permission_group_id;
			$entry->permission_id = $permission->permission_id;
		}

		$entry->user_id = $this->user ? $this->user->user_id : 0;
		$entry->user_group_id = $this->userGroup ? $this->userGroup->user_group_id : 0;

		if ($permission->permission_type == 'integer')
		{
			$entry->permission_value = 'use_int';
			$entry->permission_value_int = intval($value);
		}
		else
		{
			$entry->permission_value = $value;
			$entry->permission_value_int = 0;
		}

		$entry->save();

		return $entry;
	}

	public function triggerCacheRebuild()
	{
		/** @var \XF\Repository\PermissionCombination $combinationRepo */
		$combinationRepo = $this->repository('XF:PermissionCombination');

		if ($this->user)
		{
			$combination = $combinationRepo->updatePermissionCombinationForUser($this->user, false);
			$this->app->permissionBuilder()->rebuildCombination($combination);
		}
		else if ($this->userGroup)
		{
			$combinations = $combinationRepo->getPermissionCombinationsForUserGroup($this->userGroup->user_group_id);
			if (count($combinations) > 10)
			{
				$combinationIds = $combinations->keys();

				// too much to build inline
				$this->app->jobManager()->enqueueUnique(
					'permissionRebuild:' . substr(md5(implode(',', $combinationIds)), 0, 16),
					'XF:PermissionRebuildPartial',
					['combinationIds' => $combinationIds]
				);
			}
			else
			{
				$builder = $this->app->permissionBuilder();
				foreach ($combinations AS $combination)
				{
					$builder->rebuildCombination($combination);
				}
			}
		}
		else
		{
			// need to rebuild all combinations
			$this->app->jobManager()->enqueueUnique('permissionRebuild', 'XF:PermissionRebuild');
		}
	}
}