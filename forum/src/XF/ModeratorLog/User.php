<?php

namespace XF\ModeratorLog;

use XF\Entity\ModeratorLog;
use XF\Mvc\Entity\Entity;

class User extends AbstractHandler
{
	protected function getLogActionForChange(Entity $content, $field, $newValue, $oldValue)
	{
		switch ($field)
		{
			case 'user_state':
				if ($newValue == 'valid' && $oldValue == 'moderated')
				{
					return 'approve';
				}
				break;
		}

		return false;
	}

	protected function setupLogEntityContent(ModeratorLog $log, Entity $content)
	{
		/** @var \XF\Entity\User $content */
		$log->content_user_id = $content->user_id;
		$log->content_username = $content->username;
		$log->content_title = $content->username;
		$log->content_url = \XF::app()->router('public')->buildLink('nopath:members', $content);
		$log->discussion_content_type = 'user';
		$log->discussion_content_id = $content->user_id;
	}

	public function getContentTitle(ModeratorLog $log)
	{
		return \XF::phrase('member_x', [
			'username' => $log->content_title_
		]);
	}
}