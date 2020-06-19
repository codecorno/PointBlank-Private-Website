<?php

namespace XF\Repository;

use XF\Mvc\Entity\Finder;
use XF\Mvc\Entity\Repository;

class Feed extends Repository
{
	/**
	 * @return Finder
	 */
	public function findFeedsForList()
	{
		return $this->finder('XF:Feed')->order('title');
	}

	/**
	 * @return Finder
	 */
	public function findDueFeeds($time = null)
	{
		/** @var \XF\Finder\Feed $finder */
		$finder = $this->finder('XF:Feed');

		return $finder
			->isDue($time)
			->where('active', true)
			->with(['Forum', 'Forum.Node'], true)
			->order('last_fetch');
	}
}