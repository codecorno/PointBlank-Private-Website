<?php

namespace XF\Service\Report;

use XF\Entity\Report;
use XF\Entity\ReportComment;

class ClosureNotifier extends \XF\Service\AbstractService
{
	/**
	 * @var Report
	 */
	protected $report;

	protected $notifyUserIds = [];

	protected $alertComment = '';
	protected $alertType = null;

	public function __construct(\XF\App $app, Report $report, array $notifyUserIds = null)
	{
		parent::__construct($app);
		$this->report = $report;
		$this->alertType = $this->report->report_state;
		$this->setNotifyUserIds($notifyUserIds);
	}

	public function getReport()
	{
		return $this->report;
	}

	public function setNotifyUserIds(array $notifyUserIds = null)
	{
		if ($notifyUserIds === null)
		{
			$notifyUserIds = $this->determineNotifiableUserIds();
		}

		$this->notifyUserIds = $notifyUserIds;
	}

	public function getNotifyUserIds()
	{
		return $this->notifyUserIds;
	}

	public function setAlertComment($comment)
	{
		$this->alertComment = $comment;
	}

	public function getAlertComment()
	{
		return $this->alertComment;
	}

	public function setAlertType($type)
	{
		$this->alertType = $type;
	}

	public function getAlertType()
	{
		return $this->alertType;
	}

	public function notify()
	{
		$users = $this->app->em()->findByIds('XF:User', $this->notifyUserIds, ['Profile', 'Option']);
		foreach ($users AS $user)
		{
			$this->sendClosureNotification($user);
		}
		$this->notifyUserIds = [];
	}

	protected function sendClosureNotification(\XF\Entity\User $user)
	{
		$report = $this->report;

		$title = $report->title;
		if ($title instanceof \XF\Phrase)
		{
			$title = $title->render('raw');
		}
		$link = $report->link;

		/** @var \XF\Repository\UserAlert $alertRepo */
		$alertRepo = $this->repository('XF:UserAlert');
		$alertSent = $alertRepo->alertFromUser(
			$user, null,
			'user', $user->user_id,
			"report_{$this->alertType}",
			[
				'comment' => $this->alertComment,
				'title' => $title,
				'link' => $link
			]
		);

		return $alertSent;
	}

	public function determineNotifiableUserIds()
	{
		$reportId = $this->report->report_id;
		$db = $this->db();

		$lastOpenDate = $db->fetchOne("
			SELECT comment_date
			FROM xf_report_comment
			WHERE report_id = ?
				AND state_change = 'open'
			ORDER BY comment_date DESC
			LIMIT 1
		", $reportId);
		$userIds = $db->fetchAllColumn("
			SELECT user_id
			FROM xf_report_comment
			WHERE report_id = ?
				AND comment_date >= ?
				AND is_report = 1
		", [$reportId, $lastOpenDate ?: 0]);

		return array_unique($userIds);
	}
}