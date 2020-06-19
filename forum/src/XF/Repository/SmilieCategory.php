<?php

namespace XF\Repository;

use XF\Mvc\Entity\ArrayCollection;
use XF\Mvc\Entity\Finder;
use XF\Mvc\Entity\Repository;

class SmilieCategory extends Repository
{
	public function getDefaultCategory()
	{
		$smilieCategory = $this->em->create('XF:SmilieCategory');
		$smilieCategory->setTrusted('smilie_category_id', 0);
		$smilieCategory->setTrusted('display_order', 0);
		$smilieCategory->setReadOnly(true);

		return $smilieCategory;
	}

	public function findSmilieCategoriesForList($getDefault = false)
	{
		$categories = $this->finder('XF:SmilieCategory')
			->with('MasterTitle')
			->order(['display_order'])
			->fetch();

		if ($getDefault)
		{
			$defaultCategory = $this->getDefaultCategory();
			$smilieCategories = $categories->toArray();
			$smilieCategories = [$defaultCategory] + $smilieCategories;
			$categories = $this->em->getBasicCollection($smilieCategories);
		}

		return $categories;
	}

	public function getSmilieCategoryTitlePairs()
	{
		$smilieCategories = $this->finder('XF:SmilieCategory')
			->order('display_order');

		return $smilieCategories->fetch()->pluckNamed('title', 'smilie_category_id');
	}
}