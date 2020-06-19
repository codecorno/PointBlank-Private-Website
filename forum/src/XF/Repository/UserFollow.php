<?php

namespace XF\Repository;

use XF\Mvc\Entity\Finder;
use XF\Mvc\Entity\Repository;

class UserFollow extends Repository
{
	/**
	 * @return Finder
	 */
	public function findFollowingForProfile(\XF\Entity\User $user)
	{
		return $this->finder('XF:UserFollow')
			->with('FollowUser', true)
			->with('FollowUser.Profile', true)
			->with('FollowUser.Option', true)
			->where('user_id', $user->user_id);
	}

	/**
	 * @return Finder
	 */
	public function findFollowersForProfile(\XF\Entity\User $user)
	{
		return $this->finder('XF:UserFollow')
			->with('User', true)
			->with('User.Profile', true)
			->with('User.Option', true)
			->where('follow_user_id', $user->user_id);
	}

	public function rebuildFollowingCache($userId)
	{
		$following = $this->db()->fetchAllColumn("
			SELECT follow_user_id
			FROM xf_user_follow
			WHERE user_id = ?
			AND follow_user_id <> ?
		", [$userId, $userId]);

		$profile = $this->em->find('XF:UserProfile', $userId);
		if ($profile)
		{
			$profile->fastUpdate('following', $following);
		}

		return $following;
	}
}