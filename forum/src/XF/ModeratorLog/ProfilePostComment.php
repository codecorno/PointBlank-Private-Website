<?php

namespace XF\ModeratorLog;

use XF\Entity\ModeratorLog;
use XF\Mvc\Entity\Entity;

class ProfilePostComment extends AbstractHandler
{
	public function isLoggable(Entity $content, $action, \XF\Entity\User $actor)
	{
		switch ($action)
		{
			case 'edit':
				if ($actor->user_id == $content->user_id)
				{
					return false;
				}
		}

		return parent::isLoggable($content, $action, $actor);
	}

	protected function getLogActionForChange(Entity $content, $field, $newValue, $oldValue)
	{
		switch ($field)
		{
			case 'message':
				return 'edit';

			case 'message_state':
				if ($newValue == 'visible' && $oldValue == 'moderated')
				{
					return 'approve';
				}
				else if ($newValue == 'visible' && $oldValue == 'deleted')
				{
					return 'undelete';
				}
				else if ($newValue == 'deleted')
				{
					$reason = $content->DeletionLog ? $content->DeletionLog->delete_reason : '';
					return ['delete_soft', ['reason' => $reason]];
				}
				else if ($newValue == 'moderated')
				{
					return 'unapprove';
				}
				break;
		}

		return false;
	}

	protected function setupLogEntityContent(ModeratorLog $log, Entity $content)
	{
		/** @var \XF\Entity\Post $content */
		$log->content_user_id = $content->user_id;
		$log->content_username = $content->username;
		$log->content_title = '';
		$log->content_url = \XF::app()->router('public')->buildLink('nopath:profile-posts/comments', $content);
		$log->discussion_content_type = 'profile_post';
		$log->discussion_content_id = $content->profile_post_id;
	}

	public function getContentTitle(ModeratorLog $log)
	{
		return \XF::phrase('profile_post_comment_by_x', [
			'username' => $log->content_username
		])->render('raw');
	}
}