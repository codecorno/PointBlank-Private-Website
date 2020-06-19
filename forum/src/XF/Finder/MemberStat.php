<?php

namespace XF\Finder;

use XF\Mvc\Entity\Finder;

class MemberStat extends Finder
{
	public function activeOnly()
	{
		$this
			->where('active', 1)
			->whereAddOnActive();

		return $this;
	}

	public function cacheableOnly()
	{
		$this->where('cache_lifetime', '>', 0);

		return $this;
	}
}