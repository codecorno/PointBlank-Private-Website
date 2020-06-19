<?php

namespace XF\Notifier\Post;

class ForumWatch extends AbstractWatch
{
	protected function getApplicableActionTypes()
	{
		return ['reply', 'thread'];
	}

	public function getDefaultWatchNotifyData()
	{
		$post = $this->post;

		$finder = $this->app()->finder('XF:ForumWatch');

		$finder->where('node_id', $post->Thread->node_id)
			->where('User.user_state', '=', 'valid')
			->where('User.is_banned', '=', 0)
			->whereOr(
				['send_alert', '>', 0],
				['send_email', '>', 0]
			);

		if ($this->actionType == 'reply')
		{
			$finder->where('notify_on', 'message');
		}
		else
		{
			$finder->where('notify_on', ['thread', 'message']);
		}

		$activeLimit = $this->app()->options()->watchAlertActiveOnly;
		if (!empty($activeLimit['enabled']))
		{
			$finder->where('User.last_activity', '>=', \XF::$time - 86400 * $activeLimit['days']);
		}

		$notifyData = [];
		foreach ($finder->fetchColumns(['user_id', 'send_alert', 'send_email']) AS $watch)
		{
			$notifyData[$watch['user_id']] = [
				'alert' => (bool)$watch['send_alert'],
				'email' => (bool)$watch['send_email']
			];
		}

		return $notifyData;
	}

	public function sendAlert(\XF\Entity\User $user)
	{
		$post = $this->post;

		return $this->basicAlert($user, $post->user_id, $post->username, 'post', $post->post_id, 'forumwatch_insert');
	}

	protected function getWatchEmailTemplateName()
	{
		return 'watched_forum_' . ($this->actionType == 'thread' ? 'thread' : 'reply');
	}
}