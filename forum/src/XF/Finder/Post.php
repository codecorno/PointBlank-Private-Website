<?php

namespace XF\Finder;

use XF\Mvc\Entity\Finder;

class Post extends Finder
{
	public function inThread(\XF\Entity\Thread $thread, array $limits = [])
	{
		$limits = array_replace([
			'visibility' => true,
			'allowOwnPending' => true
		], $limits);

		$this->where('thread_id', $thread->thread_id);

		if ($limits['visibility'])
		{
			$this->applyVisibilityChecksInThread($thread, $limits['allowOwnPending']);
		}

		return $this;
	}

	public function applyVisibilityChecksInThread(\XF\Entity\Thread $thread, $allowOwnPending = true)
	{
		$conditions = [];
		$viewableStates = ['visible'];

		if ($thread->canViewDeletedPosts())
		{
			$viewableStates[] = 'deleted';

			$this->with('DeletionLog');
		}

		$visitor = \XF::visitor();
		if ($thread->canViewModeratedPosts())
		{
			$viewableStates[] = 'moderated';
		}
		else if ($visitor->user_id && $allowOwnPending)
		{
			$conditions[] = [
				'message_state' => 'moderated',
				'user_id' => $visitor->user_id
			];
		}

		$conditions[] = ['message_state', $viewableStates];

		$this->whereOr($conditions);

		return $this;
	}

	public function onPage($page, $perPage = null)
	{
		$page = max(1, intval($page));
		if ($perPage === null)
		{
			$perPage = $this->app()->options()->messagesPerPage;
		}
		$perPage = max(1, intval($perPage));

		$start = ($page - 1) * $perPage;
		$end = $start + $perPage;

		$this->where('position', '>=', $start)->where('position', '<', $end);

		return $this;
	}

	public function newerThan($date)
	{
		$this->where('post_date', '>', $date);

		return $this;
	}

	public function orderByDate($direction = 'ASC')
	{
		$this->order('position', $direction)->order('post_date', $direction);

		return $this;
	}

	public function skipIgnored(\XF\Entity\User $user = null)
	{
		if (!$user)
		{
			$user = \XF::visitor();
		}

		if (!$user->user_id)
		{
			return $this;
		}

		if ($user->Profile && $user->Profile->ignored)
		{
			$this->where('user_id', '<>', array_keys($user->Profile->ignored));
		}

		return $this;
	}

	public function isNotFirstPost()
	{
		$this->where($this->expression(
			'%s <> %s',
			'Thread.first_post_id',
			'post_id'
		));

		return $this;
	}

	/**
	 * @deprecated Use with('full')
	 *
	 * @return $this
	 */
	public function forFullView()
	{
		$this->with('full');

		return $this;
	}
}