<?php

namespace XF\Repository;

use XF\Mvc\Entity\AbstractCollection;
use XF\Mvc\Entity\Finder;
use XF\Mvc\Entity\Repository;

class Style extends Repository
{
	/**
	 * @return Finder
	 */
	public function findStyles()
	{
		return $this->finder('XF:Style')->order('style_id');
	}

	/**
	 * @return \XF\Entity\Style
	 */
	public function getMasterStyle()
	{
		$style = $this->em->create('XF:Style');
		$style->setTrusted('style_id', 0);
		$style->setTrusted('parent_list', [0]);
		$style->setTrusted('parent_id', -1);
		$style->title = \XF::phrase('master_style');
		$style->setReadOnly(true);

		return $style;
	}

	public function getSelectableStyles()
	{
		$styles = [];
		foreach ($this->getStyleTree(false)->getFlattened(0) AS $id => $record)
		{
			if (\XF::visitor()->is_admin || $record['record']->user_selectable)
			{
				$styles[$id] = $record['record']->toArray();
				$styles[$id]['depth'] = $record['depth'];
			}
		}
		return $styles;
	}

	public function getStyleTree($withMaster = null)
	{
		$styles = $this->findStyles()->fetch();
		return $this->createStyleTree($styles, $withMaster);
	}

	public function createStyleTree($styles, $withMaster = null, $rootId = null)
	{
		if ($withMaster === null)
		{
			$withMaster = \XF::$developmentMode;
		}
		if ($withMaster)
		{
			if ($styles instanceof AbstractCollection)
			{
				$styles = $styles->toArray();
			}
			$styles[0] = $this->getMasterStyle();
		}

		if ($rootId === null)
		{
			$rootId = $withMaster ? -1 : 0;
		}

		return new \XF\Tree($styles, 'parent_id', $rootId);
	}

	protected static $lastModifiedUpdate = null;

	public function updateAllStylesLastModifiedDate()
	{
		$newModified = time();
		if (self::$lastModifiedUpdate && self::$lastModifiedUpdate === $newModified)
		{
			return;
		}

		self::$lastModifiedUpdate = $newModified;

		$this->db()->update('xf_style', ['last_modified_date' => $newModified], null);
		\XF::registry()->set('masterStyleModifiedDate', $newModified);

		// none of this will be valid, so use this opportunity to just wipe it
		$this->db()->emptyTable('xf_css_cache');

		\XF::runOnce('styleCacheRebuild', function()
		{
			$this->rebuildStyleCache();
		});
	}

	public function updateAllStylesLastModifiedDateLater()
	{
		\XF::runOnce('styleLastModifiedDate', function()
		{
			$this->updateAllStylesLastModifiedDate();
		});
	}

	public function getStyleCacheData()
	{
		$styles = $this->finder('XF:Style')->fetch();
		$cache = [];

		foreach ($styles AS $style)
		{
			/** @var \XF\Entity\Style $style */
			$cache[$style->style_id] = $style->toArray();
		}

		return $cache;
	}

	public function rebuildStyleCache()
	{
		$cache = $this->getStyleCacheData();
		\XF::registry()->set('styles', $cache);
		return $cache;
	}

	public function triggerStyleDataRebuild()
	{
		$this->app()->service('XF:Style\Rebuild')->rebuildFullParentList();

		$this->app()->jobManager()->enqueueUnique('styleRebuild', 'XF:Atomic', [
			'execute' => ['XF:TemplateRebuild', 'XF:StylePropertyRebuild']
		]);
	}
}