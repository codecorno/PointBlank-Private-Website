<?php

namespace XF\Report;

use XF\Entity\Report;
use XF\Mvc\Entity\Entity;

class ConversationMessage extends AbstractHandler
{
	protected function canActionContent(Report $report)
	{
		$visitor = \XF::visitor();
		return $visitor->hasPermission('general', 'warn');
	}

	public function setupReportEntityContent(Report $report, Entity $content)
	{
		/** @var \XF\Entity\Post $content */
		$report->content_user_id = $content->user_id;
		$report->content_info = [
			'message' => $content->message,
			'conversation_id' => $content->conversation_id,
			'conversation_title' => $content->Conversation->title,
			'user_id' => $content->user_id,
			'username' => $content->username
		];
	}

	public function getContentTitle(Report $report)
	{
		if (isset($report->content_info['conversation']['title']))
		{
			// legacy; full conversation array
			$title = $report->content_info['conversation']['title'];
		}
		else
		{
			$title = $report->content_info['conversation_title'];
		}
		return \XF::phrase('conversation_message_in_x', [
			'title' => \XF::app()->stringFormatter()->censorText($title)
		]);
	}

	public function getContentMessage(Report $report)
	{
		return $report->content_info['message'];
	}

	public function getEntityWith()
	{
		return ['Conversation', 'Conversation.Starter', 'User'];
	}
}