<?php

namespace XF\Repository;

use XF\Mvc\Entity\Finder;
use XF\Mvc\Entity\Repository;

class AdminNavigation extends Repository
{
	/**
	 * @return Finder
	 */
	public function findNavigationForList()
	{
		return $this->finder('XF:AdminNavigation')->order(['parent_navigation_id', 'display_order']);
	}

	public function createNavigationTree($entries = null, $rootId = '')
	{
		if ($entries === null)
		{
			$entries = $this->findNavigationForList()->fetch();
		}

		return new \XF\Tree($entries, 'parent_navigation_id', $rootId);
	}

	public function getNavigationCacheData()
	{
		$entries = $this->finder('XF:AdminNavigation')
			->whereAddOnActive()
			->order(['parent_navigation_id', 'display_order'])
			->fetch();

		$output = [];
		foreach ($entries AS $entry)
		{
			/** @var $entry \XF\Entity\AdminNavigation */
			$output[$entry->navigation_id] = [
				'navigation_id' => $entry->navigation_id,
				'parent_navigation_id' => $entry->parent_navigation_id,
				'link' => $entry->link,
				'icon' => $entry->icon,
				'phrase' => $entry->getPhraseName(),
				'admin_permission_id' => $entry->admin_permission_id,
				'debug_only' => $entry->debug_only,
				'development_only' => $entry->development_only,
				'hide_no_children' => $entry->hide_no_children
			];
		}

		return $output;
	}

	public function rebuildNavigationCache()
	{
		$cache = $this->getNavigationCacheData();
		\XF::registry()->set('adminNavigation', $cache);
		return $cache;
	}
}