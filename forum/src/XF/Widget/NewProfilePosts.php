<?php

namespace XF\Widget;

class NewProfilePosts extends AbstractWidget
{
	protected $defaultOptions = [
		'limit' => 5,
		'style' => 'simple',
		'filter' => 'latest'
	];

	public function render()
	{
		$visitor = \XF::visitor();

		if (!$visitor->canViewProfilePosts())
		{
			return '';
		}

		$options = $this->options;
		$limit = $options['limit'];
		$filter = $options['filter'];

		if (!$visitor->user_id)
		{
			$filter = 'latest';
		}

		$router = $this->app->router('public');

		/** @var \XF\Finder\ProfilePost $profilePostFinder */
		$profilePostFinder = $this->finder('XF:ProfilePost');
		$profilePostFinder
			->with(['ProfileUser', 'ProfileUser.Privacy', 'User'])
			->where('message_state', 'visible')
			->order('post_date', 'DESC')
			->limit(max($limit * 2, 10));

		switch ($filter)
		{
			default:
			case 'latest':
				$title = \XF::phrase('widget.latest_profile_posts');
				$link = $router->buildLink('whats-new/profile-posts', null, ['skip' => 1]);
				break;

			case 'followed':
				$title = \XF::phrase('widget.latest_followed_profile_posts');
				$link = $router->buildLink('whats-new/profile-posts', null, ['followed' => 1]);

				$following = $visitor->Profile->following;
				$following[] = $visitor->user_id;

				$profilePostFinder->where('user_id', $following);
				break;
		}

		if ($options['style'] == 'full')
		{
			$profilePostFinder->with('fullProfile');
		}

		/** @var \XF\Entity\ProfilePost $profilePost */
		foreach ($profilePosts = $profilePostFinder->fetch() AS $profilePostId => $profilePost)
		{
			if (!$profilePost->canView()
				|| $profilePost->isIgnored()
			)
			{
				unset($profilePosts[$profilePostId]);
			}
		}
		$profilePosts = $profilePosts->slice(0, $limit, true);

		$viewParams = [
			'title' => $this->getTitle() ?: $title,
			'link' => $link,
			'profilePosts' => $profilePosts,
			'style' => $options['style'],
			'filter' => $filter
		];
		return $this->renderer('widget_new_profile_posts', $viewParams);
	}

	public function verifyOptions(\XF\Http\Request $request, array &$options, &$error = null)
	{
		$options = $request->filter([
			'limit' => 'uint',
			'style' => 'str',
			'filter' => 'str'
		]);
		if ($options['limit'] < 1)
		{
			$options['limit'] = 1;
		}

		return true;
	}
}