<?php

namespace XF\Notifier\Post;

class ThreadWatch extends AbstractWatch
{
	protected function getApplicableActionTypes()
	{
		return ['reply'];
	}

	public function getDefaultWatchNotifyData()
	{
		$post = $this->post;

		if ($post->isFirstPost())
		{
			return [];
		}

		$finder = $this->app()->finder('XF:ThreadWatch');

		$finder->where('thread_id', $post->thread_id)
			->where('User.user_state', '=', 'valid')
			->where('User.is_banned', '=', 0);

		$activeLimit = $this->app()->options()->watchAlertActiveOnly;
		if (!empty($activeLimit['enabled']))
		{
			$finder->where('User.last_activity', '>=', \XF::$time - 86400 * $activeLimit['days']);
		}

		$notifyData = [];
		foreach ($finder->fetchColumns(['user_id', 'email_subscribe']) AS $watch)
		{
			$notifyData[$watch['user_id']] = [
				'alert' => true,
				'email' => (bool)$watch['email_subscribe']
			];
		}

		return $notifyData;
	}

	protected function getWatchEmailTemplateName()
	{
		return 'watched_thread_reply';
	}
}