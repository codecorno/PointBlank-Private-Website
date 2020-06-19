<?php

namespace XF\Finder;

use XF\Mvc\Entity\Finder;

class ProfilePost extends Finder
{
	/**
	 * @deprecated Use with('full') or with('fullProfile')
	 *
	 * @param bool $withProfile
	 *
	 * @return $this
	 */
	public function forFullView($withProfile = false)
	{
		$this->with($withProfile ? 'fullProfile' : 'full');

		return $this;
	}

	public function onProfile(\XF\Entity\User $user, array $limits = [])
	{
		$limits = array_replace([
			'visibility' => true,
			'allowOwnPending' => true
		], $limits);

		$this->where('profile_user_id', $user->user_id);

		if ($limits['visibility'])
		{
			$this->applyVisibilityChecksForProfile($user, $limits['allowOwnPending']);
		}

		$this->with('full');

		return $this;
	}

	public function applyVisibilityChecksForProfile(\XF\Entity\User $user, $allowOwnPending = true)
	{
		$conditions = [];
		$viewableStates = ['visible'];

		if ($user->canViewDeletedPostsOnProfile())
		{
			$viewableStates[] = 'deleted';
			$this->with('DeletionLog');
		}

		$visitor = \XF::visitor();
		if ($user->canViewModeratedPostsOnProfile())
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
		$this->where('post_date', '>', $date);

		return $this;
	}
}