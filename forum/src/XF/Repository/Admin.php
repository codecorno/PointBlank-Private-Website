<?php

namespace XF\Repository;

use XF\Mvc\Entity\Finder;
use XF\Mvc\Entity\Repository;

class Admin extends Repository
{
	/**
	 * @return Finder
	 */
	public function findAdminsForList()
	{
		return $this->finder('XF:Admin')
			->with('User')
			->order('User.username');
	}
} 