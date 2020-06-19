<?php

namespace XF\Admin\Controller;

use XF\Mvc\ParameterBag;

class Thread extends AbstractController
{
	protected function preDispatchController($action, ParameterBag $params)
	{
		$this->assertAdminPermission('thread');
	}

	public function actionList()
	{
		$this->setSectionContext('batchUpdateThreads');

		$criteria = $this->filter('criteria', 'array');
		$order = $this->filter('order', 'str');
		$direction = $this->filter('direction', 'str');

		$page = $this->filterPage();
		$perPage = 50;

		$showingAll = $this->filter('all', 'bool');
		if ($showingAll)
		{
			$page = 1;
			$perPage = 5000;
		}

		$criteria['not_discussion_type'] = 'redirect';
		$searcher = $this->searcher('XF:Thread', $criteria);
		$searcher->setOrder($order, $direction);

		$finder = $searcher->getFinder();
		$finder->limitByPage($page, $perPage);

		$total = $finder->total();
		$threads = $finder->fetch();

		$viewParams = [
			'threads' => $threads,

			'total' => $total,
			'page' => $page,
			'perPage' => $perPage,

			'showingAll' => $showingAll,
			'showAll' => (!$showingAll && $total <= 5000),

			'criteria' => $searcher->getFilteredCriteria(),
			'order' => $order,
			'direction' => $direction
		];
		return $this->view('XF:Thread\Listing', 'thread_list', $viewParams);
	}

	public function actionReplyBans()
	{
		$replyBanRepo = $this->getReplyBanRepo();
		$replyBanFinder = $replyBanRepo->findReplyBansForList();

		$user = null;
		$linkParams = [];
		if ($username = $this->filter('username', 'str'))
		{
			$user = $this->finder('XF:User')->where('username', $username)->fetchOne();
			if ($user)
			{
				$replyBanFinder->where('user_id', $user->user_id);
				$linkParams['username'] = $user->username;
			}
		}

		$page = $this->filterPage();
		$perPage = 25;

		$replyBanFinder->limitByPage($page, $perPage);
		$total = $replyBanFinder->total();

		$this->assertValidPage($page, $perPage, $total, 'threads/reply-bans');

		$viewParams = [
			'bans' => $replyBanFinder->fetch(),
			'user' => $user,

			'page' => $page,
			'perPage' => $perPage,
			'total' => $total,

			'linkParams' => $linkParams
		];
		return $this->view('XF:Thread\ReplyBan\Listing', 'thread_reply_ban_list', $viewParams);
	}

	public function actionReplyBansDelete(ParameterBag $params)
	{
		$replyBan = $this->assertReplyBanExists($params->thread_reply_ban_id);

		/** @var \XF\ControllerPlugin\Delete $plugin */
		$plugin = $this->plugin('XF:Delete');
		return $plugin->actionDelete(
			$replyBan,
			$this->buildLink('threads/reply-bans/delete', $replyBan),
			$this->buildLink('threads/reply-bans/edit', $replyBan),
			$this->buildLink('threads/reply-bans'),
			"{$replyBan->Thread->title} - {$replyBan->User->username}"
		);
	}

	public function actionBatchUpdate()
	{
		$this->setSectionContext('batchUpdateThreads');

		$searcher = $this->searcher('XF:Thread');

		$viewParams = [
			'criteria' => $searcher->getFormCriteria(),
			'success' => $this->filter('success', 'bool')
		] + $searcher->getFormData();
		return $this->view('XF:Thread\BatchUpdate', 'thread_batch_update', $viewParams);
	}

	public function actionBatchUpdateConfirm()
	{
		$this->setSectionContext('batchUpdateThreads');

		$this->assertPostOnly();

		$criteria = $this->filter('criteria', 'array');
		$criteria['not_discussion_type'] = 'redirect';
		$searcher = $this->searcher('XF:Thread', $criteria);

		$threadIds = $this->filter('thread_ids', 'array-uint');

		$total = count($threadIds) ?: $searcher->getFinder()->total();
		if (!$total)
		{
			throw $this->exception($this->error(\XF::phraseDeferred('no_items_matched_your_filter')));
		}

		if ($threadIds)
		{
			$threadFinder = $this->finder('XF:Thread');
			$threadFinder->where('thread_id', $threadIds);
		}
		else
		{
			$threadFinder = clone $searcher->getFinder();
		}
		$hasPrefixes = (bool)$threadFinder
			->where('prefix_id', '>', 0)
			->total();

		/** @var \XF\Repository\ThreadPrefix $prefixRepo */
		$prefixRepo = $this->repository('XF:ThreadPrefix');
		$prefixes = $prefixRepo->getPrefixListData();

		/** @var \XF\Repository\Node $nodeRepo */
		$nodeRepo = $this->repository('XF:Node');
		$forums = $nodeRepo->getNodeOptionsData(false, 'Forum');

		$viewParams = [
			'total' => $total,
			'threadIds' => $threadIds,
			'hasPrefixes' => $hasPrefixes,
			'criteria' => $searcher->getFilteredCriteria(),

			'prefixes' => $prefixes,
			'forums' => $forums
		];
		return $this->view('XF:Thread\BatchUpdate\Confirm', 'thread_batch_update_confirm', $viewParams);
	}

	public function actionBatchUpdateAction()
	{
		$this->setSectionContext('batchUpdateThreads');

		$this->assertPostOnly();

		if ($this->request->exists('thread_ids'))
		{
			$threadIds = $this->filter('thread_ids', 'json-array');
			$total = count($threadIds);
			$jobCriteria = null;
		}
		else
		{
			$criteria = $this->filter('criteria', 'json-array');
			$criteria['not_discussion_type'] = 'redirect';

			$searcher = $this->searcher('XF:Thread', $criteria);
			$total = $searcher->getFinder()->total();
			$jobCriteria = $searcher->getFilteredCriteria();

			$threadIds = null;
		}

		if (!$total)
		{
			throw $this->exception($this->error(\XF::phraseDeferred('no_items_matched_your_filter')));
		}

		$actions = $this->filter('actions', 'array');

		if ($this->request->exists('confirm_delete') && empty($actions['delete']))
		{
			return $this->error(\XF::phrase('you_must_confirm_deletion_to_proceed'));
		}

		$this->app->jobManager()->enqueueUnique('threadAction', 'XF:ThreadAction', [
			'total' => $total,
			'actions' => $actions,
			'threadIds' => $threadIds,
			'criteria' => $jobCriteria
		]);

		return $this->redirect($this->buildLink('threads/batch-update', null, ['success' => true]));
	}

	/**
	 * @param string $id
	 * @param array|string|null $with
	 * @param null|string $phraseKey
	 *
	 * @return \XF\Entity\ThreadReplyBan
	 */
	protected function assertReplyBanExists($id, array $extraWith = [], $phraseKey = null)
	{
		$extraWith[] = 'Thread';
		$extraWith[] = 'User';
		return $this->assertRecordExists('XF:ThreadReplyBan', $id, $extraWith, $phraseKey);
	}

	/**
	 * @return \XF\Repository\ThreadReplyBan
	 */
	protected function getReplyBanRepo()
	{
		return $this->repository('XF:ThreadReplyBan');
	}
}