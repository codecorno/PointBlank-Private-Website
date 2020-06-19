<?php

namespace XF\Finder;

use XF\Mvc\Entity\Finder;

class Thread extends Finder
{
	public function inForum(\XF\Entity\Forum $forum, array $limits = [])
	{
		$limits = array_replace([
			'visibility' => true,
			'allowOwnPending' => false
		], $limits);

		$this->where('node_id', $forum->node_id);

		$this->applyForumDefaultOrder($forum);

		if ($limits['visibility'])
		{
			$this->applyVisibilityChecksInForum($forum, $limits['allowOwnPending']);
		}

		return $this;
	}

	public function applyVisibilityChecksInForum(\XF\Entity\Forum $forum, $allowOwnPending = false)
	{
		$conditions = [];
		$viewableStates = ['visible'];

		if ($forum->canViewDeletedThreads())
		{
			$viewableStates[] = 'deleted';

			$this->with('DeletionLog');
		}

		$visitor = \XF::visitor();
		if ($forum->canViewModeratedThreads())
		{
			$viewableStates[] = 'moderated';
		}
		else if ($visitor->user_id && $allowOwnPending)
		{
			$conditions[] = [
				'discussion_state' => 'moderated',
				'user_id' => $visitor->user_id
			];
		}

		$conditions[] = ['discussion_state', $viewableStates];

		$this->whereOr($conditions);

		$visitor = \XF::visitor();
		if (!$visitor->hasNodePermission($forum->node_id, 'viewOthers'))
		{
			if ($visitor->user_id)
			{
				$this->where('user_id', $visitor->user_id);
			}
			else
			{
				$this->whereSql('1=0'); // force false immediately
			}
		}

		return $this;
	}

	public function applyForumDefaultOrder(\XF\Entity\Forum $forum)
	{
		$this->setDefaultOrder($forum->default_sort_order, $forum->default_sort_direction);

		return $this;
	}

	/**
	 * @deprecated Use with('full') or with('fullForum') instead
	 *
	 * @param bool $includeForum
	 *
	 * @return $this
	 */
	public function forFullView($includeForum = false)
	{
		$this->with($includeForum ? 'fullForum' : 'full');

		return $this;
	}

	public function withReadData($userId = null)
	{
		if ($userId === null)
		{
			$userId = \XF::visitor()->user_id;
		}

		if ($userId)
		{
			$this->with([
				'Read|' . $userId,
				'Forum.Read|' . $userId
			]);
		}

		return $this;
	}

	public function unreadOnly($userId = null)
	{
		if ($userId === null)
		{
			$userId = \XF::visitor()->user_id;
		}
		if (!$userId)
		{
			// no user, no read tracking
			return $this;
		}

		$threadReadExpression = $this->expression(
			'%s > COALESCE(%s, 0)',
			'last_post_date',
			'Read|' . $userId . '.thread_read_date'
		);

		$forumReadExpression = $this->expression(
			'%s > COALESCE(%s, 0)',
			'last_post_date',
			'Forum.Read|' . $userId . '.forum_read_date'
		);

		/** @var \XF\Repository\Thread $threadRepo */
		$threadRepo = $this->em->getRepository('XF:Thread');

		$this->where('last_post_date', '>', $threadRepo->getReadMarkingCutOff())
			->where($threadReadExpression)
			->where($forumReadExpression);

		return $this;
	}

	public function watchedOnly($userId = null)
	{
		if ($userId === null)
		{
			$userId = \XF::visitor()->user_id;
		}
		if (!$userId)
		{
			// no user, just ignore
			return $this;
		}

		$this->whereOr(
			['Watch|' . $userId . '.user_id', '!=', null],
			['Forum.Watch|' . $userId . '.user_id', '!=', null]
		);

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
}