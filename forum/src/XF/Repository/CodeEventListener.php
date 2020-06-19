<?php

namespace XF\Repository;

use XF\Mvc\Entity\Finder;
use XF\Mvc\Entity\Repository;

class CodeEventListener extends Repository
{
	/**
	 * @return Finder
	 */
	public function findListenersForList()
	{
		$listeners = $this->finder('XF:CodeEventListener')
			->order(['addon_id', 'event_id', 'execute_order']);

		return $listeners;
	}

	public function getListenerCacheData()
	{
		$listeners = $this->finder('XF:CodeEventListener')
			->whereAddOnActive(['disableProcessing' => true])
			->where('active', 1)
			->order(['event_id', 'execute_order'])
			->fetch();

		$cache = [];

		foreach ($listeners AS $listener)
		{
			$hint = $listener['hint'] ? $listener['hint'] : '_';
			$cache[$listener['event_id']][$hint][] = [$listener['callback_class'], $listener['callback_method']];
		}

		return $cache;
	}

	public function rebuildListenerCache()
	{
		$cache = $this->getListenerCacheData();
		\XF::registry()->set('codeEventListeners', $cache);
		return $cache;
	}
}