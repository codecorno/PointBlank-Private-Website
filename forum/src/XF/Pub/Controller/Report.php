<?php

namespace XF\Pub\Controller;

use XF\Mvc\ParameterBag;

class Report extends AbstractController
{
	protected function preDispatchController($action, ParameterBag $params)
	{
		if (!\XF::visitor()->is_moderator)
		{
			throw $this->exception($this->noPermission());
		}
	}

	public function actionIndex(ParameterBag $params)
	{
		if ($params->report_id)
		{
			return $this->rerouteController(__CLASS__, 'view', $params);
		}

		$this->session()->reportLastRead = \XF::$time;

		$reportRepo = $this->getReportRepo();

		$openReports = $reportRepo->findReports()->fetch();

		if ($openReports->count() != $this->app->reportCounts['total'])
		{
			$reportRepo->rebuildReportCounts();
		}

		$openReports = $openReports->filterViewable();

		$closedReportsFinder = $reportRepo->findReports(['resolved', 'rejected'], time() - 86400);
		$closedReports = $closedReportsFinder->fetch();
		$closedReports = $closedReports->filterViewable();

		$viewParams = [
			'openReports' => $openReports,
			'closedReports' => $closedReports
		];
		return $this->view('XF:Report\Listing', 'report_list', $viewParams);
	}

	public function actionClosed()
	{
		$page = $this->filterPage();

		$daysInRange = 7;
		$date = new \DateTime();
		if ($page > 1)
		{
			$max = floor(time() / ($daysInRange * 86400));

			if ($page > $max)
			{
				$params = $_GET;
				$params['page'] = $max;

				$redirect = $this->router()->buildLink('reports/closed', null, $params);
				throw $this->exception($this->redirect($redirect));
			}

			$date->modify('-' . ($daysInRange * ($page - 1)) . ' days');
		}

		$maximumTimestamp = $date->format('U');
		$date->modify("-$daysInRange days");
		$minimumTimestamp = $date->format('U');
		$timeFrame = [$minimumTimestamp, $maximumTimestamp];

		$reportRepo = $this->getReportRepo();
		$reports = $reportRepo->findReports(['resolved', 'rejected'], $timeFrame)->fetch();
		$reports = $reports->filterViewable();

		$viewParams = [
			'reports' => $reports,
			'page' => $page,
			'timeFrame' => $timeFrame,
		];
		return $this->view('XF:Report\ListClosed', 'report_list_closed', $viewParams);
	}

	public function actionView(ParameterBag $params)
	{
		$visitor = \XF::visitor();
		$with = ['User', 'User.Profile', 'DraftComments|' . $visitor->user_id];

		$report = $this->assertViewableReport($params->report_id, $with);
		$handler = $report->getHandler();

		$moderators = $this->getReportRepo()->getModeratorsWhoCanHandleReport($report);

		/** @var \XF\Repository\UserAlert $userAlertRepo */
		$userAlertRepo = $this->repository('XF:UserAlert');
		$userAlertRepo->markUserAlertsReadForContent('report', $report->report_id);

		$comments = $report->getRelationFinder('Comments')->with('User')->fetch();

		$viewParams = [
			'report' => $report,
			'handler' => $handler,
			'comments' => $comments,
			'moderators' => $moderators
		];
		return $this->view('XF:Report\View', 'report_view', $viewParams);
	}

	public function actionSearch(ParameterBag $params)
	{
		$username = $this->filter('username', 'str');
		if ($username)
		{
			$user = $this->em()->findOne('XF:User', ['username' => $username]);
		}
		else
		{
			$user = $this->em()->find('XF:User', $this->filter('user_id', 'uint'));
		}
		if (!$user)
		{
			return $this->error(\XF::phrase('requested_member_not_found'), 404);
		}

		$page = $this->filterPage();
		$perPage = 50;

		$reportRepo = $this->getReportRepo();
		$reportsFinder = $reportRepo->findReports(null)
			->forContentUser($user)
			->limitByPage($page, $perPage);
		
		$total = $reportsFinder->total();
		if (!$total)
		{
			return $this->error(\XF::phrase('no_reports_were_found_for_this_users_content'));
		}
		
		$this->assertValidPage($page, $perPage, $total, 'reports/search', ['user_id' => $user->user_id]);

		$viewParams = [
			'reports' => $reportsFinder->fetch(),
			'user' => $user,
			'page' => $page,
			'perPage' => $perPage,
			'total' => $total
		];
		return $this->view('XF:Report\Search', 'report_search', $viewParams);
	}

	/**
	 * @param \XF\Entity\Report $report
	 *
	 * @return \XF\Service\Report\Commenter
	 */
	protected function setupReportComment(\XF\Entity\Report $report)
	{
		$message = $this->plugin('XF:Editor')->fromInput('message');

		/** @var \XF\Service\Report\Commenter $commenter */
		$commenter = $this->service('XF:Report\Commenter', $report);
		$commenter->setMessage($message);

		$newState = $this->filter('report_state', 'str');
		if ($newState)
		{
			$user = null;
			if ($newState == 'assigned')
			{
				if ($report->assigned_user_id)
				{
					// Unassign
					$newState = 'open';
				}
				else
				{
					$user = \XF::visitor();
				}
			}
			$commenter->setReportState($newState, $user);

			if ($this->filter('send_alert', 'bool'))
			{
				$alertComment = $this->filter('alert_comment', 'str');
				$commenter->setupClosedAlert($alertComment);
			}
		}

		return $commenter;
	}

	protected function finalizeReportComment(\XF\Service\Report\Commenter $commenter)
	{
		$commenter->sendNotifications();

		$report = $commenter->getReport();
		$report->draft_comment->delete();

		$this->session()->reportLastRead = \XF::$time;
	}

	public function actionUpdate(ParameterBag $params)
	{
		$this->assertPostOnly();

		$report = $this->assertViewableReport($params->report_id);
		$commenter = $this->setupReportComment($report);

		if (!$commenter->validate($errors))
		{
			return $this->error($errors);
		}
		$comment = $commenter->save();
		$this->finalizeReportComment($commenter);

		return $this->redirect($this->router()->buildLink('reports', $commenter->getReport()) . '#report-comment-' . $comment->report_comment_id);
	}

	public function actionReassign(ParameterBag $params)
	{
		$this->assertPostOnly();

		$report = $this->assertViewableReport($params->report_id);

		$moderators = $this->getReportRepo()->getModeratorsWhoCanHandleReport($report);

		$userId = $this->filter('user_id', 'uint');

		$commenter = $this->setupReportComment($report);

		if ($userId)
		{
			if (!isset($moderators[$userId]))
			{
				return $this->error(\XF::phrase('you_cannot_reassign_this_report_to_this_user'));
			}
			$commenter->setReportState('assigned', $moderators[$userId]->User);
		}
		else
		{
			$commenter->setReportState('open');
		}

		if ($commenter->hasSaveableChanges())
		{
			if (!$commenter->validate($errors))
			{
				return $this->error($errors);
			}

			$comment = $commenter->save();
			$this->finalizeReportComment($commenter);

			return $this->redirect($this->router()->buildLink('reports', $commenter->getReport()) . '#report-comment-' . $comment->report_comment_id);
		}
		else
		{
			// no assignment or state change
			return $this->redirect($this->router()->buildLink('reports', $commenter->getReport()));
		}
	}

	public function actionDraft(ParameterBag $params)
	{
		$report = $this->assertViewableReport($params->report_id);

		/** @var \XF\ControllerPlugin\Draft $draftPlugin */
		$draftPlugin = $this->plugin('XF:Draft');
		return $draftPlugin->actionDraftMessage($report->draft_comment);
	}

	/**
	 * @param $reportId
	 * @param array $extraWith
	 *
	 * @return \XF\Entity\Report
	 *
	 * @throws \XF\Mvc\Reply\Exception
	 */
	protected function assertViewableReport($reportId, array $extraWith = [])
	{
		/** @var \XF\Entity\Report $report */
		$report = $this->em()->find('XF:Report', $reportId, $extraWith);
		if (!$report)
		{
			throw $this->exception($this->notFound(\XF::phrase('requested_report_not_found')));
		}

		if (!$report->canView())
		{
			throw $this->exception($this->noPermission());
		}

		return $report;
	}

	/**
	 * @return \XF\Repository\Report
	 */
	protected function getReportRepo()
	{
		return $this->repository('XF:Report');
	}

	public static function getActivityDetails(array $activities)
	{
		return \XF::phrase('performing_moderation_duties');
	}
}