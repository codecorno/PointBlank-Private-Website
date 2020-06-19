<?php

namespace XF\Finder;

use XF\Mvc\Entity\Finder;

class NewsFeed extends Finder
{
	public function beforeFeedId($feedId)
	{
		if ($feedId)
		{
			$this->where('news_feed_id', '<', $feedId);
		}

		return $this;
	}

	public function applyPrivacyChecks(\XF\Entity\User $viewingUser = null)
	{
		if (!$viewingUser)
		{
			$viewingUser = \XF::visitor();
		}

		if ($viewingUser->canBypassUserPrivacy())
		{
			// no limits
			return $this;
		}

		if ($viewingUser->user_id)
		{
			$privacyConditions = [];
			$privacyConditions[] = ['user_id', $viewingUser->user_id];
			$privacyConditions[] = ['user_id', 0];
			$privacyConditions[] = ['User.Privacy.allow_receive_news_feed', ['everyone', 'members']];
			$privacyConditions[] = [
				['User.Privacy.allow_receive_news_feed', 'followed'],
				['User.Following|' . $viewingUser->user_id . '.user_id', '!=', null]
			];

			$this->whereOr($privacyConditions);
		}
		else
		{
			$this->whereOr(
				['user_id' => 0],
				['User.Privacy.allow_receive_news_feed' => 'everyone']
			);
		}

		return $this;
	}

	public function forUser(\XF\Entity\User $user)
	{
		if ($user->user_id)
		{
			$following = $user->Profile->following ?: [];

			$this->where('user_id', $following);
		}
		else
		{
			$this->whereImpossible();
		}

		return $this;
	}

	public function byUser(\XF\Entity\User $user)
	{
		$this->where('user_id', $user->user_id);

		return $this;
	}
}