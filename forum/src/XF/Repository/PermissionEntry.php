<?php

namespace XF\Repository;

use XF\Mvc\Entity\Finder;
use XF\Mvc\Entity\Repository;

class PermissionEntry extends Repository
{
	public function getGlobalUserGroupPermissionEntries($userGroupId)
	{
		if (!$userGroupId)
		{
			return [];
		}

		$entries = $this->getGlobalPermissionEntriesGrouped(['user_group_id' => $userGroupId]);

		return isset($entries['groups'][$userGroupId])
			? $entries['groups'][$userGroupId]
			: [];
	}

	public function getGlobalUserPermissionEntries($userId)
	{
		if (!$userId)
		{
			return [];
		}

		$entries = $this->getGlobalPermissionEntriesGrouped(['user_id' => $userId]);

		return isset($entries['users'][$userId])
			? $entries['users'][$userId]
			: [];
	}

	public function getGlobalPermissionEntriesGrouped(array $limits = [])
	{
		$userEntries = [];
		$groupEntries = [];
		$systemEntries = [];
		$db = $this->db();

		if (isset($limits['user_group_id']))
		{
			$conditions[] = "AND user_group_id = " . $db->quote($limits['user_group_id']);
		}
		if (isset($limits['user_id']))
		{
			$conditions[] = "AND user_id = " . $db->quote($limits['user_id']);
		}

		$permissions = $db->query("
			SELECT *
			FROM xf_permission_entry
		");
		while ($permission = $permissions->fetch())
		{
			$value = $permission['permission_value'] == 'use_int'
				? $permission['permission_value_int'] : $permission['permission_value'];
			$groupId = $permission['permission_group_id'];
			$permissionId = $permission['permission_id'];

			if ($permission['user_id'])
			{
				$userEntries[$permission['user_id']][$groupId][$permissionId] = $value;
			}
			else if ($permission['user_group_id'])
			{
				$groupEntries[$permission['user_group_id']][$groupId][$permissionId] = $value;
			}
			else
			{
				$systemEntries[$groupId][$permissionId] = $value;
			}
		}

		return [
			'users' => $userEntries,
			'groups' => $groupEntries,
			'system' => $systemEntries
		];
	}

	public function getContentPermissionEntriesGrouped($contentType, $contentId = null, array $limits = [])
	{
		$userEntries = [];
		$groupEntries = [];
		$systemEntries = [];

		$db = $this->db();

		$conditions = [];

		if ($contentId !== null)
		{
			$conditions[] = "AND content_id = " . $db->quote($contentId);
		}
		if (isset($limits['user_group_id']))
		{
			$conditions[] = "AND user_group_id = " . $db->quote($limits['user_group_id']);
		}
		if (isset($limits['user_id']))
		{
			$conditions[] = "AND user_id = " . $db->quote($limits['user_id']);
		}

		$permissions = $db->query("
			SELECT *
			FROM xf_permission_entry_content
			WHERE content_type = ?
				" . implode("\n", $conditions)
		, $contentType);
		while ($permission = $permissions->fetch())
		{
			$value = $permission['permission_value'] == 'use_int'
				? $permission['permission_value_int'] : $permission['permission_value'];
			$groupId = $permission['permission_group_id'];
			$permissionId = $permission['permission_id'];
			$thisContentId = $permission['content_id'];

			if ($permission['user_id'])
			{
				$userEntries[$thisContentId][$permission['user_id']][$groupId][$permissionId] = $value;
			}
			else if ($permission['user_group_id'])
			{
				$groupEntries[$thisContentId][$permission['user_group_id']][$groupId][$permissionId] = $value;
			}
			else
			{
				$systemEntries[$thisContentId][$groupId][$permissionId] = $value;
			}
		}

		if ($contentId !== null)
		{
			$userEntries = isset($userEntries[$contentId]) ? $userEntries[$contentId] : [];
			$groupEntries = isset($groupEntries[$contentId]) ? $groupEntries[$contentId] : [];
			$systemEntries = isset($systemEntries[$contentId]) ? $systemEntries[$contentId] : [];
		}

		return [
			'users' => $userEntries,
			'groups' => $groupEntries,
			'system' => $systemEntries
		];
	}

	public function getContentUserGroupPermissionEntries($contentType, $contentId, $userGroupId)
	{
		if (!$userGroupId)
		{
			return [];
		}

		$entries = $this->getContentPermissionEntriesGrouped(
			$contentType, $contentId, ['user_group_id' => $userGroupId]
		);

		return isset($entries['groups'][$userGroupId]) ? $entries['groups'][$userGroupId] : [];
	}

	public function getContentUserPermissionEntries($contentType, $contentId, $userId)
	{
		if (!$userId)
		{
			return [];
		}

		$entries = $this->getContentPermissionEntriesGrouped(
			$contentType, $contentId, ['user_id' => $userId]
		);

		return isset($entries['users'][$userId]) ? $entries['users'][$userId] : [];
	}

	public function getContentWithCustomPermissions($contentType)
	{
		$ids = $this->db()->fetchAllColumn("
			SELECT DISTINCT content_id
			FROM xf_permission_entry_content
			WHERE content_type = ?
		", $contentType);

		return array_fill_keys($ids, true);
	}

	public function deleteOrphanedGlobalUserPermissionEntries()
	{
		$this->deleteOrphanedPermissionEntries('xf_permission_entry');
	}

	public function deleteOrphanedContentUserPermissionEntries()
	{
		$this->deleteOrphanedPermissionEntries('xf_permission_entry_content');
	}

	protected function deleteOrphanedPermissionEntries($table)
	{
		$db = $this->db();

		$groups = $db->fetchAllColumn('
			SELECT DISTINCT pe.permission_group_id
			FROM `' . $table . '` AS pe
			LEFT JOIN xf_permission AS p ON
				(pe.permission_group_id = p.permission_group_id)
			WHERE p.permission_group_id IS NULL
		');

		if ($groups)
		{
			$db->delete($table, 'permission_group_id IN(' . $db->quote($groups) . ')');
		}

		$permPairs = $db->fetchAll('
			SELECT DISTINCT pe.permission_id, pe.permission_group_id
			FROM `' . $table . '` AS pe
			LEFT JOIN xf_permission AS p ON
				(pe.permission_id = p.permission_id AND pe.permission_group_id = p.permission_group_id)
			WHERE p.permission_id IS NULL
			AND p.permission_group_id IS NULL
		');

		if ($permPairs)
		{
			foreach ($permPairs AS $perms)
			{
				$db->delete($table, 'permission_id = ? AND permission_group_id = ?', $perms);
			}
		}
	}
}