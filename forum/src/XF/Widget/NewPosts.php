<?php

namespace XF\Widget;

class NewPosts extends AbstractWidget
{
	protected $defaultOptions = [
		'limit' => 5,
		'style' => 'simple',
		'filter' => 'latest',
		'node_ids' => []
	];

	protected function getDefaultTemplateParams($context)
	{
		$params = parent::getDefaultTemplateParams($context);
		if ($context == 'options')
		{
			$nodeRepo = $this->app->repository('XF:Node');
			$params['nodeTree'] = $nodeRepo->createNodeTree($nodeRepo->getFullNodeList());
		}
		return $params;
	}

	public function render()
	{
		$visitor = \XF::visitor();

		$options = $this->options;
		$limit = $options['limit'];
		$filter = $options['filter'];
		$nodeIds = $options['node_ids'];

		if (!$visitor->user_id)
		{
			$filter = 'latest';
		}

		$router = $this->app->router('public');

		/** @var \XF\Repository\Thread $threadRepo */
		$threadRepo = $this->repository('XF:Thread');

		switch ($filter)
		{
			default:
			case 'latest':
				$threadFinder = $threadRepo->findThreadsWithLatestPosts();
				$title = \XF::phrase('widget.latest_posts');
				$link = $router->buildLink('whats-new/posts', null, ['skip' => 1]);
				break;

			case 'unread':
				$threadFinder = $threadRepo->findThreadsWithUnreadPosts();
				$title = \XF::phrase('widget.unread_posts');
				$link = $router->buildLink('whats-new/posts', null, ['unread' => 1]);
				break;

			case 'watched':
				$threadFinder = $threadRepo->findThreadsForWatchedList();
				$title = \XF::phrase('widget.latest_watched');
				$link = $router->buildLink('whats-new/posts', null, ['watched' => 1]);
				break;
		}

		$threadFinder
			->with('Forum.Node.Permissions|' . $visitor->permission_combination_id)
			->limit(max($limit * 2, 10));

		if ($nodeIds && !in_array(0, $nodeIds))
		{
			$threadFinder->where('node_id', $nodeIds);
		}

		if ($options['style'] == 'full')
		{
			$threadFinder->with('fullForum');
		}
		else
		{
			$threadFinder
				->with('LastPoster')
				->withReadData();
		}

		/** @var \XF\Entity\Thread $thread */
		foreach ($threads = $threadFinder->fetch() AS $threadId => $thread)
		{
			if (!$thread->canView()
				|| $thread->isIgnored()
				|| $visitor->isIgnoring($thread->last_post_user_id)
			)
			{
				unset($threads[$threadId]);
			}
		}
		$total = $threads->count();
		$threads = $threads->slice(0, $limit, true);

		$viewParams = [
			'title' => $this->getTitle() ?: $title,
			'link' => $link,
			'threads' => $threads,
			'style' => $options['style'],
			'filter' => $filter,
			'hasMore' => $total > $threads->count()
		];
		return $this->renderer('widget_new_posts', $viewParams);
	}

	public function verifyOptions(\XF\Http\Request $request, array &$options, &$error = null)
	{
		$options = $request->filter([
			'limit' => 'uint',
			'style' => 'str',
			'filter' => 'str',
			'node_ids' => 'array-uint'
		]);
		if (in_array(0, $options['node_ids']))
		{
			$options['node_ids'] = [0];
		}
		if ($options['limit'] < 1)
		{
			$options['limit'] = 1;
		}

		return true;
	}
}