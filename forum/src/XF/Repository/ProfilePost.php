<?php

namespace XF\Repository;

use XF\Mvc\Entity\Repository;

class ProfilePost extends Repository
{
	public function findProfilePostsOnProfile(\XF\Entity\User $user, array $limits = [])
	{
		/** @var \XF\Finder\ProfilePost $finder */
		$finder = $this->finder('XF:ProfilePost');
		$finder
			->onProfile($user, $limits)
			->order('post_date', 'DESC');

		return $finder;
	}

	/**
	 * @param \XF\Entity\User $user
	 * @param $newerThan
	 * @param array $limits
	 *
	 * @return \XF\Finder\ProfilePost
	 */
	public function findNewestProfilePostsOnProfile(\XF\Entity\User $user, $newerThan, array $limits = [])
	{
		/** @var \XF\Finder\ProfilePost $finder */
		$finder = $this->findNewestProfilePosts($newerThan)
			->onProfile($user, $limits);

		return $finder;
	}

	/**
	 * @param $newerThan
	 *
	 * @return \XF\Finder\ProfilePost
	 */
	public function findNewestProfilePosts($newerThan)
	{
		/** @var \XF\Finder\ProfilePost $finder */
		$finder = $this->finder('XF:ProfilePost');
		$finder
			->newerThan($newerThan)
			->order('post_date', 'DESC');

		return $finder;
	}

	/**
	 * @param \XF\Entity\ProfilePost $profilePost
	 * @param array $limits
	 *
	 * @return \XF\Finder\ProfilePostComment
	 */
	public function findProfilePostComments(\XF\Entity\ProfilePost $profilePost, array $limits = [])
	{
		/** @var \XF\Finder\ProfilePostComment $commentFinder */
		$commentFinder = $this->finder('XF:ProfilePostComment');
		$commentFinder->setDefaultOrder('comment_date');
		$commentFinder->forProfilePost($profilePost, $limits);

		return $commentFinder;
	}

	public function findNewestCommentsForProfilePost(\XF\Entity\ProfilePost $profilePost, $newerThan, array $limits = [])
	{
		/** @var \XF\Finder\ProfilePostComment $commentFinder */
		$commentFinder = $this->finder('XF:ProfilePostComment');
		$commentFinder
			->setDefaultOrder('comment_date', 'DESC')
			->forProfilePost($profilePost, $limits)
			->newerThan($newerThan);

		return $commentFinder;
	}

	/**
	 * @param \XF\Mvc\Entity\AbstractCollection|\XF\Entity\ProfilePost[] $profilePosts
	 * @param bool $skipUnfurlRecrawl
	 *
	 * @return \XF\Mvc\Entity\AbstractCollection|\XF\Entity\ProfilePost[]
	 */
	public function addCommentsToProfilePosts($profilePosts, $skipUnfurlRecrawl = false)
	{
		$commentFinder = $this->finder('XF:ProfilePostComment');

		$visitor = \XF::visitor();

		$ids = [];
		foreach ($profilePosts AS $profilePostId => $profilePost)
		{
			$commentIds = $profilePost->latest_comment_ids;
			foreach ($commentIds AS $commentId => $state)
			{
				$commentId = intval($commentId);

				switch ($state[0])
				{
					case 'visible':
						$ids[] = $commentId;
						break;

					case 'moderated':
						if ($profilePost->canViewModeratedComments())
						{
							// can view all moderated comments
							$ids[] = $commentId;
						}
						else if ($visitor->user_id && $visitor->user_id == $state[1])
						{
							// can view your own moderated comments
							$ids[] = $commentId;
						}
						break;

					case 'deleted':
						if ($profilePost->canViewDeletedComments())
						{
							$ids[] = $commentId;

							$commentFinder->with('DeletionLog');
						}
						break;
				}
			}
		}

		if ($ids)
		{
			$commentFinder->with('full');

			$comments = $commentFinder
				->where('profile_post_comment_id', $ids)
				->order('comment_date')
				->fetch();

			/** @var \XF\Repository\Unfurl $unfurlRepo */
			$unfurlRepo = $this->repository('XF:Unfurl');
			$unfurlRepo->addUnfurlsToContent($comments, $skipUnfurlRecrawl);

			$comments = $comments->groupBy('profile_post_id');

			foreach ($profilePosts AS $profilePostId => $profilePost)
			{
				$profilePostComments = isset($comments[$profilePostId]) ? $comments[$profilePostId] : [];
				$profilePostComments = $this->em->getBasicCollection($profilePostComments)
					->filterViewable()
					->slice(-3, 3);

				$profilePost->setLatestComments($profilePostComments->toArray());
			}
		}

		return $profilePosts;
	}

	public function addCommentsToProfilePost(\XF\Entity\ProfilePost $profilePost)
	{
		$id = $profilePost->profile_post_id;
		$result = $this->addCommentsToProfilePosts([$id => $profilePost]);
		return $result[$id];
	}

	public function getLatestCommentCache(\XF\Entity\ProfilePost $profilePost)
	{
		$comments = $this->finder('XF:ProfilePostComment')
			->where('profile_post_id', $profilePost->profile_post_id)
			->order('comment_date', 'DESC')
			->limit(20)
			->fetch();

		$visCount = 0;
		$latestComments = [];

		/** @var \XF\Entity\ProfilePostComment $comment */
		foreach ($comments AS $commentId => $comment)
		{
			if ($comment->message_state == 'visible')
			{
				$visCount++;
			}

			$latestComments[$commentId] = [$comment->message_state, $comment->user_id];

			if ($visCount === 3)
			{
				break;
			}
		}

		return array_reverse($latestComments, true);
	}

	public function sendModeratorActionAlert(\XF\Entity\ProfilePost $profilePost, $action, $reason = '', array $extra = [])
	{
		if (!$profilePost->user_id || !$profilePost->User)
		{
			return false;
		}

		$router = $this->app()->router('public');

		$extra = array_merge([
			'profileUserId' => $profilePost->profile_user_id,
			'profileUser' => $profilePost->ProfileUser ? $profilePost->ProfileUser->username : '',
			'profileLink' => $router->buildLink('nopath:members', $profilePost->ProfileUser),
			'link' => $router->buildLink('nopath:profile-posts', $profilePost),
			'reason' => $reason
		], $extra);

		/** @var \XF\Repository\UserAlert $alertRepo */
		$alertRepo = $this->repository('XF:UserAlert');
		$alertRepo->alert(
			$profilePost->User,
			0, '',
			'user', $profilePost->user_id,
			"profile_post_{$action}", $extra
		);

		return true;
	}

	public function sendCommentModeratorActionAlert(\XF\Entity\ProfilePostComment $comment, $action, $reason = '', array $extra = [])
	{
		if (!$comment->user_id || !$comment->User)
		{
			return false;
		}

		/** @var \XF\Entity\ProfilePost $profilePost */
		$profilePost = $comment->ProfilePost;
		if (!$profilePost)
		{
			return false;
		}

		$router = $this->app()->router('public');

		$extra = array_merge([
			'profileUserId' => $profilePost->profile_user_id,
			'profileUser' => $profilePost->ProfileUser ? $profilePost->ProfileUser->username : '',
			'postUserId' => $profilePost->user_id,
			'postUser' => $profilePost->User ? $profilePost->User->username : '',
			'link' => $router->buildLink('nopath:profile-posts/comments', $comment),
			'profileLink' => $router->buildLink('nopath:members', $profilePost->ProfileUser),
			'profilePostLink' => $router->buildLink('nopath:profile-posts', $profilePost),
			'reason' => $reason
		], $extra);

		/** @var \XF\Repository\UserAlert $alertRepo */
		$alertRepo = $this->repository('XF:UserAlert');
		$alertRepo->alert(
			$comment->User,
			0, '',
			'user', $comment->user_id,
			"profile_post_comment_{$action}", $extra
		);

		return true;
	}
}