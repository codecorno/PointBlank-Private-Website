<?php

namespace XF\Repository;

use XF\Mvc\Entity\Finder;
use XF\Mvc\Entity\Repository;

class ClassExtension extends Repository
{
	/**
	 * @return Finder
	 */
	public function findExtensionsForList()
	{
		$listeners = $this->finder('XF:ClassExtension')
			->order(['from_class', 'to_class']);

		return $listeners;
	}

	public function getExtensionCacheData()
	{
		$extensions = $this->finder('XF:ClassExtension')
			->whereAddOnActive(['disableProcessing' => true])
			->where('active', 1)
			->order(['execute_order'])
			->fetch();

		$cache = [];

		foreach ($extensions AS $extension)
		{
			$cache[$extension->from_class][] = $extension->to_class;
		}

		return $cache;
	}

	public function rebuildExtensionCache()
	{
		$cache = $this->getExtensionCacheData();
		\XF::registry()->set('classExtensions', $cache);
		return $cache;
	}
}