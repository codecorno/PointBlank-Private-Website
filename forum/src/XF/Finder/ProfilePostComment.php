<?php

namespace XF\Finder;

use XF\Mvc\Entity\Finder;

class ProfilePostComment extends Finder
{
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

	public function forProfilePost(\XF\Entity\ProfilePost $profilePost, array $limits = [])
	{
		$limits = array_replace([
			'visibility' => true,
			'allowOwnPending' => true
		], $limits);

		$this->where('profile_post_id', $profilePost->profile_post_id);

		if ($limits['visibility'])
		{
			$this->applyVisibilityChecksForProfilePost($profilePost, $limits['allowOwnPending']);
		}

		return $this;
	}

	public function applyVisibilityChecksForProfilePost(\XF\Entity\ProfilePost $profilePost, $allowOwnPending = true)
	{
		$conditions = [];
		$viewableStates = ['visible'];

		if ($profilePost->canViewDeletedComments())
		{
			$viewableStates[] = 'deleted';
			$this->with('DeletionLog');
		}

		$visitor = \XF::visitor();
		if ($profilePost->canViewModeratedComments())
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

	public function newerThan($date)
	{
		$this->where('comment_date', '>', $date);

		return $this;
	}
}