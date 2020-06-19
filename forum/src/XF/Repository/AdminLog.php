<?php

namespace XF\Repository;

use XF\Mvc\Entity\Finder;
use XF\Mvc\Entity\Repository;
use XF\Moderator\AbstractModerator;

class AdminLog extends Repository
{
	/**
	 * @return Finder
	 */
	public function findLogsForList()
	{
		return $this->finder('XF:AdminLog')
			->with('User')
			->setDefaultOrder('request_date', 'DESC');
	}

	public function getUsersInLog()
	{
		return $this->db()->fetchPairs("
			SELECT user.user_id, user.username
			FROM (
				SELECT DISTINCT user_id FROM xf_admin_log
			) AS log
			INNER JOIN xf_user AS user ON (log.user_id = user.user_id)
			ORDER BY user.username
		");
	}

	public function logAdminRequest($userId, $url, array $data, $ipAddress)
	{
		$userId = intval($userId);
		if (!$userId)
		{
			return null;
		}

		$ipAddress = \XF\Util\Ip::convertIpStringToBinary($ipAddress);

		$this->db()->insert('xf_admin_log', [
			'user_id' => $userId,
			'ip_address' => $ipAddress,
			'request_date' => \XF::$time,
			'request_url' => $url,
			'request_data' => json_encode($data)
		]);

		return $this->db()->lastInsertId();
	}

	public function pruneAdminLogs($cutOff = null)
	{
		if ($cutOff === null)
		{
			$logLength = $this->app()->config()['adminLogLength'];
			if (!$logLength)
			{
				return 0;
			}

			$cutOff = \XF::$time - 86400 * $logLength;
		}

		return $this->db()->delete('xf_admin_log', 'request_date < ?', $cutOff);
	}
}