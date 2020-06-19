<?php

namespace XF\Repository;

use XF\Mvc\Entity\Finder;
use XF\Mvc\Entity\Repository;

class CronEntry extends Repository
{
	/**
	 * @return Finder
	 */
	public function findCronEntriesForList()
	{
		return $this->finder('XF:CronEntry')
			->with('AddOn')
			->order(['next_run']);
	}
}