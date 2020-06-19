<?php

namespace XF\Pub\Controller;

use XF\Mvc\ParameterBag;

class FindThreads extends AbstractController
{
	protected $user = null;

	protected function preDispatchController($action, ParameterBag $params)
	{
		$this->assertRegistrationRequired();
	}

	public function actionIndex()
	{
		switch ($this->filter('type', 'str'))
		{
			case 'started':
				return $this->redirectPermanently($this->buildLink('find-threads/started'));
			case 'contributed':
				return $this->redirectPermanently($this->buildLink('find-threads/contributed'));
			case 'unanswered':
			default:
				return $this->redirectPermanently($this->buildLink('find-threads/unanswered'));
		}
	}

	public function actionUnanswered()
	{
		$threadFinder = $this->getThreadRepo()->findThreadsWithNoReplies();

		return $this->getThreadResults($threadFinder, 'unanswered');
	}

	public function actionStarted()
	{
		if (!$userId = $this->getUserId())
		{
			$this->assertRegistrationRequired();
		}

		$threadFinder = $this->getThreadRepo()->findThreadsStartedByUser($userId);

		return $this->getThreadResults($threadFinder, 'started');
	}

	public function actionContributed()
	{
		if (!$userId = $this->getUserId())
		{
			$this->assertRegistrationRequired();
		}

		$threadFinder = $this->getThreadRepo()->findThreadsWithPostsByUser($userId);

		return $this->getThreadResults($threadFinder, 'contributed');
	}

	protected function getThreadResults(\XF\Finder\Thread $threadFinder, $pageSelected)
	{
		$this->setSectionContext('forums');

		$forums = $this->repository('XF:Forum')->getViewableForums();

		$page = $this->filterPage();
		$perPage = $this->options()->discussionsPerPage;

		$threadFinder
			->where('discussion_state', 'visible')
			->where('node_id', $forums->keys())
			->limitByPage($page, $perPage);

		$total = $threadFinder->total();
		$threads = $threadFinder->fetch()->filterViewable();

		/** @var \XF\Entity\Thread $thread */
		$canInlineMod = false;
		foreach ($threads AS $threadId => $thread)
		{
			if ($thread->canUseInlineModeration())
			{
				$canInlineMod = true;
				break;
			}
		}

		$viewParams = [
			'page' => $page,
			'perPage' => $perPage,
			'total' => $total,
			'threads' => $threads->filterViewable(),
			'canInlineMod' => $canInlineMod,
			'user' => $this->user,
			'pageSelected' => $pageSelected,
		];
		return $this->view('XF:FindThreads\List', 'find_threads_list', $viewParams);
	}

	/**
	 * @return \XF\Repository\Thread
	 */
	protected function getThreadRepo()
	{
		return $this->repository('XF:Thread');
	}

	protected function getUserId()
	{
		$userId = $this->filter('user_id', 'uint');
		if (!$userId)
		{
			$this->user = \XF::visitor();
		}
		else
		{
			$this->user = $this->assertRecordExists('XF:User', $userId, null, 'requested_member_not_found');
		}

		return $this->user->user_id;
	}

	/**
	 * @param \XF\Entity\SessionActivity[] $activities
	 */
	public static function getActivityDetails(array $activities)
	{
		return \XF::phrase('viewing_latest_content');
	}
}