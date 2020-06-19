<?php

namespace XF\Pub\Controller;

use XF\Mvc\ParameterBag;
use XF\Mvc\RouteMatch;
use XF\Mvc\Reply\AbstractReply;

class Category extends AbstractController
{
	public function actionIndex(ParameterBag $params)
	{
		$category = $this->assertViewableCategory($params->node_id);

		$this->assertCanonicalUrl($this->buildLink('categories', $category));

		$nodeRepo = $this->getNodeRepo();
		$nodes = $nodeRepo->getNodeList($category->Node);
		$nodeTree = count($nodes) ? $nodeRepo->createNodeTree($nodes, $category->node_id) : null;
		$nodeExtras = $nodeTree ? $nodeRepo->getNodeListExtras($nodeTree) : null;

		$hasForumDescendents = false;
		if ($nodeTree)
		{
			$nodeTree->traverse(function($id, \XF\Entity\Node $childNode) use (&$hasForumDescendents)
			{
				if ($childNode->node_type_id == 'Forum')
				{
					$hasForumDescendents = true;
				}
			});
		}

		$viewParams = [
			'category' => $category,
			'hasForumDescendents' => $hasForumDescendents,

			'nodeTree' => $nodeTree,
			'nodeExtras' => $nodeExtras
		];
		return $this->view('XF:Category\View', 'category_view', $viewParams);
	}

	public function actionMarkRead(ParameterBag $params)
	{
		$category = $this->assertViewableCategory($params->node_id);

		$visitor = \XF::visitor();
		if (!$visitor->user_id)
		{
			return $this->noPermission();
		}

		$markDate = $this->filter('date', 'uint');
		if (!$markDate)
		{
			$markDate = \XF::$time;
		}

		$forumRepo = $this->repository('XF:Forum');

		if ($this->isPost())
		{
			$forumRepo->markForumTreeReadByVisitor($category, $markDate);

			return $this->redirect(
				$this->buildLink('categories', $category),
				\XF::phrase('forum_x_marked_as_read', ['forum' => $category->title])
			);
		}
		else
		{
			$viewParams = [
				'category' => $category,
				'date' => $markDate
			];
			return $this->view('XF:Category\MarkRead', 'category_mark_read', $viewParams);
		}
	}

	/**
	 * @param $nodeId
	 * @param array $extraWith
	 *
	 * @return \XF\Entity\Category
	 *
	 * @throws \XF\Mvc\Reply\Exception
	 */
	protected function assertViewableCategory($nodeId, array $extraWith = [])
	{
		$visitor = \XF::visitor();
		$extraWith[] = 'Node.Permissions|' . $visitor->permission_combination_id;

		/** @var \XF\Entity\Category $category */
		$category = $this->em()->find('XF:Category', $nodeId, $extraWith);
		if (!$category)
		{
			throw $this->exception($this->notFound(\XF::phrase('requested_category_not_found')));
		}
		if (!$category->canView($error))
		{
			throw $this->exception($this->noPermission($error));
		}

		$this->plugin('XF:Node')->applyNodeContext($category->Node);

		return $category;
	}

	/**
	 * @return \XF\Repository\Node
	 */
	protected function getNodeRepo()
	{
		return $this->repository('XF:Node');
	}

	/**
	 * @return \XF\Repository\Thread
	 */
	protected function getThreadRepo()
	{
		return $this->repository('XF:Thread');
	}

	/**
	 * @param \XF\Entity\SessionActivity[] $activities
	 */
	public static function getActivityDetails(array $activities)
	{
		return \XF\ControllerPlugin\Node::getNodeActivityDetails(
			$activities,
			'Category',
			\XF::phrase('viewing_category')
		);
	}
}