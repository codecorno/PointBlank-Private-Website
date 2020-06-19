<?php

namespace XF\Repository;

use XF\Mvc\Entity\Finder;
use XF\Mvc\Entity\Repository;

class UserGroup extends Repository
{
	protected $displayStyleMapCache = [];

	/**
	 * @return Finder
	 */
	public function findUserGroupsForList()
	{
		return $this->finder('XF:UserGroup')->order('title');
	}

	public function getUserGroupTitlePairs()
	{
		return $this->findUserGroupsForList()->fetch()->pluckNamed('title', 'user_group_id');
	}

	public function getUserGroupOptionsData($includeEmpty = true, $type = null)
	{
		$choices = [];
		if ($includeEmpty)
		{
			$choices = [
				0 => ['_type' => 'option', 'value' => 0, 'label' => \XF::phrase('(none)')]
			];
		}

		$userGroups = $this->getUserGroupTitlePairs();

		foreach ($userGroups AS $userGroupId => $label)
		{
			$choices[$userGroupId] = [
				'value' => $userGroupId,
				'label' => $label
			];
			if ($type !== null)
			{
				$choices[$userGroupId]['_type'] = $type;
			}
		}

		return $choices;
	}

	public function getDisplayGroupIdForUser(\XF\Entity\User $user)
	{
		$groups = $user->secondary_group_ids;
		$groups[] = $user->user_group_id;

		$groups = array_unique($groups);
		sort($groups, SORT_NUMERIC);

		$cacheId = implode(',', $groups);
		if (isset($this->displayStyleMapCache[$cacheId]))
		{
			return $this->displayStyleMapCache[$cacheId];
		}

		$result = $this->db()->fetchOne("
			SELECT user_group_id
			FROM xf_user_group
			WHERE user_group_id IN (" . $this->db()->quote($groups) . ")
			ORDER BY display_style_priority DESC
			LIMIT 1
		");
		if (!$result)
		{
			$result = end($groups);
		}

		$this->displayStyleMapCache[$cacheId] = $result;

		return $result;
	}

	public function rebuildDisplayPriority($userGroupId, $oldPriority, $newPriority)
	{
		if ($oldPriority == $newPriority)
		{
			return;
		}

		$userGroups = $this->finder('XF:UserGroup')->order('title')->fetch();

		$betweenGroupIds = [];

		$lowerBound = min($oldPriority, $newPriority);
		$upperBound = max($oldPriority, $newPriority);

		foreach ($userGroups AS $userGroup)
		{
			if ($userGroup->display_style_priority >= $lowerBound
				&& $userGroup->display_style_priority < $upperBound
				&& $userGroup->user_group_id != $userGroupId
			)
			{
				$betweenGroupIds[] = $userGroup->user_group_id;
			}
		}

		if (!$betweenGroupIds)
		{
			return;
		}

		$db = $this->db();

		if ($newPriority > $oldPriority)
		{
			// moving up: all who have this group as highest stay; switch to this if highest
			// was between old and new priorities and member of this group
			$updateUserIds = $db->fetchAllColumn('
				SELECT user.user_id
				FROM xf_user AS user
				INNER JOIN xf_user_group_relation AS user_group_relation ON
					(user_group_relation.user_id = user.user_id AND user_group_relation.user_group_id = ?)
				WHERE user.display_style_group_id IN (' . $db->quote($betweenGroupIds) . ')
			', $userGroupId);
			if ($updateUserIds)
			{
				$db->update('xf_user',
					['display_style_group_id' => $userGroupId],
					'user_id IN (' . $db->quote($updateUserIds) . ')'
				);
			}
		}
		else
		{
			// moving down: need to recalculate for users that have this group as highest
			// but only need to for users that have more than one group
			$updateRes = $db->query('
				SELECT user.user_id, user_group.display_style_priority, user_group.user_group_id
				FROM xf_user AS user
				INNER JOIN xf_user_group_relation AS user_group_relation ON
					(user.user_id = user_group_relation.user_id
					AND user_group_relation.user_group_id <> user.display_style_group_id)
				INNER JOIN xf_user_group AS user_group ON
					(user_group.user_group_id = user_group_relation.user_group_id)
				WHERE user.display_style_group_id = ?
				GROUP BY user.user_id
			', $userGroupId);

			$updateGroups = [];
			$updatePriorities = [];
			while ($update = $updateRes->fetch())
			{
				// seed with current data if not set
				if (!isset($updateGroups[$update['user_id']]))
				{
					$updateGroups[$update['user_id']] = $userGroupId;
					$updatePriorities[$update['user_id']] = $newPriority;
				}

				// if higher, update
				if ($update['display_style_priority'] > $updatePriorities[$update['user_id']])
				{
					$updateGroups[$update['user_id']] = $update['user_group_id'];
					$updatePriorities[$update['user_id']] = $update['display_style_priority'];
				}
			}

			foreach ($updateGroups AS $userId => $displayGroupId)
			{
				// if ==, then no change from the current display group id
				if ($displayGroupId != $userGroupId)
				{
					$db->update('xf_user',
						['display_style_group_id' => $displayGroupId],
						'user_id = ?', $userId
					);
				}
			}
		}
	}

	public function getDisplayStyleCacheData()
	{
		$data = [];
		foreach ($this->finder('XF:UserGroup')->fetch() AS $userGroup)
		{
			$data[$userGroup->user_group_id] = [
				'username_css' => $userGroup->username_css,
				'user_title' => $userGroup->user_title
			];
		}

		return $data;
	}

	public function rebuildDisplayStyleCache()
	{
		$cache = $this->getDisplayStyleCacheData();
		\XF::registry()->set('displayStyles', $cache);

		/** @var \XF\Repository\Style $styleRepo */
		$styleRepo = $this->repository('XF:Style');
		$styleRepo->updateAllStylesLastModifiedDateLater();

		return $cache;
	}

	public function getUserBannerCacheData()
	{
		$cache = [];
		$userGroups = $this->finder('XF:UserGroup')
			->where('banner_text', '<>', '')
			->order('display_style_priority', 'desc')
			->fetch();

		foreach ($userGroups AS $userGroup)
		{
			$cache[$userGroup->user_group_id] = [
				'class' => $userGroup->banner_css_class,
				'text' => $userGroup->banner_text
			];
		}

		return $cache;
	}

	public function rebuildUserBannerCache()
	{
		$cache = $this->getUserBannerCacheData();
		\XF::registry()->set('userBanners', $cache);

		return $cache;
	}
}