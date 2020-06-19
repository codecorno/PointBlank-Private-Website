<?php

namespace XF\Repository;

use XF\Mvc\Entity\Finder;
use XF\Mvc\Entity\Repository;

class BbCode extends Repository
{
	/**
	 * @return Finder
	 */
	public function findBbCodesForList()
	{
		return $this->finder('XF:BbCode')->order(['bb_code_id']);
	}

	/**
	 * @return Finder
	 */
	public function findActiveBbCodes()
	{
		return $this->finder('XF:BbCode')
			->where('active', 1)
			->whereAddOnActive()
			->setDefaultOrder('bb_code_id');
	}

	public function getBbCodeCacheData()
	{
		$bbCodes = $this->findActiveBbCodes()->fetch();

		$cache = [];

		foreach ($bbCodes AS $bbCodeId => $bbCode)
		{
			$bbCode = $bbCode->toArray();
			unset($bbCode['bb_code_id'], $bbCode['active'], $bbCode['addon_id']);

			$cache[$bbCodeId] = $bbCode;
		}

		return $cache;
	}

	public function rebuildBbCodeCache()
	{
		$cache = $this->getBbCodeCacheData();
		\XF::registry()->set('bbCodeCustom', $cache);
		return $cache;
	}
}