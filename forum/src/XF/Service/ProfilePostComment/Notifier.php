<?php

namespace XF\Service\ProfilePostComment;

use XF\Entity\ProfilePostComment;
use XF\Service\AbstractService;

class Notifier extends AbstractService
{
	protected $comment;

	protected $notifyProfileOwner;
	protected $notifyProfilePostAuthor;
	protected $notifyMentioned = [];
	protected $notifyOtherCommenters;

	protected $usersAlerted = [];

	public function __construct(\XF\App $app, ProfilePostComment $comment)
	{
		parent::__construct($app);

		$this->comment = $comment;
	}

	public function getNotifyProfileOwner()
	{
		if ($this->notifyProfileOwner === null)
		{
			$this->notifyProfileOwner = [$this->comment->ProfilePost->ProfileUser->user_id];
		}
		return $this->notifyProfileOwner;
	}

	public function getNotifyProfilePostAuthor()
	{
		if ($this->notifyProfilePostAuthor === null)
		{
			$this->notifyProfilePostAuthor = [$this->comment->ProfilePost->user_id];
		}
		return $this->notifyProfilePostAuthor;
	}

	public function setNotifyMentioned(array $mentioned)
	{
		$this->notifyMentioned = array_unique($mentioned);
	}

	public function getNotifyMentioned()
	{
		return $this->notifyMentioned;
	}

	public function getNotifyOtherCommenters()
	{
		if ($this->notifyOtherCommenters === null && $this->comment->ProfilePost)
		{
			/** @var \XF\Repository\ProfilePost $repo */
			$repo = $this->repository('XF:ProfilePost');
			$comments = $repo->findProfilePostComments($this->comment->ProfilePost, ['visibility' => false])
				->where('message_state', 'visible')
				->fetch();

			$this->notifyOtherCommenters = $comments->pluckNamed('user_id');
		}
		return $this->notifyOtherCommenters;
	}

	public function notify()
	{
		$notifiableUsers = $this->getUsersForNotification();

		$profileUserIds = $this->getNotifyProfileOwner();
		foreach ($profileUserIds AS $userId)
		{
			if (isset($notifiableUsers[$userId]))
			{
				$this->sendNotification($notifiableUsers[$userId], 'your_profile');
			}
		}

		$profilePostAuthors = $this->getNotifyProfilePostAuthor();
		foreach ($profilePostAuthors AS $userId)
		{
			if (isset($notifiableUsers[$userId]))
			{
				$this->sendNotification($notifiableUsers[$userId], 'your_post');
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

		$otherCommenters = $this->getNotifyOtherCommenters();
		foreach ($otherCommenters AS $userId)
		{
			if (isset($notifiableUsers[$userId]))
			{
				$this->sendNotification($notifiableUsers[$userId], 'other_commenter');
			}
		}
	}

	protected function getUsersForNotification()
	{
		$userIds = array_merge(
			$this->getNotifyProfileOwner(),
			$this->getNotifyProfilePostAuthor(),
			$this->getNotifyMentioned(),
			$this->getNotifyOtherCommenters()
		);

		$comment = $this->comment;

		$users = $this->app->em()->findByIds('XF:User', $userIds, ['Profile', 'Option']);
		if (!$users->count())
		{
			return [];
		}

		$users = $users->toArray();
		foreach ($users AS $id => $user)
		{
			/** @var \XF\Entity\User $user */
			$canView = \XF::asVisitor($user, function() use ($comment) { return $comment->canView(); });
			if (!$canView)
			{
				unset($users[$id]);
			}
		}

		return $users;
	}

	protected function sendNotification(\XF\Entity\User $user, $action)
	{
		$comment = $this->comment;
		if ($user->user_id == $comment->user_id)
		{
			return false;
		}

		if (empty($this->usersAlerted[$user->user_id]))
		{
			/** @var \XF\Repository\UserAlert $alertRepo */
			$alertRepo = $this->app->repository('XF:UserAlert');
			if ($alertRepo->alert($user, $comment->user_id, $comment->username, 'profile_post_comment', $comment->profile_post_comment_id, $action))
			{
				$this->usersAlerted[$user->user_id] = true;
				return true;
			}
		}

		return false;
	}

}