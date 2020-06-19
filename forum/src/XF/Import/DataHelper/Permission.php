<?php

namespace XF\Import\DataHelper;

class Permission extends AbstractHelper
{
	// TODO: I think this may be getting instantiated multiple times in one import step. Check that.
	protected $knownPermissions;

	public function insertUserGroupPermissions($userGroupId, array $permissionsGrouped, $deleteExisting = false)
	{
		return $this->insertPermissionsInternal('group', $userGroupId, $permissionsGrouped, $deleteExisting);
	}

	public function insertUserPermissions($userId, array $permissionsGrouped, $deleteExisting = false)
	{
		return $this->insertPermissionsInternal('user', $userId, $permissionsGrouped, $deleteExisting);
	}

	public function insertContentUserGroupPermissions(
		$contentType, $contentId, $userGroupId, array $permissionsGrouped, $deleteExisting = false
	)
	{
		return $this->insertContentPermissionsInternal(
			$contentType, $contentId, 'group', $userGroupId, $permissionsGrouped, $deleteExisting
		);
	}

	public function insertContentUserPermissions(
		$contentType, $contentId, $userId, array $permissionsGrouped, $deleteExisting = false
	)
	{
		return $this->insertContentPermissionsInternal(
			$contentType, $contentId, 'user', $userId, $permissionsGrouped, $deleteExisting
		);
	}

	public function insertContentGlobalPermissions(
		$contentType, $contentId, array $permissionsGrouped, $deleteExisting = false
	)
	{
		return $this->insertContentPermissionsInternal(
			$contentType, $contentId, 'global', 0, $permissionsGrouped, $deleteExisting
		);
	}

	protected function getPermissionInsertData($type, $id, array $permissionsGrouped, array $insertExtra = [])
	{
		switch ($type)
		{
			case 'user':
			case 'group':
			case 'global';
				break;

			default:
				throw new \InvalidArgumentException("Invalid type '$type'");
		}

		if ($type == 'global')
		{
			$userId = 0;
			$userGroupId = 0;
		}
		else
		{
			$id = intval($id);
			if (!$id)
			{
				throw new \InvalidArgumentException("Must provide an ID");
			}

			$isForUserGroup = ($type == 'group');

			$userId = $isForUserGroup ? 0 : $id;
			$userGroupId = $isForUserGroup ? $id : 0;
		}

		$insert = [];
		if ($permissionsGrouped)
		{
			$this->loadKnownPermissions();

			foreach ($permissionsGrouped AS $group => $permissions)
			{
				foreach ($permissions AS $permission => $value)
				{
					if (!isset($this->knownPermissions[$group][$permission]))
					{
						continue;
					}

					switch ($value)
					{
						case 'unset':
						case 'allow':
						case 'content_allow':
						case 'reset':
						case 'deny':
							$valueStr = $value;
							$valueInt = 0;
							break;

						default:
							$valueStr = 'use_int';
							$valueInt = intval($value);
					}

					$insert[] = $insertExtra + [
						'user_group_id' => $userGroupId,
						'user_id' => $userId,
						'permission_group_id' => $group,
						'permission_id' => $permission,
						'permission_value' => $valueStr,
						'permission_value_int' => $valueInt
					];
				}
			}
		}

		return $insert;
	}

	protected function insertPermissionsInternal($type, $id, array $permissionsGrouped, $deleteExisting = false)
	{
		$insert = $this->getPermissionInsertData($type, $id, $permissionsGrouped);
		$db = $this->db();
		$changed = false;

		if ($deleteExisting)
		{
			if ($type == 'group')
			{
				$affected = $db->delete('xf_permission_entry', 'user_group_id = ? AND user_id = 0', $id);
			}
			else
			{
				$affected = $db->delete('xf_permission_entry', 'user_id = ? AND user_group_id = 0', $id);
			}
			if ($affected > 0)
			{
				$changed = true;
			}
		}

		if ($insert)
		{
			$db->insertBulk(
				'xf_permission_entry',
				$insert,
				false,
				'permission_value = VALUES(permission_value), permission_value_int = VALUES(permission_value_int)'
			);
			$changed = true;
		}

		return $changed;
	}

	protected function insertContentPermissionsInternal(
		$contentType, $contentId, $type, $id, array $permissionsGrouped, $deleteExisting = false
	)
	{
		$insertExtra = [
			'content_type' => $contentType,
			'content_id' => $contentId
		];

		$insert = $this->getPermissionInsertData($type, $id, $permissionsGrouped, $insertExtra);
		$db = $this->db();
		$changed = false;

		if ($deleteExisting)
		{
			$deleteParams = [$id, $contentType, $contentId];

			if ($type == 'group')
			{
				$affected = $db->delete(
					'xf_permission_entry_content',
					'user_group_id = ? AND user_id = 0 AND content_type = ? AND content_id = ?',
					$deleteParams
				);
			}
			else if ($type == 'user')
			{
				$affected = $db->delete(
					'xf_permission_entry_content',
					'user_id = ? AND user_group_id = 0 AND content_type = ? AND content_id = ?',
					$deleteParams
				);
			}
			else
			{
				$affected = $db->delete(
					'xf_permission_entry_content',
					'user_id = 0 AND user_group_id = 0 AND content_type = ? AND content_id = ?',
					[$contentType, $contentId]
				);
			}

			if ($affected > 0)
			{
				$changed = true;
			}
		}

		if ($insert)
		{
			$db->insertBulk(
				'xf_permission_entry_content',
				$insert,
				false,
				'permission_value = VALUES(permission_value), permission_value_int = VALUES(permission_value_int)'
			);
			$changed = true;
		}

		return $changed;
	}

	protected function loadKnownPermissions()
	{
		if ($this->knownPermissions === null)
		{
			$output = [];
			$results = $this->db()->fetchAll("
				SELECT permission_group_id, permission_id
				FROM xf_permission
			");
			foreach ($results AS $result)
			{
				$output[$result['permission_group_id']][$result['permission_id']] = true;
			}

			$this->knownPermissions = $output;
		}
	}
}