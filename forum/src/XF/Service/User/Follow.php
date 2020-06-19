<?php

namespace XF\Service\User;

use XF\Service\AbstractService;

class Follow extends AbstractService
{
	/**
	 * @var \XF\Entity\User
	 */
	protected $followedBy;

	/**
	 * @var \XF\Entity\User
	 */
	protected $followUser;

	protected $silent = false;

	public function __construct(\XF\App $app, \XF\Entity\User $followUser, \XF\Entity\User $followedBy = null)
	{
		parent::__construct($app);

		$this->followUser = $followUser;
		$this->followedBy = $followedBy ?: \XF::visitor();
	}

	public function setSilent($silent)
	{
		$this->silent = (bool)$silent;
	}

	public function follow()
	{
		$userFollow = $this->em()->create('XF:UserFollow');
		$userFollow->user_id = $this->followedBy->user_id;
		$userFollow->follow_user_id = $this->followUser->user_id;

		try
		{
			$saved = $userFollow->save(false);
		}
		catch (\XF\Db\DuplicateKeyException $e)
		{
			$saved = false;

			$dupe = $this->em()->findOne('XF:UserFollow', [
				'user_id' => $this->followedBy->user_id,
				'follow_user_id' => $this->followUser->user_id
			]);
			if ($dupe)
			{
				$userFollow = $dupe;
			}
		}

		if ($saved)
		{
			$this->sendFollowingAlert();
		}

		return $userFollow;
	}

	protected function sendFollowingAlert()
	{
		if ($this->silent)
		{
			return;
		}

		$followedBy = $this->followedBy;
		$followUser = $this->followUser;

		if (!$followUser->isIgnoring($followedBy->user_id)
			&& $followUser->Option->doesReceiveAlert('user', 'following')
		)
		{
			/** @var \XF\Repository\UserAlert $alertRepo */
			$alertRepo = $this->repository('XF:UserAlert');
			$alertRepo->alert(
				$followUser, $followedBy->user_id, $followedBy->username, 'user', $followUser->user_id, 'following'
			);
		}
	}

	public function unfollow()
	{
		$userFollow = $this->em()->findOne('XF:UserFollow', [
			'user_id' => $this->followedBy->user_id,
			'follow_user_id' => $this->followUser->user_id
		]);

		if ($userFollow && $userFollow->delete())
		{
			$this->deleteFollowingAlert();
		}

		return $userFollow;
	}

	protected function deleteFollowingAlert()
	{
		$alertRepo = $this->repository('XF:UserAlert');
		$alertRepo->fastDeleteAlertsFromUser(
			$this->followedBy->user_id, 'user', $this->followUser->user_id, 'following'
		);
	}
}