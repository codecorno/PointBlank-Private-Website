<?php

namespace XF\Repository;

use XF\Mvc\Entity\Finder;
use XF\Mvc\Entity\Repository;

class UserReject extends Repository
{
	/**
	 * @return Finder
	 */
	public function findUserRejectionsForList()
	{
		return $this->finder('XF:UserReject')
			->with('User')
			->with('RejectUser')
			->setDefaultOrder('reject_date', 'DESC');
	}
}