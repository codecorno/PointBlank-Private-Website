<?php

namespace XF\EmailStop;

class Forum extends AbstractHandler
{
	public function getStopOneText(\XF\Entity\User $user, $contentId)
	{
		/** @var \XF\Entity\Forum|null $forum */
		$forum = \XF::em()->find('XF:Forum', $contentId);
		$canView = \XF::asVisitor(
			$user,
			function() use ($forum) { return $forum && $forum->canView(); }
		);

		if ($canView)
		{
			return \XF::phrase('stop_notification_emails_from_x', ['title' => $forum->title]);
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
		/** @var \XF\Entity\Forum $forum */
		$forum = \XF::em()->find('XF:Forum', $contentId);
		if ($forum)
		{
			/** @var \XF\Repository\ForumWatch $forumWatchRepo */
			$forumWatchRepo = \XF::repository('XF:ForumWatch');
			$forumWatchRepo->setWatchState($forum, $user, null, null, false);
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