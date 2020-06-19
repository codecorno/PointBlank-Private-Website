<?php

namespace XF\Repository;

use XF\Mvc\Entity\Finder;
use XF\Mvc\Entity\Repository;

class UserIgnored extends Repository
{
	public function getIgnoredUserCache($userId)
	{
		return $this->db()->fetchPairs('
			SELECT user.user_id, user.username
			FROM xf_user_ignored AS ignored
			INNER JOIN xf_user AS user ON (ignored.ignored_user_id = user.user_id)
			WHERE ignored.user_id = ?
				AND user.is_staff = 0
				AND user.user_id <> ignored.user_id
			ORDER BY user.username
		', $userId);
	}

	public function rebuildIgnoredCache($userId)
	{
		$cache = $this->getIgnoredUserCache($userId);

		$profile = $this->em->find('XF:UserProfile', $userId);
		if ($profile)
		{
			$profile->fastUpdate('ignored', $cache);
		}

		return $cache;
	}

	public function rebuildIgnoredCacheByIgnoredUser($ignoredUserId)
	{
		$ignorers = $this->db()->fetchAllColumn("
			SELECT user_id
			FROM xf_user_ignored
			WHERE ignored_user_id = ?
		", $ignoredUserId);

		$this->db()->beginTransaction();

		foreach ($ignorers AS $ignorer)
		{
			$this->rebuildIgnoredCache($ignorer);
		}

		$this->db()->commit();
	}
}