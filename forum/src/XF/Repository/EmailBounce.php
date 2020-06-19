<?php

namespace XF\Repository;

use XF\Mvc\Entity\Finder;
use XF\Mvc\Entity\Repository;

class EmailBounce extends Repository
{
	/**
	 * @return Finder
	 */
	public function findEmailBounceLogsForList()
	{
		return $this->finder('XF:EmailBounceLog')
			->with('User')
			->setDefaultOrder('log_date', 'DESC');
	}

	public function insertSoftBounceLogEntry($userId, $bounceDate)
	{
		$this->db()->insert('xf_email_bounce_soft', [
			'user_id' => $userId,
			'bounce_date' => gmdate('Y-m-d', $bounceDate),
			'bounce_total' => 1
		], false, 'bounce_total = bounce_total + 1');
	}

	public function countRecentSoftBounces($userId, $numberOfDays)
	{
		$cutOffTimestamp = \XF::$time - intval($numberOfDays) * 86400;
		$cutOff = gmdate('Y-m-d', $cutOffTimestamp);

		$result = $this->db()->fetchRow("
			SELECT COUNT(DISTINCT bounce_date) AS unique_days,
				DATEDIFF(MAX(bounce_date), MIN(bounce_date)) AS days_between,
				SUM(bounce_total) AS bounce_total
			FROM xf_email_bounce_soft
			WHERE user_id = ?
				AND bounce_date > ?
		", [$userId, $cutOff]);

		if (!$result || !$result['unique_days'])
		{
			return [
				'unique_days' => 0,
				'days_between' => 0,
				'bounce_total' => 0
			];
		}
		else
		{
			return $result;
		}
	}

	public function pruneEmailBounceLogs($cutOff = null)
	{
		if ($cutOff === null)
		{
			$cutOff = \XF::$time - 86400 * 30;
		}

		return $this->db()->delete('xf_email_bounce_log', 'log_date < ?', $cutOff);
	}

	public function pruneSoftBounceHistory($cutOff = null)
	{
		if ($cutOff === null)
		{
			$cutOff = \XF::$time - 86400 * 30;
		}

		$date = new \DateTime("@$cutOff", new \DateTimeZone('UTC'));

		return $this->db()->delete('xf_email_bounce_soft', 'bounce_date < ?', $date->format('Y-m-d'));
	}
}