<?php

namespace XF\Notifier\Post;

use XF\Notifier\AbstractNotifier;

abstract class AbstractWatch extends AbstractNotifier
{
	/**
	 * @var \XF\Entity\Post
	 */
	protected $post;

	protected $actionType;
	protected $isApplicable;

	protected $userReadDates = [];
	protected $previousPosts = null;

	abstract protected function getDefaultWatchNotifyData();
	abstract protected function getApplicableActionTypes();
	abstract protected function getWatchEmailTemplateName();

	public function __construct(\XF\App $app, \XF\Entity\Post $post, $actionType)
	{
		parent::__construct($app);

		$this->post = $post;
		$this->actionType = $actionType;
		$this->isApplicable = $this->isApplicable();
	}

	protected function isApplicable()
	{
		if (!in_array($this->actionType, $this->getApplicableActionTypes()))
		{
			return false;
		}

		if (!$this->post->isVisible())
		{
			return false;
		}

		return true;
	}

	public function canNotify(\XF\Entity\User $user)
	{
		if (!$this->isApplicable)
		{
			return false;
		}

		if (!isset($this->userReadDates[$user->user_id]))
		{
			// this should have a record for every user, so generally shouldn't happen
			return false;
		}

		$userReadDate = $this->userReadDates[$user->user_id];
		$post = $this->post;

		if ($user->user_id == $post->user_id || $user->isIgnoring($post->user_id))
		{
			return false;
		}

		if ($userReadDate > $post->Thread->last_post_date)
		{
			return false;
		}

		if ($this->actionType == 'reply')
		{
			$previousVisiblePost = null;
			foreach ($this->getPreviousPosts() AS $previousPost)
			{
				if (!$user->isIgnoring($previousPost->user_id))
				{
					$previousVisiblePost = $previousPost;
					break;
				}
			}

			$autoReadDate = \XF::$time - $this->app()->options()->readMarkingDataLifetime * 86400;
			if (!$previousVisiblePost || $previousVisiblePost->post_date < $autoReadDate)
			{
				// always alert
			}
			else if ($previousVisiblePost->post_date > $userReadDate)
			{
				return false;
			}
		}

		return true;
	}

	public function sendAlert(\XF\Entity\User $user)
	{
		$post = $this->post;

		return $this->basicAlert($user, $post->user_id, $post->username, 'post', $post->post_id, 'insert');
	}

	public function sendEmail(\XF\Entity\User $user)
	{
		if (!$user->email || $user->user_state != 'valid')
		{
			return false;
		}

		$post = $this->post;

		$params = [
			'post' => $post,
			'thread' => $post->Thread,
			'forum' => $post->Thread->Forum,
			'receiver' => $user
		];

		$template = $this->getWatchEmailTemplateName();

		$this->app()->mailer()->newMail()
			->setToUser($user)
			->setTemplate($template, $params)
			->queue();

		return true;
	}

	public function getDefaultNotifyData()
	{
		if (!$this->isApplicable)
		{
			return [];
		}

		return $this->getDefaultWatchNotifyData();
	}

	public function getUserData(array $userIds)
	{
		$users = parent::getUserData($userIds);
		$this->userReadDates = $this->getUserReadDates($userIds);

		return $users;
	}

	protected function getUserReadDates(array $userIds)
	{
		if (!$userIds)
		{
			return [];
		}

		$autoReadDate = \XF::$time - $this->app()->options()->readMarkingDataLifetime * 86400;
		$post = $this->post;

		$db = $this->app()->db();
		$readDates = $db->fetchPairs("
			SELECT user.user_id,
				GREATEST(
					COALESCE(thread_read.thread_read_date, 0),
					COALESCE(forum_read.forum_read_date, 0),
					?
				)
			FROM xf_user AS user
			LEFT JOIN xf_thread_read AS thread_read ON
				(thread_read.user_id = user.user_id AND thread_read.thread_id = ?)
			LEFT JOIN xf_forum_read AS forum_read ON
				(forum_read.user_id = user.user_id AND forum_read.node_id = ?)
			WHERE user.user_id IN (" . $db->quote($userIds) . ")
		", [$autoReadDate, $post->thread_id, $post->Thread->node_id]);

		foreach ($userIds AS $userId)
		{
			if (!isset($readDates[$userId]))
			{
				$readDates[$userId] = $autoReadDate;
			}
		}

		return $readDates;
	}

	protected function getPreviousPosts()
	{
		if ($this->previousPosts === null)
		{
			$autoReadDate = \XF::$time - $this->app()->options()->readMarkingDataLifetime * 86400;

			$finder = $this->app()->finder('XF:Post')
				->where('thread_id', $this->post->thread_id)
				->where('message_state', 'visible')
				->where('post_date', '<', $this->post->post_date)
				->where('post_date', '>=', $autoReadDate)
				->order('post_date', 'desc');

			$this->previousPosts = $finder->fetch(15);
		}

		return $this->previousPosts;
	}
}