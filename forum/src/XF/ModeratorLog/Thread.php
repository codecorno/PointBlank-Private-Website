<?php

namespace XF\ModeratorLog;

use XF\Entity\ModeratorLog;
use XF\Mvc\Entity\Entity;

class Thread extends AbstractHandler
{
	public function isLoggable(Entity $content, $action, \XF\Entity\User $actor)
	{
		switch ($action)
		{
			case 'title':
			case 'prefix_id':
			case 'custom_fields':
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
			case 'custom_fields':
				return 'custom_fields_edit';

			case 'sticky':
				return $newValue ? 'stick' : 'unstick';

			case 'discussion_open':
				return $newValue ? 'unlock' : 'lock';

			case 'discussion_state':
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

			case 'title':
				return ['title', ['old' => $oldValue]];

			case 'prefix_id':
				if ($oldValue)
				{
					$old = \XF::phrase('thread_prefix.' . $oldValue)->render();
				}
				else
				{
					$old = '-';
				}
				return ['prefix', ['old' => $old]];

			case 'node_id':
				$node = \XF::em()->find('XF:Node', $oldValue);
				$oldForum = $node ? $node->title : '';
				return ['move', ['from' => $oldForum]];
		}

		return false;
	}

	protected function setupLogEntityContent(ModeratorLog $log, Entity $content)
	{
		/** @var \XF\Entity\Thread $content */
		$log->content_user_id = $content->user_id;
		$log->content_username = $content->username;
		$log->content_title = $content->title;
		$log->content_url = \XF::app()->router('public')->buildLink('nopath:threads', $content);
		$log->discussion_content_type = 'thread';
		$log->discussion_content_id = $content->thread_id;
	}

	protected function getActionPhraseParams(ModeratorLog $log)
	{
		if ($log->action == 'edit')
		{
			return ['elements' => implode(', ', array_keys($log->action_params))];
		}
		else
		{
			return parent::getActionPhraseParams($log);
		}
	}
}