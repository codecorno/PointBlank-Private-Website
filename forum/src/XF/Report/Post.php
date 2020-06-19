<?php

namespace XF\Report;

use XF\Entity\Report;
use XF\Mvc\Entity\Entity;

class Post extends AbstractHandler
{
	protected function canViewContent(Report $report)
	{
		return \XF::visitor()->hasNodePermission($report->content_info['node_id'], 'view');
	}

	protected function canActionContent(Report $report)
	{
		$visitor = \XF::visitor();
		$nodeId = $report->content_info['node_id'];
		return ($visitor->hasNodePermission($nodeId, 'editAnyPost') || $visitor->hasNodePermission($nodeId, 'deleteAnyPost'));
	}

	public function setupReportEntityContent(Report $report, Entity $content)
	{
		if (!empty($content->Thread->prefix_id) && $content->Thread->Prefix)
		{
			/** @var \XF\Entity\ThreadPrefix $prefix */
			$prefix = $content->Thread->Prefix;
			$prefixPhrase = \XF::phrase($prefix->getPhraseName());
			$prefixPhrase = $prefixPhrase->render();

			$threadTitle = $prefixPhrase . ' - ' . $content->Thread->title;
		}
		else
		{
			$threadTitle = $content->Thread->title;
		}

		/** @var \XF\Entity\Post $content */
		$report->content_user_id = $content->user_id;
		$report->content_info = [
			'message' => $content->message,
			'node_id' => $content->Thread->Forum->Node->node_id,
			'node_name' => $content->Thread->Forum->Node->node_name,
			'node_title' => $content->Thread->Forum->Node->title,
			'post_id' => $content->post_id,
			'thread_id' => $content->thread_id,
			'thread_title' => $threadTitle,
			'user_id' => $content->user_id,
			'username' => $content->username
		];
	}

	public function getContentTitle(Report $report)
	{
		return \XF::phrase('post_in_thread_x', [
			'title' => \XF::app()->stringFormatter()->censorText($report->content_info['thread_title'])
		]);
	}

	public function getContentMessage(Report $report)
	{
		return $report->content_info['message'];
	}

	public function getContentLink(Report $report)
	{
		if (!empty($report->content_info['post_id']))
		{
			$linkData = $report->content_info;
		}
		else
		{
			$linkData = ['post_id' => $report->content_id];
		}

		return \XF::app()->router('public')->buildLink('canonical:posts', $linkData);
	}

	public function getEntityWith()
	{
		return ['Thread', 'Thread.Forum', 'User'];
	}
}