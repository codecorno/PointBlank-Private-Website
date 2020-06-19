<?php

namespace XF\Service\Report;

use XF\Entity\Report;
use XF\Entity\ReportComment;

class Commenter extends \XF\Service\AbstractService
{
	use \XF\Service\ValidateAndSavableTrait;

	/**
	 * @var Report
	 */
	protected $report;

	/**
	 * @var ReportComment
	 */
	protected $comment;

	/**
	 * @var CommentPreparer
	 */
	protected $commentPreparer;

	protected $alertComment;
	protected $sendAlert = false;

	public function __construct(\XF\App $app, Report $report)
	{
		parent::__construct($app);

		$this->report = $report;
		$this->comment = $report->getNewComment();
		$this->comment->addCascadedSave($this->report);
		$this->commentPreparer = $this->service('XF:Report\CommentPreparer', $this->comment);
		$this->setCommentDefaults();
	}

	public function getReport()
	{
		return $this->report;
	}

	public function getComment()
	{
		return $this->comment;
	}

	public function getCommentPreparer()
	{
		return $this->commentPreparer;
	}

	protected function setCommentDefaults()
	{
		$visitor = \XF::visitor();

		$this->commentPreparer->setUser($visitor);
		$this->comment->is_report = false;
		$this->comment->comment_date = \XF::$time;

		$this->report->last_modified_date = time();
		$this->report->last_modified_user_id = $visitor->user_id;
		$this->report->last_modified_username = $visitor->username;

		if ($this->report->isClosed())
		{
			$this->setReportState('open');
		}
	}

	public function setReportState($newState = null, \XF\Entity\User $assignedUser = null)
	{
		$oldState = $this->report->getExistingValue('report_state');
		if ($newState)
		{
			$this->report->report_state = $newState;
			if ($assignedUser && $newState == 'assigned')
			{
				$this->report->assigned_user_id = $assignedUser->user_id;
			}

			if ($newState == 'open')
			{
				$this->report->assigned_user_id = 0;
			}
		}

		if ($newState && ($newState != $oldState || $this->report->isChanged('assigned_user_id')))
		{
			$this->comment->state_change = $newState;
		}
	}

	public function setupClosedAlert($alertComment)
	{
		$this->alertComment = $alertComment;
		$this->sendAlert = true;
	}

	public function setMessage($message, $format = true)
	{
		return $this->commentPreparer->setMessage($message, $format);
	}

	protected function finalSetup()
	{
		$comment = $this->comment;

		if ($this->sendAlert && $comment->isClosureComment())
		{
			// Include resolution/rejection alert inside the comment.

			$appendPhrase = \XF::phrase('report_resolution_alert_comment_append', [
				'alert' => $this->alertComment ?: '-'
			]);
			$message = trim($comment->message . "\n\n" . $appendPhrase->render('raw'));

			$comment->set('message', $message, ['forceSet' => true]);
		}
	}
	
	public function hasSaveableChanges()
	{
		return $this->comment->hasSaveableChanges();
	}

	protected function _validate()
	{
		$this->finalSetup();

		$this->comment->preSave();
		return $this->comment->getErrors();
	}

	protected function _save()
	{
		$comment = $this->comment;
		$report = $this->report;

		$db = $this->db();
		$db->beginTransaction();

		// This will save the report, also.
		$comment->save(true, false);

		if ($comment->message)
		{
			$report->fastUpdate('comment_count', $report->comment_count + 1);
		}

		$db->commit();

		return $comment;
	}

	public function sendNotifications()
	{
		// TODO: send quote notifications, assigned notifications?

		if ($this->comment->isClosureComment() && $this->sendAlert)
		{
			/** @var \XF\Service\Report\ClosureNotifier $closureNotifier */
			$closureNotifier = $this->service('XF:Report\ClosureNotifier', $this->report);
			$closureNotifier->setAlertComment($this->alertComment);
			$closureNotifier->notify();
		}

		/** @var \XF\Service\Report\Notifier $notifier */
		$notifier = $this->service('XF:Report\Notifier', $this->report, $this->comment);
		$notifier->setNotifyMentioned($this->commentPreparer->getMentionedUserIds());
		$notifier->notify();
	}
}