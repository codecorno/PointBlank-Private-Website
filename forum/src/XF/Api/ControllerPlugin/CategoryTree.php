<?php

namespace XF\Api\ControllerPlugin;

use XF\Entity\ReactionTrait;
use XF\Mvc\Entity\Entity;
use XF\Mvc\FormAction;

class CategoryTree extends AbstractPlugin
{
	public function actionGet(\XF\Repository\AbstractCategoryTree $repo)
	{
		$tree = $this->getCategoryTreeForList($repo);

		/** @var \XF\Mvc\Entity\AbstractCollection $categories */
		$categories = $tree->getAllData();

		$result = [
			'tree_map' => (object)$tree->getParentMapSimplified(),
			'categories' => $categories->toApiResults()
		];
		return $this->apiResult($result);
	}

	public function actionGetFlattened(\XF\Repository\AbstractCategoryTree $repo)
	{
		$tree = $this->getCategoryTreeForList($repo);

		$flat = [];
		foreach ($tree->getFlattened() AS $id => $data)
		{
			$flat[] = [
				'category' => $data['record']->toApiResult(),
				'depth' => $data['depth']
			];
		}

		return $this->apiResult(['categories_flat' => $flat]);
	}

	public function getCategoryTreeForList(
		\XF\Repository\AbstractCategoryTree $repo, \XF\Entity\AbstractCategoryTree $withinCategory = null, $with = 'api'
	)
	{
		if (\XF::isApiCheckingPermissions())
		{
			$categories = $repo->getViewableCategories($withinCategory, $with);
		}
		else
		{
			$categories = $repo->findCategoryList($withinCategory, $with)->fetch();
		}

		return $repo->createCategoryTree($categories);
	}

	/**
	 * @param \XF\Entity\AbstractCategoryTree $category
	 * @return \XF\Api\Mvc\Reply\ApiResult
	 *
	 * @api-in bool $delete_children If true, child nodes will be deleted. Otherwise, they will be connected to this node's parent.
	 *
	 * @api-out true $success
	 */
	public function actionDelete(\XF\Entity\AbstractCategoryTree $category)
	{
		$deleteChildAction = $this->filter('delete_children', 'bool') ? 'delete' : 'move';
		$category->getBehavior('XF:TreeStructured')->setOption('deleteChildAction', $deleteChildAction);

		$category->delete();

		return $this->apiSuccess();
	}
}