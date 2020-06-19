<?php

namespace XF\ModeratorLog;

use XF\Entity\ModeratorLog;
use XF\Mvc\Entity\Entity;

class ProfilePost extends AbstractHandler
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
		$log->content_title = $content->ProfileUser->username ?: '';
		$log->content_url = \XF::app()->router('public')->buildLink('nopath:profile-posts', $content);
		$log->discussion_content_type = 'user';
		$log->discussion_content_id = $content->profile_user_id;
	}

	public function getContentTitle(ModeratorLog $log)
	{
		if ($log->content_user_id == $log->discussion_content_id)
		{
			return \XF::phrase('status_update_by_x', [
				'username' => $log->content_title_
			])->render('raw');
		}
		else
		{
			return \XF::phrase('profile_post_for_x', [
				'username' => $log->content_title_
			])->render('raw');
		}
	}
}