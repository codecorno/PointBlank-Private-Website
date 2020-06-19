<?php

namespace XF\Report;

use XF\Entity\Report;
use XF\Mvc\Entity\Entity;

class ProfilePost extends AbstractHandler
{
	protected function canViewContent(Report $report)
	{
		return \XF::visitor()->canViewProfilePosts();
	}

	protected function canActionContent(Report $report)
	{
		$visitor = \XF::visitor();
		return ($visitor->hasPermission('profilePost', 'editAny') || $visitor->hasPermission('profilePost', 'deleteAny'));
	}

	public function setupReportEntityContent(Report $report, Entity $content)
	{
		/** @var \XF\Entity\Post $content */
		$report->content_user_id = $content->user_id;
		$report->content_info = [
			'message' => $content->message,
			'profileUser' => [
				'user_id' => $content->ProfileUser->user_id,
				'username' => $content->ProfileUser->username
			],
			'user' => [
				'user_id' => $content->user_id,
				'username' => $content->username
			],
			'profile_post_id' => $content->profile_post_id
		];
	}

	public function getContentTitle(Report $report)
	{
		if (isset($report->content_info['user']))
		{
			if ($report->content_info['user']['user_id'] == $report->content_info['profileUser']['user_id'])
			{
				return \XF::phrase('status_update_by_x', [
					'username' => $report->content_info['user']['username']
				]);
			}
			else
			{
				return \XF::phrase('profile_post_by_x', [
					'name' => $report->content_info['user']['username']
				]);
			}
		}
		else
		{
			return \XF::phrase('profile_post_for_x', [
				'username' => $report->content_info['profile_username']
			]);
		}
	}

	public function getContentMessage(Report $report)
	{
		return $report->content_info['message'];
	}

	public function getContentLink(Report $report)
	{
		if (isset($report->content_info['user']))
		{
			$linkData = $report->content_info;
		}
		else
		{
			$linkData = ['profile_post_id' => $report->content_id];
		}

		return \XF::app()->router('public')->buildLink('canonical:profile-posts', $linkData);
	}

	public function getEntityWith()
	{
		return ['ProfileUser'];
	}
}