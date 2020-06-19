<?php

namespace XF\Spam;

use XF\Entity\SpamCleanerLog;

class Restorer
{
	protected $app;
	protected $db;

	/** @var SpamCleanerLog */
	protected $log;

	protected $errors = [];

	public function __construct(\XF\App $app, SpamCleanerLog $log)
	{
		$this->app = $app;

		$this->db = $app->db();
		$this->db->beginTransaction();

		$this->setLog($log);
	}

	public function setLog(SpamCleanerLog $log)
	{
		$this->log = $log;
	}

	public function restoreContent()
	{
		/** @var \XF\Repository\User $userRepo */
		$userRepo = $this->app->repository('XF:User');

		/** @var \XF\Repository\Spam $spamRepo */
		$spamRepo = $this->app->repository('XF:Spam');

		$log = $this->log;
		$user = $log->User ?: $userRepo->getGuestUser($log->username);

		$spamHandlers = $spamRepo->getSpamHandlers($user);
		foreach ($spamHandlers AS $contentType => $spamHandler)
		{
			if (!empty($this->log['data'][$contentType]))
			{
				if (!$spamHandler->restore($this->log['data'][$contentType], $error))
				{
					$this->logError($contentType, $error);

					$this->db->rollback();
					return;
				}
			}
		}
	}

	public function liftBan()
	{
		$log = $this->log;
		$user = $log->User;

		if ($user && !empty($log->data['user']) && $log->data['user'] == 'banned')
		{
			$userBan = $user->Ban;
			if ($userBan)
			{
				$userBan->delete();
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

		$this->updateLog();

		$db->commit();

		return true;
	}

	protected function updateLog()
	{
		$db = $this->db;

		$db->update(
			'xf_spam_cleaner_log',
			['restored_date' => time()],
			'spam_cleaner_log_id = ?',
			$this->log->spam_cleaner_log_id
		);
	}

	public function getErrors()
	{
		return $this->errors;
	}

	protected function logError($logKey, $value)
	{
		$this->errors[$logKey] = $value;
	}
}