<?php

namespace XF\Poll;

use XF\Entity\Poll;
use XF\Mvc\Entity\Entity;

class Thread extends AbstractHandler
{
	public function canCreate(Entity $content, &$error = null)
	{
		/** @var \XF\Entity\Thread $content */

		return $content->canCreatePoll($error);
	}

	public function canEdit(Entity $content, Poll $poll, &$error = null)
	{
		/** @var \XF\Entity\Thread $content */

		$visitor = \XF::visitor();
		if (!$visitor->user_id)
		{
			return false;
		}

		if (!$content->discussion_open && !$content->canLockUnlock())
		{
			$error = \XF::phraseDeferred('you_may_not_perform_this_action_because_discussion_is_closed');
			return false;
		}

		if ($visitor->hasNodePermission($content->node_id, 'manageAnyThread'))
		{
			return true;
		}

		$nodeId = $content->node_id;

		if ($content->user_id == $visitor->user_id && $visitor->hasNodePermission($nodeId, 'editOwnPost'))
		{
			$editLimit = $visitor->hasNodePermission($nodeId, 'editOwnPostTimeLimit');
			if ($editLimit != -1 && (!$editLimit || $content->post_date < \XF::$time - 60 * $editLimit))
			{
				$error = \XF::phraseDeferred('message_edit_time_limit_expired', ['minutes' => $editLimit]);
				return false;
			}

			if (!$content->Forum || !$content->Forum->allow_posting)
			{
				$error = \XF::phraseDeferred('you_may_not_perform_this_action_because_forum_does_not_allow_posting');
				return false;
			}

			return true;
		}

		return false;
	}

	public function canAlwaysEditDetails(Entity $content, Poll $poll, &$error = null)
	{
		/** @var \XF\Entity\Thread $content */

		$visitor = \XF::visitor();
		return ($visitor->user_id && $visitor->hasNodePermission($content->node_id, 'manageAnyThread'));
	}

	public function canDelete(Entity $content, Poll $poll, &$error = null)
	{
		/** @var \XF\Entity\Thread $content */

		$visitor = \XF::visitor();
		if (!$visitor->user_id)
		{
			return false;
		}

		if ($visitor->hasNodePermission($content->node_id, 'manageAnyThread'))
		{
			return true;
		}

		if ($visitor->user_id != $content->user_id)
		{
			return false;
		}

		return ($poll->voter_count == 0);
	}

	public function canVote(Entity $content, Poll $poll, &$error = null)
	{
		/** @var \XF\Entity\Thread $content */

		$visitor = \XF::visitor();
		if (!$visitor->user_id)
		{
			return false;
		}

		if (!$content->discussion_open)
		{
			$error = \XF::phraseDeferred('you_may_not_perform_this_action_because_discussion_is_closed');
			return false;
		}

		if (!$content->Forum || !$content->Forum->allow_posting)
		{
			$error = \XF::phraseDeferred('you_may_not_perform_this_action_because_forum_does_not_allow_posting');
			return false;
		}

		return $visitor->hasNodePermission($content->node_id, 'votePoll');
	}

	public function getPollLink($action, Entity $content, array $extraParams = [])
	{
		if ($action == 'content')
		{
			return \XF::app()->router('public')->buildLink('threads', $content, $extraParams);
		}
		else
		{
			return \XF::app()->router('public')->buildLink('threads/poll/' . $action, $content, $extraParams);
		}
	}

	public function finalizeCreation(Entity $content, Poll $poll)
	{
		$content->discussion_type = 'poll';
		$content->save();
	}

	public function finalizeDeletion(Entity $content, Poll $poll)
	{
		$content->discussion_type = '';
		$content->save();
	}

	public function getEntityWith()
	{
		return ['Forum', 'User'];
	}
}