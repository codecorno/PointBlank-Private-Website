<?php

namespace XF\Repository;

use XF\Mvc\Entity\Repository;

class Post extends Repository
{
	public function findPostsForThreadView(\XF\Entity\Thread $thread, array $limits = [])
	{
		/** @var \XF\Finder\Post $finder */
		$finder = $this->finder('XF:Post');
		$finder
			->inThread($thread, $limits)
			->orderByDate()
			->with('full');

		return $finder;
	}
	
	public function findNewestPostsInThread(\XF\Entity\Thread $thread, $newerThan, array $limits = [])
	{
		/** @var \XF\Finder\Post $finder */
		$finder = $this->finder('XF:Post');
		$finder
			->inThread($thread, $limits)
			->orderByDate('DESC')
			->newerThan($newerThan);

		return $finder;
	}

	public function findNextPostsInThread(\XF\Entity\Thread $thread, $newerThan, array $limits = [])
	{
		/** @var \XF\Finder\Post $finder */
		$finder = $this->finder('XF:Post');
		$finder
			->inThread($thread, $limits)
			->orderByDate()
			->newerThan($newerThan);

		return $finder;
	}

	public function sendModeratorActionAlert(\XF\Entity\Post $post, $action, $reason = '', array $extra = [])
	{
		if (!$post->user_id || !$post->User)
		{
			return false;
		}

		$extra = array_merge([
			'title' => $post->Thread->title,
			'prefix_id' => $post->Thread->prefix_id,
			'link' => $this->app()->router('public')->buildLink('nopath:posts', $post),
			'threadLink' => $this->app()->router('public')->buildLink('nopath:threads', $post->Thread),
			'reason' => $reason
		], $extra);

		/** @var \XF\Repository\UserAlert $alertRepo */
		$alertRepo = $this->repository('XF:UserAlert');
		$alertRepo->alert(
			$post->User,
			0, '',
			'user', $post->user_id,
			"post_{$action}", $extra
		);

		return true;
	}
}