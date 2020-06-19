<?php

namespace XF\Repository;

use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Finder;
use XF\Mvc\Entity\Repository;

abstract class AbstractCategoryTree extends Repository
{
	/**
	 * @return string
	 */
	abstract protected function getClassName();

	/**
	 * @param array $extras
	 * @param array $childExtras
	 *
	 * @return array
	 */
	abstract public function mergeCategoryListExtras(array $extras, array $childExtras);

	/**
	 * @param Entity|null $withinCategory
	 * @param null $with
	 *
	 * @return Finder
	 */
	public function findCategoryList(Entity $withinCategory = null, $with = null)
	{
		$finder = $this->finder($this->getClassName())->setDefaultOrder('lft');
		if ($withinCategory)
		{
			$finder
				->where('lft', '>', $withinCategory->lft)
				->where('rgt', '<', $withinCategory->rgt);
		}
		if ($with)
		{
			$finder->with($with);
		}

		return $finder;
	}

	/**
	 * @param Entity|null $withinCategory
	 * @param null $with
	 *
	 * @return \XF\Mvc\Entity\ArrayCollection
	 */
	public function getViewableCategories(Entity $withinCategory = null, $with = null)
	{
		if ($with === null)
		{
			$with = [];
		}
		else
		{
			$with = (array)$with;
		}

		$with[] = 'Permissions|' . \XF::visitor()->permission_combination_id;

		/** @var \XF\Entity\AbstractCategoryTree $withinCategory */
		$categories = $this->findCategoryList($withinCategory, $with)->fetch();
		return $categories->filterViewable();
	}

	/**
	 * @param Entity|null $withinCategory
	 * @param bool $includeSelf
	 *
	 * @return array
	 */
	public function getViewableCategoryIds(Entity $withinCategory = null, $includeSelf = true)
	{
		$viewable = $this->getViewableCategories($withinCategory)->keys();
		if ($includeSelf && $withinCategory)
		{
			$viewable[] = $withinCategory->getEntityId();
		}

		return $viewable;
	}

	public function getCategoryIds(Entity $withinCategory = null, $includeSelf = true)
	{
		$ids = $this->findCategoryList($withinCategory)->fetch()->keys();
		if ($includeSelf && $withinCategory)
		{
			$ids[] = $withinCategory->getEntityId();
		}

		return $ids;
	}

	/**
	 * @param null $categories
	 * @param int $rootId
	 *
	 * @return \XF\Tree
	 */
	public function createCategoryTree($categories = null, $rootId = 0)
	{
		if ($categories === null)
		{
			$categories = $this->findCategoryList()->fetch();
		}
		return new \XF\Tree($categories, 'parent_category_id', $rootId);
	}

	/**
	 * @param \XF\Tree $categoryTree
	 *
	 * @return array
	 */
	public function getCategoryListExtras(\XF\Tree $categoryTree)
	{
		$finalOutput = [];
		$f = function(Entity $category, array $children) use (&$f, &$finalOutput)
		{
			/** @var \XF\Entity\AbstractCategoryTree $category */

			$childOutput = [];
			foreach ($children AS $id => $child)
			{
				/** @var \XF\SubTree $child */
				$childOutput[$id] = $f($child->record, $child->children());
			}

			$output = $this->mergeCategoryListExtras($category->getCategoryListExtras(), $childOutput);
			$finalOutput[$category->getEntityId()] = $output;

			return $output;
		};

		foreach ($categoryTree AS $id => $subTree)
		{
			$f($subTree->record, $subTree->children());
		}

		return $finalOutput;
	}

	/**
	 * @param bool $includeEmpty
	 * @param bool $checkPerms
	 *
	 * @return array
	 */
	public function getCategoryOptionsData($includeEmpty = true, $checkPerms = false)
	{
		$choices = [];
		if ($includeEmpty)
		{
			$choices = [
				0 => ['value' => 0, 'label' => '']
			];
		}

		$categoryList = $this->findCategoryList()->fetch();
		if ($checkPerms)
		{
			$categoryList = $categoryList->filterViewable();
		}

		foreach ($this->createCategoryTree($categoryList)->getFlattened() AS $entry)
		{
			/** @var \XF\Entity\AbstractCategoryTree $category */
			$category = $entry['record'];

			if ($entry['depth'])
			{
				$prefix = str_repeat('--', $entry['depth']) . ' ';
			}
			else
			{
				$prefix = '';
			}
			$choices[$category->getEntityId()] = [
				'value' => $category->getEntityId(),
				'label' => $prefix . $category->title
			];
		}

		return $choices;
	}

	public function findChildren(\XF\Entity\AbstractCategoryTree $category, $listable = true)
	{
		$finder = $this->finder($this->getClassName());

		$finder->where('parent_category_id', $category->category_id);

		$finder->setDefaultOrder('lft');

		return $finder;
	}
}