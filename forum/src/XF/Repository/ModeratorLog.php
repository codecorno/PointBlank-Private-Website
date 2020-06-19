<?php

namespace XF\Repository;

use XF\Mvc\Entity\Finder;
use XF\Mvc\Entity\Repository;
use XF\Moderator\AbstractModerator;

class ModeratorLog extends Repository
{
	/**
	 * @return Finder
	 */
	public function findLogsForList()
	{
		return $this->finder('XF:ModeratorLog')
			->with('User')
			->setDefaultOrder('log_date', 'DESC');
	}

	/**
	 * @return Finder
	 */
	public function findLogsForDiscussion($discussionContentType, $discussionId)
	{
		return $this->finder('XF:ModeratorLog')
			->where(['discussion_content_type' => $discussionContentType, 'discussion_content_id' => $discussionId])
			->with('User')
			->setDefaultOrder('log_date', 'DESC');
	}

	public function getUsersInLog()
	{
		return $this->db()->fetchPairs("
			SELECT user.user_id, user.username
			FROM (
				SELECT DISTINCT user_id FROM xf_moderator_log
			) AS log
			INNER JOIN xf_user AS user ON (log.user_id = user.user_id)
			ORDER BY user.username
		");
	}

	public function pruneModeratorLogs($cutOff = null)
	{
		if ($cutOff === null)
		{
			$logLength = $this->options()->moderatorLogLength;
			if (!$logLength)
			{
				return 0;
			}

			$cutOff = \XF::$time - 86400 * $logLength;
		}

		return $this->db()->delete('xf_moderator_log', 'log_date < ?', $cutOff);
	}

	/**
	 * @return \XF\ModeratorLog\AbstractHandler[]
	 */
	public function getModeratorLogHandlers()
	{
		$handlers = [];

		foreach (\XF::app()->getContentTypeField('moderator_log_handler_class') AS $contentType => $handlerClass)
		{
			if (class_exists($handlerClass))
			{
				$handlerClass = \XF::extendClass($handlerClass);
				$handlers[$contentType] = new $handlerClass($contentType);
			}
		}

		return $handlers;
	}

	/**
	 * @param string $type
	 *
	 * @return \XF\ModeratorLog\AbstractHandler|null
	 */
	public function getModeratorLogHandler($type)
	{
		$handlerClass = \XF::app()->getContentTypeFieldValue($type, 'moderator_log_handler_class');
		if (!$handlerClass)
		{
			return null;
		}

		if (!class_exists($handlerClass))
		{
			return null;
		}

		$handlerClass = \XF::extendClass($handlerClass);
		return new $handlerClass($type);
	}
}