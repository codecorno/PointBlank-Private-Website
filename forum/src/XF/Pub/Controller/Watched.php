<?php

namespace XF\Pub\Controller;

use XF\Mvc\ParameterBag;

class Watched extends AbstractController
{
	protected function preDispatchController($action, ParameterBag $params)
	{
		$this->assertRegistrationRequired();
	}

	public function actionThreads()
	{
		$this->setSectionContext('forums');

		$page = $this->filterPage();
		$perPage = $this->options()->discussionsPerPage;

		/** @var \XF\Repository\Thread $threadRepo */
		$threadRepo = $this->repository('XF:Thread');
		$threadFinder = $threadRepo->findThreadsForWatchedList();

		$total = $threadFinder->total();
		$threads = $threadFinder->limitByPage($page, $perPage)->fetch();

		$viewParams = [
			'page' => $page,
			'perPage' => $perPage,
			'total' => $total,
			'threads' => $threads->filterViewable()
		];
		return $this->view('XF:Watched\Threads', 'watched_threads_list', $viewParams);
	}

	public function actionThreadsManage()
	{
		$this->setSectionContext('forums');

		if (!$state = $this->filter('state', 'str'))
		{
			return $this->redirect($this->buildLink('watched/threads'));
		}

		if ($this->isPost())
		{
			/** @var \XF\Repository\ThreadWatch $threadWatchRepo */
			$threadWatchRepo = $this->repository('XF:ThreadWatch');

			if ($threadWatchRepo->isValidWatchState($state))
			{
				$threadWatchRepo->setWatchStateForAll(\XF::visitor(), $state);
			}

			return $this->redirect($this->buildLink('watched/threads'));
		}
		else
		{
			$viewParams = [
				'state' => $state
			];
			return $this->view('XF:Watched\ThreadsManage', 'watched_threads_manage', $viewParams);
		}
	}

	public function actionThreadsUpdate()
	{
		$this->assertPostOnly();
		$this->setSectionContext('forums');

		/** @var \XF\Repository\ThreadWatch $threadWatchRepo */
		$threadWatchRepo = $this->repository('XF:ThreadWatch');

		$state = $this->filter('state', 'str');

		if ($state && $threadWatchRepo->isValidWatchState($state))
		{
			$threadIds = $this->filter('thread_ids', 'array-uint');
			$threads = $this->em()->findByIds('XF:Thread', $threadIds);
			$visitor = \XF::visitor();

			/** @var \XF\Entity\Thread $thread */
			foreach ($threads AS $thread)
			{
				$threadWatchRepo->setWatchState($thread, $visitor, $state);
			}
		}

		return $this->redirect($this->buildLink('watched/threads'));
	}

	public function actionForums()
	{
		$this->setSectionContext('forums');

		/** @var \XF\Repository\Node $nodeRepo */
		$nodeRepo = $this->repository('XF:Node');

		$watchedFinder = $this->finder('XF:ForumWatch');
		/** @var \XF\Entity\ForumWatch[]|\XF\Mvc\Entity\ArrayCollection $watchedForums */
		$watchedForums = $watchedFinder->where('user_id', \XF::visitor()->user_id)
			->keyedBy('node_id')
			->with('Forum', true)
			->with('Forum.Node', true)
			->fetch();

		$nodes = $nodeRepo->getFullNodeList();
		$nodes = $nodes->filter(function(\XF\Entity\Node $node) use ($watchedForums)
		{
			if ($node->display_in_list || isset($watchedForums[$node->node_id]))
			{
				return true;
			}

			$lft = $node->lft;
			$rgt = $node->rgt;

			foreach ($watchedForums AS $watched)
			{
				$watchedNode = $watched->Forum->Node;
				if ($watchedNode->lft > $lft && $watchedNode->rgt < $rgt)
				{
					// we're watching a node within this so we have to include it
					return true;
				}
			}

			return false;
		});
		$nodeRepo->loadNodeTypeDataForNodes($nodes);
		$nodes = $nodeRepo->filterViewable($nodes);

		$nodeTree = $nodeRepo->createNodeTree($nodes);
		$nodeExtras = $nodeRepo->getNodeListExtras($nodeTree);

		$viewParams = [
			'nodeTree' => $nodeTree,
			'nodeExtras' => $nodeExtras,

			'watchedForums' => $watchedForums
		];
		return $this->view('XF:Watched\Forums', 'watched_forums_list', $viewParams);
	}

	public function actionForumsManage()
	{
		$this->setSectionContext('forums');

		if (!$state = $this->filter('state', 'str'))
		{
			return $this->redirect($this->buildLink('watched/forums'));
		}

		if ($this->isPost())
		{
			/** @var \XF\Repository\ForumWatch $forumWatchRepo */
			$forumWatchRepo = $this->repository('XF:ForumWatch');

			if ($forumWatchRepo->isValidWatchState($state))
			{
				$forumWatchRepo->setWatchStateForAll(\XF::visitor(), $state);
			}

			return $this->redirect($this->buildLink('watched/forums'));
		}
		else
		{
			$viewParams = [
				'state' => $state
			];
			return $this->view('XF:Watched\ForumsManage', 'watched_forums_manage', $viewParams);
		}
	}

	public function actionForumsUpdate()
	{
		$this->assertPostOnly();
		$this->setSectionContext('forums');

		if ($action = $this->filter('action', 'str'))
		{
			$nodeIds = $this->filter('node_ids', 'array-uint');
			$visitor = \XF::visitor();

			foreach ($nodeIds AS $nodeId)
			{
				$watch = $this->em()->find('XF:ForumWatch', [
					'node_id' => $nodeId,
					'user_id' => $visitor->user_id
				]);
				if (!$watch)
				{
					continue;
				}

				switch ($action)
				{
					case 'email':
					case 'no_email':
						$watch->send_email = ($action == 'email');
						$watch->save();
						break;

					case 'alert':
					case 'no_alert':
						$watch->send_alert = ($action == 'alert');
						$watch->save();
						break;

					case 'delete':
						$watch->delete();
						break;
				}
			}
		}

		return $this->redirect($this->buildLink('watched/forums'));
	}

	public static function getActivityDetails(array $activities)
	{
		return \XF::phrase('managing_account_details');
	}
}