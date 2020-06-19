<?php

namespace XF\Repository;

use XF\Mvc\Entity\Finder;
use XF\Mvc\Entity\Repository;

class Spam extends Repository
{
	/**
	 * @return Finder
	 */
	public function findSpamCleanerLogsForList()
	{
		return $this->finder('XF:SpamCleanerLog')
			->with('User')
			->setDefaultOrder('application_date', 'DESC');
	}

	/**
	 * @return Finder
	 */
	public function findSpamTriggerLogsForList()
	{
		return $this->finder('XF:SpamTriggerLog')
			->with('User')
			->setDefaultOrder('log_date', 'DESC');
	}

	/**
	 * @return \XF\Finder\SpamTriggerLog
	 */
	public function findSpamTriggerLogs()
	{
		return $this->finder('XF:SpamTriggerLog')
			->with('User')
			->order('log_date', 'DESC');
	}

	/**
	 * @return \XF\Spam\Cleaner\AbstractHandler[]
	 */
	public function getSpamHandlers(\XF\Entity\User $user)
	{
		$handlers = [];

		foreach (\XF::app()->getContentTypeField('spam_handler_class') AS $contentType => $handlerClass)
		{
			if (class_exists($handlerClass))
			{
				$handlerClass = \XF::extendClass($handlerClass);
				$handlers[$contentType] = new $handlerClass($user);
			}
		}

		return $handlers;
	}

	public function cleanUpRegistrationResultCache($date = null)
	{
		if ($date === null)
		{
			$date = time();
		}

		$this->db()->delete('xf_registration_spam_cache', 'timeout < ?', $date);
	}

	public function cleanupContentSpamCheck($cutOff = null)
	{
		if ($cutOff === null)
		{
			$cutOff = time() - 14 * 86400;
		}

		$this->db()->delete('xf_content_spam_cache', 'insert_date < ?', $cutOff);
	}

	public function cleanupSpamTriggerLog($cutOff = null)
	{
		if ($cutOff === null)
		{
			$cutOff = time() - 30 * 86400;
		}

		$this->db()->delete('xf_spam_trigger_log', 'log_date < ?', $cutOff);
	}
}