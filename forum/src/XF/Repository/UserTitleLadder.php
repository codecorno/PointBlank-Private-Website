<?php

namespace XF\Repository;

use XF\Mvc\Entity\Finder;
use XF\Mvc\Entity\Repository;

class UserTitleLadder extends Repository
{
	/**
	 * @return Finder
	 */
	public function findLadder()
	{
		return $this->finder('XF:UserTitleLadder')->order('minimum_level');
	}

	public function recreateLadder(array $records)
	{
		$db = $this->db();

		$filtered = [];
		foreach ($records AS $record)
		{
			if (isset($record['minimum_level']) && !empty($record['title']))
			{
				$filtered[$record['minimum_level']] = $record['title'];
			}
		}

		$db->beginTransaction();
		$db->delete('xf_user_title_ladder', null); // don't use emptyTable as it may not work in a transaction

		foreach ($filtered AS $level => $title)
		{
			$ladder = $this->em->create('XF:UserTitleLadder');
			$ladder->minimum_level = $level;
			$ladder->title = $title;
			$ladder->save();
		}

		$this->rebuildLadderCache();

		$db->commit();
	}

	public function getLadderCacheData()
	{
		return $this->db()->fetchPairs("
			SELECT minimum_level, title
			FROM xf_user_title_ladder
			ORDER BY minimum_level DESC
		");
	}

	public function rebuildLadderCache()
	{
		$cache = $this->getLadderCacheData();
		\XF::registry()->set('userTitleLadder', $cache);

		return $cache;
	}
}