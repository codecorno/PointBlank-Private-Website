<?php

namespace XF\Report;

use XF\Entity\Report;
use XF\Mvc\Entity\Entity;

class User extends AbstractHandler
{
	protected function canActionContent(Report $report)
	{
		$visitor = \XF::visitor();
		return ($visitor->hasPermission('general', 'warn') && $visitor->hasPermission('general', 'editBasicProfile'));
	}

	public function setupReportEntityContent(Report $report, Entity $content)
	{
		/** @var \XF\Entity\User $content */
		$report->content_user_id = $content->user_id;
		$report->content_info = [
			'user_id' => $content->user_id,
			'username' => $content->username
		];
	}

	public function getContentTitle(Report $report)
	{
		$content = $report->content_info;

		if (isset($content['username']))
		{
			$name = $content['username'];
		}
		else if (isset($content['user']['username']))
		{
			$name = $content['user']['username'];
		}
		else
		{
			$name = \XF::phrase('guest');
		}

		return \XF::phrase('member_x', [
			'username' => $name
		]);
	}

	public function getContentMessage(Report $report)
	{
		return $this->getContentTitle($report);
	}

	public function getContentLink(Report $report)
	{
		if (!empty($report->content_info['user_id']))
		{
			$linkData = $report->content_info;
		}
		else
		{
			$linkData = ['user_id' => $report->content_id];
		}

		return \XF::app()->router('public')->buildLink('canonical:members', $linkData);
	}

	public function getTemplateName()
	{
		return null;
	}
}