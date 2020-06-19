<?php

namespace XF\Service\ProfilePost;

use XF\Entity\ProfilePost;
use XF\Service\AbstractService;

class Notifier extends AbstractService
{
	protected $profilePost;

	protected $notifyInsert;
	protected $notifyMentioned = [];

	protected $usersAlerted = [];

	public function __construct(\XF\App $app, ProfilePost $profilePost)
	{
		parent::__construct($app);

		$this->profilePost = $profilePost;
	}

	public function getNotifyInsert()
	{
		if ($this->notifyInsert === null)
		{
			$this->notifyInsert = [$this->profilePost->profile_user_id];
		}
		return $this->notifyInsert;
	}

	public function setNotifyMentioned(array $mentioned)
	{
		$this->notifyMentioned = array_unique($mentioned);
	}

	public function getNotifyMentioned()
	{
		return $this->notifyMentioned;
	}

	public function notify()
	{
		$notifiableUsers = $this->getUsersForNotification();

		$insertUsers = $this->getNotifyInsert();
		foreach ($insertUsers AS $userId)
		{
			if (isset($notifiableUsers[$userId]))
			{
				$this->sendNotification($notifiableUsers[$userId], 'insert');
			}
		}

		$mentionUsers = $this->getNotifyMentioned();
		foreach ($mentionUsers AS $userId)
		{
			if (isset($notifiableUsers[$userId]))
			{
				$this->sendNotification($notifiableUsers[$userId], 'mention');
			}
		}
	}

	protected function getUsersForNotification()
	{
		$userIds = array_merge(
			$this->getNotifyInsert(),
			$this->getNotifyMentioned()
		);

		$profilePost = $this->profilePost;
		$users = $this->app->em()->findByIds('XF:User', $userIds, ['Profile', 'Option']);
		if (!$users->count())
		{
			return [];
		}

		$users = $users->toArray();
		foreach ($users AS $id => $user)
		{
			/** @var \XF\Entity\User $user */
			$canView = \XF::asVisitor($user, function() use ($profilePost) { return $profilePost->canView(); });
			if (!$canView)
			{
				unset($users[$id]);
			}
		}

		return $users;
	}

	protected function sendNotification(\XF\Entity\User $user, $action)
	{
		$profilePost = $this->profilePost;
		if ($user->user_id == $profilePost->user_id)
		{
			return false;
		}

		if (empty($this->usersAlerted[$user->user_id]))
		{
			/** @var \XF\Repository\UserAlert $alertRepo */
			$alertRepo = $this->app->repository('XF:UserAlert');
			if ($alertRepo->alert($user, $profilePost->user_id, $profilePost->username, 'profile_post', $profilePost->profile_post_id, $action))
			{
				$this->usersAlerted[$user->user_id] = true;
				return true;
			}
		}

		return false;
	}

}