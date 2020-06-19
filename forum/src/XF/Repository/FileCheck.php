<?php

namespace XF\Repository;

use XF\Mvc\Entity\Finder;
use XF\Mvc\Entity\Repository;

class FileCheck extends Repository
{
	/**
	 * @return Finder
	 */
	public function findFileChecksForList()
	{
		return $this->finder('XF:FileCheck')
			->setDefaultOrder('check_date', 'DESC');
	}

	public function pruneFileChecks($cutOff = null)
	{
		if ($cutOff === null)
		{
			$cutOff = \XF::$time - 86400 * 60;
		}

		/** @var \XF\Entity\FileCheck[] $fileChecks */
		$fileChecks = $this->finder('XF:FileCheck')
			->where('check_date', '<', $cutOff)
			->order('check_date', 'ASC')
			->fetch(1000);
		foreach ($fileChecks AS $fileCheck)
		{
			$fileCheck->delete();
		}
	}
}