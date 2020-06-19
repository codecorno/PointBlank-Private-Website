<?php

namespace XF\Repository;

use XF\Mvc\Entity\Finder;
use XF\Mvc\Entity\Repository;

class MemberStat extends Repository
{
	/**
	 * @return Finder
	 */
	public function findMemberStatsForList()
	{
		return $this->finder('XF:MemberStat')
			->order('display_order');
	}

	/**
	 * @return Finder
	 */
	public function findMemberStatsForDisplay()
	{
		/** @var \XF\Finder\MemberStat $finder */
		$finder = $this->finder('XF:MemberStat');

		$finder
			->activeOnly()
			->order('display_order')
			->keyedBy('member_stat_key');

		return $finder;
	}

	/**
	 * @return Finder
	 */
	public function findCacheableMemberStats()
	{
		/** @var \XF\Finder\MemberStat $finder */
		$finder = $this->finder('XF:MemberStat');

		$finder
			->activeOnly()
			->cacheableOnly()
			->order('member_stat_id');

		return $finder;
	}

	public function emptyCache($memberStatKey)
	{
		/** @var \XF\Finder\MemberStat $finder */
		$finder = $this->finder('XF:MemberStat');

		$memberStat = $finder
			->cacheableOnly()
			->where('member_stat_key', $memberStatKey)
			->order('member_stat_id')
			->fetchOne();

		if (!$memberStat)
		{
			return false;
		}

		$memberStat->cache_results = null;
		$memberStat->cache_expiry = 0;
		$memberStat->save();
		return true;
	}
}