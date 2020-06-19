<?php

namespace XF\Pub\Controller;

use XF\Mvc\ParameterBag;
use XF\Mvc\RouteMatch;

class Page extends AbstractController
{
	public function actionIndex(ParameterBag $params)
	{
		$page = $this->assertViewablePage($params->node_name);
		$pageRepo = $this->getPageRepo();
		$nodeRepo = $this->getNodeRepo();

		$this->assertCanonicalUrl($this->buildLink('pages', $page));

		if ($page->log_visits)
		{
			$pageRepo->logView($page, \XF::visitor());
		}

		$siblings = $page->list_siblings ? $nodeRepo->findSiblings($page->Node)->fetch() : null;
		$children = $page->list_children ? $nodeRepo->findChildren($page->Node)->fetch() : null;

		$testNodes = $this->em()->getEmptyCollection();
		if ($siblings)
		{
			$testNodes = $testNodes->merge($siblings);
		}
		if ($children)
		{
			$testNodes = $testNodes->merge($children);
		}
		if ($testNodes->count())
		{
			$nodeRepo->loadNodeTypeDataForNodes($testNodes);
		}
		if ($siblings)
		{
			$siblings = $nodeRepo->filterViewable($siblings);
		}
		if ($children)
		{
			$children = $nodeRepo->filterViewable($children);
		}

		$viewParams = [
			'page' => $page,
			'parent' => $page->Node->Parent,
			'siblings' => $siblings,
			'children' => $children
		];
		$reply = $this->view('XF:Page\View', 'page_view', $viewParams);

		if ($page->callback_class && $page->callback_method)
		{
			call_user_func_array([$page->callback_class, $page->callback_method], [$this, &$reply]);
		}

		return $reply;
	}

	public function actionBookmark(ParameterBag $params)
	{
		$page = $this->assertViewablePage($params->node_name);
		$node = $page->Node;

		/** @var \XF\ControllerPlugin\Bookmark $bookmarkPlugin */
		$bookmarkPlugin = $this->plugin('XF:Bookmark');

		return $bookmarkPlugin->actionBookmark(
			$node, $this->buildLink('pages/bookmark', $node)
		);
	}

	/**
	 * @param string $nodeName
	 * @param array $extraWith
	 *
	 * @return \XF\Entity\Page
	 *
	 * @throws \XF\Mvc\Reply\Exception
	 */
	protected function assertViewablePage($nodeName, array $extraWith = [])
	{
		$visitor = \XF::visitor();
		$extraWith[] = 'Node.Permissions|' . $visitor->permission_combination_id;

		$finder = $this->em()->getFinder('XF:Page')
			->with('Node', true)
			->with('Node.Parent')
			->with($extraWith)
			->where([
				'Node.node_name' => $nodeName,
				'Node.node_type_id' => 'Page'
			]);

		/** @var \XF\Entity\Page $page */
		$page = $finder->fetchOne();
		if (!$page)
		{
			throw $this->exception($this->notFound(\XF::phrase('requested_page_not_found')));
		}
		if (!$page->canView($error))
		{
			throw $this->exception($this->noPermission($error));
		}

		$this->plugin('XF:Node')->applyNodeContext($page->Node);

		return $page;
	}

	/**
	 * @return \XF\Repository\Node
	 */
	protected function getNodeRepo()
	{
		return $this->repository('XF:Node');
	}

	/**
	 * @return \XF\Repository\Page
	 */
	protected function getPageRepo()
	{
		return $this->repository('XF:Page');
	}

	/**
	 * @param \XF\Entity\SessionActivity[] $activities
	 */
	public static function getActivityDetails(array $activities)
	{
		return \XF\ControllerPlugin\Node::getNodeActivityDetails(
			$activities,
			'Page',
			\XF::phrase('viewing_page')
		);
	}

	// in case these have custom URL which is a page node
	public function assertPolicyAcceptance($action) {}
}