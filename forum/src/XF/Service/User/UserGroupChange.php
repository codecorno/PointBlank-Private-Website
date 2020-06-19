<?php

namespace XF\Service\User;

class UserGroupChange extends \XF\Service\AbstractService
{
	/**
	 * Inserts (or updates an existing) user group change set.
	 *
	 * @param integer $userId
	 * @param string $key Unique identifier for change set
	 * @param string|array $addGroups Comma delimited string or array of user groups to add
	 *
	 * @return boolean True on change success
	 */
	public function addUserGroupChange($userId, $key, $addGroups)
	{
		if (is_array($addGroups))
		{
			$addGroups = implode(',', $addGroups);
		}

		$oldChanges = $this->getUserGroupChangesForUser($userId);
		$newChanges = $oldChanges;

		if (!$addGroups)
		{
			if (isset($newChanges[$key]))
			{
				// already exists and we're removing the groups, so we can just remove the record
				return $this->removeUserGroupChange($userId, $key);
			}
			else
			{
				// would be inserting but nothing to do anyway
				return true;
			}
		}

		$newChanges[$key] = $addGroups;

		$db = $this->db();
		$db->beginTransaction();

		$success = $this->applyUserGroupChanges($userId, $oldChanges, $newChanges, $addGroups);
		if ($success)
		{
			$db->insert('xf_user_group_change', [
				'user_id' => $userId,
				'change_key' => $key,
				'group_ids' => $addGroups
			], false, 'group_ids = VALUES(group_ids)');

			$db->commit();
		}
		else
		{
			$db->rollback();
		}

		return $success;
	}

	/**
	 * Removes the specified user group change set.
	 *
	 * @param integer $userId
	 * @param string $key Change set key
	 *
	 * @return boolean True on success
	 */
	public function removeUserGroupChange($userId, $key)
	{
		$oldChanges = $this->getUserGroupChangesForUser($userId);
		if (!isset($oldChanges[$key]))
		{
			// already removed?
			return true;
		}

		$newChanges = $oldChanges;
		unset($newChanges[$key]);

		$db = $this->db();
		$db->beginTransaction();

		$success = $this->applyUserGroupChanges($userId, $oldChanges, $newChanges);
		if ($success)
		{
			$db->delete('xf_user_group_change',
				'user_id = ? AND change_key = ?',
				[$userId, $key]
			);

			$db->commit();
		}
		else
		{
			$db->rollback();
		}

		return $success;
	}

	public function removeUserGroupChangeLogByKey($key)
	{
		$this->db()->delete('xf_user_group_change', 'change_key = ?', $key);
	}

	public function getUserGroupChangesForUser($userId)
	{
		return $this->db()->fetchPairs('
			SELECT change_key, group_ids
			FROM xf_user_group_change
			WHERE user_id = ?
		', $userId);
	}

	/**
	 * Applies a set of user group changes.
	 *
	 * @param integer $userId
	 * @param array $oldGroupStrings Array of comma-delimited strings of existing (accounted for) user group change sets
	 * @param array $newGroupStrings Array of comma-delimited strings for new list of user group change sets
	 * @param string $forceAdd A comma-delimited list of groups to force add, even if they were already in the old groups
	 *
	 * @return boolean
	 */
	protected function applyUserGroupChanges($userId, array $oldGroupStrings, array $newGroupStrings, $forceAdd = '')
	{
		$oldGroups = [];
		foreach ($oldGroupStrings AS $string)
		{
			if ($string)
			{
				$oldGroups = array_merge($oldGroups, explode(',', $string));
			}
		}
		$oldGroups = array_unique($oldGroups);

		$newGroups = [];
		foreach ($newGroupStrings AS $string)
		{
			if ($string)
			{
				$newGroups = array_merge($newGroups, explode(',', $string));
			}
		}
		$newGroups = array_unique($newGroups);

		$removeGroups = array_diff($oldGroups, $newGroups);
		$addGroups = array_diff($newGroups, $oldGroups);
		if ($forceAdd)
		{
			$addGroups = array_merge($addGroups, explode(',', $forceAdd));
			$addGroups = array_unique($addGroups);
		}

		if (!$addGroups && !$removeGroups)
		{
			return true;
		}

		/** @var \XF\Entity\User $user */
		$user = $this->em()->find('XF:User', $userId);
		if (!$user)
		{
			throw new \LogicException("User '$userId' could not be found");
		}

		$secondaryGroupIds = $user->secondary_group_ids;
		if ($removeGroups)
		{
			foreach ($secondaryGroupIds AS $key => $secondaryGroup)
			{
				if (in_array($secondaryGroup, $removeGroups))
				{
					unset($secondaryGroupIds[$key]);
				}
			}
		}
		if ($addGroups)
		{
			$secondaryGroupIds = array_merge($secondaryGroupIds, $addGroups);
		}

		$user->secondary_group_ids = $secondaryGroupIds;

		if ($user->isChanged('secondary_group_ids'))
		{
			return $user->save(false, false);
		}
		else
		{
			return true;
		}
	}
}