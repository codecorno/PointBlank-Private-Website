<?php

namespace XF\Widget;

class NewThreads extends AbstractWidget
{
	protected $defaultOptions = [
		'limit' => 5,
		'node_ids' => '',
		'date_limit_days' => 0,
		'style' => 'simple',
		'show_expanded_title' => false
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
		$style = $options['style'];
		$nodeIds = $options['node_ids'];
		$dateLimit = isset($options['date_limit_days']) ? $options['date_limit_days'] : 0;

		$router = $this->app->router('public');

		/** @var \XF\Repository\Thread $threadRepo */
		$threadRepo = $this->repository('XF:Thread');

		$threadFinder = $threadRepo->findLatestThreads();
		$title = \XF::phrase('latest_threads');
		$link = $router->buildLink('whats-new');

		$threadFinder
			->with('Forum.Node.Permissions|' . $visitor->permission_combination_id)
			->limit(max($limit * 4, 20));

		if ($nodeIds && !in_array(0, $nodeIds))
		{
			$threadFinder->where('node_id', $nodeIds);
		}

		if ($dateLimit > 0)
		{
			$threadFinder->where('post_date', '>=', \XF::$time - ($dateLimit * 86400));
		}

		if ($style == 'full')
		{
			$threadFinder->with('fullForum');
		}
		if ($style == 'expanded')
		{
			$threadFinder->with('FirstPost');
		}

		/** @var \XF\Entity\Thread $thread */
		foreach ($threads = $threadFinder->fetch() AS $threadId => $thread)
		{
			if (!$thread->canView()
				|| $thread->isIgnored()
			)
			{
				unset($threads[$threadId]);
			}

			if ($options['style'] != 'expanded' && $visitor->isIgnoring($thread->last_post_user_id))
			{
				unset($threads[$threadId]);
			}
		}
		$threads = $threads->slice(0, $limit, true);

		$viewParams = [
			'title' => $this->getTitle() ?: $title,
			'link' => $link,
			'threads' => $threads,
			'style' => $options['style'],
			'showExpandedTitle' => $options['show_expanded_title']
		];
		return $this->renderer('widget_new_threads', $viewParams);
	}

	public function verifyOptions(\XF\Http\Request $request, array &$options, &$error = null)
	{
		$options = $request->filter([
			'limit' => 'uint',
			'style' => 'str',
			'node_ids' => 'array-uint',
			'show_expanded_title' => 'bool',
			'date_limit_days' => 'uint'
		]);

		if (in_array(0, $options['node_ids']))
		{
			$options['node_ids'] = [0];
		}

		if ($options['limit'] < 1)
		{
			$options['limit'] = 1;
		}

		if ($options['style'] != 'expanded')
		{
			$options['show_expanded_title'] = false;
		}
		
		return true;
	}
}