<?php

namespace XF\Spam;

use XF\Entity\User;

class Cleaner
{
	protected $app;
	protected $db;

	/** @var User */
	protected $user;

	protected $log = [];
	protected $errors = [];

	public function __construct(\XF\App $app, User $user)
	{
		$this->app = $app;

		$this->db = $app->db();
		$this->db->beginTransaction();

		$this->setUser($user);
	}

	public function setUser(User $user)
	{
		$this->user = $user;
	}

	public function isAlreadyCleaned()
	{
		$db = $this->db;

		return boolval($db->fetchOne('
			SELECT COUNT(*)
			FROM xf_spam_cleaner_log
			WHERE user_id = ?
			AND restored_date = 0
		', $this->user->user_id));
	}

	public function banUser()
	{
		$this->log('user', 'banned');

		$user = $this->user;

		$reason = \XF::phrase('spam_cleaner_ban_reason');
		$reason = $reason->render();

		/** @var \XF\Repository\Banning $banningRepo */
		$banningRepo = $this->app->repository('XF:Banning');

		$success = $banningRepo->banUser($user, 0, $reason, $error);
		if (!$success)
		{
			$this->logError('ban', $error);
			return;
		}

		if ($user->avatar_date > 0 || $user->gravatar)
		{
			/** @var \XF\Service\User\Avatar $avatarService */
			$avatarService = $this->app->service('XF:User\Avatar', $user);
			$avatarService->logIp(false);
			$avatarService->deleteAvatar();
		}

		$this->submitData();
	}

	public function submitData()
	{
		$submitter = $this->app->container('spam.userSubmitter');
		$submitter->submit($this->user);
	}

	protected function getDefaultActions()
	{
		return [
			'action_threads' => false,
			'delete_messages' => false,
			'delete_conversations' => false,
			'ban_user' => false,
			'check_ips' => false
		];
	}

	protected function prepareActions(array $actions)
	{
		return array_replace($this->getDefaultActions(), $actions);
	}

	public function cleanUp(array $actions)
	{
		$actions = $this->prepareActions($actions);

		if ($actions['ban_user'])
		{
			$this->banUser();
		}

		$this->cleanUpContent($actions);
	}

	public function cleanUpContent(array $actions)
	{
		$actions = $this->prepareActions($actions);

		/** @var \XF\Repository\Spam $spamRepo */
		$spamRepo = $this->app->repository('XF:Spam');

		$spamHandlers = $spamRepo->getSpamHandlers($this->user);
		foreach ($spamHandlers AS $contentType => $spamHandler)
		{
			if ($spamHandler->canCleanUp($actions))
			{
				if (!$spamHandler->cleanUp($this->log, $error))
				{
					$this->logError($contentType, $error);

					$this->db->rollback();
					return;
				}
			}
		}

		if ($actions['delete_messages'])
		{
			$reports = $this->app->finder('XF:Report')->where('content_user_id', $this->user->user_id);
			foreach ($reports->fetch() AS $report)
			{
				$report->report_state = 'resolved';
				$report->save();
			}
		}
	}

	public function finalize()
	{
		$db = $this->db;

		if (count($this->errors))
		{
			$db->rollback();
			return false;
		}

		$this->user->save();
		$this->writeLog();

		$db->commit();

		return true;
	}

	protected function writeLog()
	{
		$db = $this->db;

		$user = $this->user;
		$visitor = \XF::visitor();

		// log progress
		$db->insert('xf_spam_cleaner_log', [
			'user_id' => $user->user_id,
			'username' => $user->username,
			'applying_user_id' => $visitor->user_id,
			'applying_username' => $visitor->username,
			'application_date' => time(),
			'data' => (count($this->log) ? json_encode($this->log) : '')
		]);

		$this->app->logger()->logModeratorAction('user', $user, 'spam_clean');
	}

	public function getErrors()
	{
		return $this->errors;
	}

	protected function log($logKey, $value)
	{
		$this->log[$logKey] = $value;
	}

	protected function logError($logKey, $value)
	{
		$this->errors[$logKey] = $value;
	}
}