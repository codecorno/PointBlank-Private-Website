<?php

namespace XF\FindNew;

use XF\Entity\FindNew;

class ProfilePost extends AbstractHandler
{
	public function getRoute()
	{
		return 'whats-new/profile-posts';
	}

	public function getPageReply(\XF\Mvc\Controller $controller, FindNew $findNew, array $results, $page, $perPage)
	{
		/** @var \XF\Repository\ProfilePost $profilePostRepo */
		$profilePostRepo = \XF::repository('XF:ProfilePost');
		$profilePosts = $profilePostRepo->addCommentsToProfilePosts($results);

		$canInlineMod = false;
		/** @var \XF\Entity\ProfilePost $profilePost */
		foreach ($profilePosts AS $profilePost)
		{
			if ($profilePost->canUseInlineModeration())
			{
				$canInlineMod = true;
				break;
			}
		}

		$viewParams = [
			'findNew' => $findNew,

			'page' => $page,
			'perPage' => $perPage,

			'profilePosts' => $profilePosts,
			'canInlineMod' => $canInlineMod
		];
		return $controller->view('XF:WhatsNew\ProfilePosts', 'whats_new_profile_posts', $viewParams);
	}

	public function getFiltersFromInput(\XF\Http\Request $request)
	{
		$filters = [];

		$visitor = \XF::visitor();
		$followed = $request->filter('followed', 'bool');

		if ($followed && $visitor->user_id)
		{
			$filters['followed'] = true;
		}

		return $filters;
	}

	public function getDefaultFilters()
	{
		return [];
	}

	public function getResultIds(array $filters, $maxResults)
	{
		/** @var \XF\Finder\ProfilePost $profilePostFinder */
		$profilePostFinder = \XF::finder('XF:ProfilePost')
			->where('message_state', '<>', 'moderated')
			->where('message_state', '<>', 'deleted')
			->order('post_date', 'DESC');

		$this->applyFilters($profilePostFinder, $filters);

		$profilePosts = $profilePostFinder->fetch($maxResults);
		$profilePosts = $this->filterResults($profilePosts);

		// TODO: consider overfetching or some other permission limits within the query

		return $profilePosts->keys();
	}

	public function getPageResultsEntities(array $ids)
	{
		$ids = array_map('intval', $ids);

		/** @var \XF\Finder\ProfilePost $profilePostFinder */
		$profilePostFinder = \XF::finder('XF:ProfilePost')
			->where('profile_post_id', $ids)
			->with('fullProfile');

		return $profilePostFinder->fetch();
	}

	protected function filterResults(\XF\Mvc\Entity\ArrayCollection $results)
	{
		$visitor = \XF::visitor();

		return $results->filter(function(\XF\Entity\ProfilePost $profilePosts) use($visitor)
		{
			return ($profilePosts->canView() && !$visitor->isIgnoring($profilePosts->user_id));
		});
	}

	protected function applyFilters(\XF\Finder\ProfilePost $profilePostFinder, array $filters)
	{
		$visitor = \XF::visitor();

		if (!empty($filters['followed']))
		{
			$following = $visitor->Profile->following;
			$following[] = $visitor->user_id;

			$profilePostFinder->where('user_id', $following);
		}
	}

	public function getResultsPerPage()
	{
		return \XF::options()->messagesPerPage;
	}

	public function isAvailable()
	{
		return \XF::visitor()->canViewProfilePosts();
	}
}