<?php

namespace XF\EmailStop;

class Thread extends AbstractHandler
{
	public function getStopOneText(\XF\Entity\User $user, $contentId)
	{
		/** @var \XF\Entity\Thread|null $thread */
		$thread = \XF::em()->find('XF:Thread', $contentId);
		$canView = \XF::asVisitor(
			$user,
			function() use ($thread) { return $thread && $thread->canView(); }
		);

		if ($canView)
		{
			return \XF::phrase('stop_notification_emails_from_x', ['title' => $thread->title]);
		}
		else
		{
			return null;
		}
	}

	public function getStopAllText(\XF\Entity\User $user)
	{
		return \XF::phrase('stop_notification_emails_from_all_threads');
	}

	public function stopOne(\XF\Entity\User $user, $contentId)
	{
		/** @var \XF\Entity\Thread $thread */
		$thread = \XF::em()->find('XF:Thread', $contentId);
		if ($thread)
		{
			/** @var \XF\Repository\ThreadWatch $threadWatchRepo */
			$threadWatchRepo = \XF::repository('XF:ThreadWatch');
			$threadWatchRepo->setWatchState($thread, $user, 'no_email');
		}
	}

	public function stopAll(\XF\Entity\User $user)
	{
		// Note that we stop all thread and forum notifications here, as the distinction of the source is unlikely
		// to be clear and they've chosen to stop all emails of this type.

		/** @var \XF\Repository\ThreadWatch $threadWatchRepo */
		$threadWatchRepo = \XF::repository('XF:ThreadWatch');
		$threadWatchRepo->setWatchStateForAll($user, 'no_email');

		/** @var \XF\Repository\ForumWatch $forumWatchRepo */
		$forumWatchRepo = \XF::repository('XF:ForumWatch');
		$forumWatchRepo->setWatchStateForAll($user, 'no_email');
	}
}