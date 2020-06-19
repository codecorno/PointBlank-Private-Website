<?php

namespace XF\ControllerPlugin;

use XF\Mvc\Entity\Entity;
use XF\Mvc\ParameterBag;

abstract class AbstractCategoryTree extends AbstractPlugin
{
	protected $viewFormatter = '';
	protected $templateFormatter = '';
	protected $routePrefix = '';
	protected $entityIdentifier = '';
	protected $primaryKey = '';

	public function actionList(array $options = [])
	{
		$options = array_replace([
			'permissionContentType' => ''
		], $options);

		$categoryRepo = $this->getCategoryRepo();
		$categories = $categoryRepo->findCategoryList()->fetch();
		$categoryTree = $categoryRepo->createCategoryTree($categories);

		if ($options['permissionContentType'])
		{
			/** @var \XF\Repository\PermissionEntry $entryRepo */
			$entryRepo = $this->repository('XF:PermissionEntry');
			$customPermissions = $entryRepo->getContentWithCustomPermissions($options['permissionContentType']);
		}
		else
		{
			$customPermissions = [];
		}

		$viewParams = [
			'categoryTree' => $categoryTree,
			'permissionContentType' => $options['permissionContentType'],
			'customPermissions' => $customPermissions
		];
		return $this->view(
			$this->formatView('List'),
			$this->formatTemplate('list'),
			$viewParams
		);
	}

	public function actionDelete(ParameterBag $params)
	{
		$category = $this->assertCategoryExists($params->{$this->primaryKey});
		if ($this->isPost())
		{
			$childAction = $this->filter('child_nodes_action', 'str');
			$category->getBehavior('XF:TreeStructured')->setOption('deleteChildAction', $childAction);

			$category->delete();
			return $this->redirect($this->buildLink($this->routePrefix));
		}
		else
		{
			$viewParams = [
				'category' => $category
			];
			return $this->view(
				$this->formatView('Delete'),
				$this->formatTemplate('delete'),
				$viewParams
			);
		}
	}

	public function actionSort()
	{
		$categoryRepo = $this->getCategoryRepo();
		$categories = $categoryRepo->findCategoryList()->fetch();
		$categoryTree = $categoryRepo->createCategoryTree($categories);

		if ($this->isPost())
		{
			/** @var \XF\ControllerPlugin\Sort $sorter */
			$sorter = $this->plugin('XF:Sort');
			$sortTree = $sorter->buildSortTree($this->filter('categories', 'json-array'));
			$sorter->sortTree($sortTree, $categoryTree->getAllData(), 'parent_category_id');

			return $this->redirect($this->buildLink($this->routePrefix));
		}
		else
		{
			$viewParams = [
				'categoryTree' => $categoryTree
			];
			return $this->view(
				$this->formatView('Sort'),
				$this->formatTemplate('sort'),
				$viewParams
			);
		}
	}

	protected function formatView($type)
	{
		$formatter = $this->viewFormatter;

		if (is_string($formatter))
		{
			return sprintf($formatter, $type);
		}
		else if ($formatter instanceof \Closure)
		{
			return $formatter($type);
		}
		else
		{
			return '';
		}
	}

	protected function formatTemplate($type)
	{
		$formatter = $this->templateFormatter;

		if (is_string($formatter))
		{
			return sprintf($formatter, $type);
		}
		else if ($formatter instanceof \Closure)
		{
			return $formatter($type);
		}
		else
		{
			return '';
		}
	}

	/**
	 * @param string $id
	 * @param array|string|null $with
	 * @param null|string $phraseKey
	 *
	 * @return \XF\Entity\AbstractCategoryTree
	 */
	protected function assertCategoryExists($id, $with = null, $phraseKey = null)
	{
		return $this->assertRecordExists($this->entityIdentifier, $id, $with, $phraseKey);
	}

	/**
	 * @return \XF\Repository\AbstractCategoryTree
	 */
	protected function getCategoryRepo()
	{
		return $this->repository($this->entityIdentifier);
	}
}