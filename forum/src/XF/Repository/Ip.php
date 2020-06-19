<?php

namespace XF\Repository;

use XF\Mvc\Entity\Finder;
use XF\Mvc\Entity\Repository;

class Ip extends Repository
{
	/**
	 * Logs the specified IP. This will return null if the IP couldn't be logged for any reason.
	 *
	 * @param integer $userId
	 * @param string $ip String IP representation; binary version also supported
	 * @param string $contentType
	 * @param int $contentId
	 * @param string $action
	 *
	 * @return null|\XF\Entity\Ip If the insert fails (generally for an invalid IP address), null.
	 */
	public function logIp($userId, $ip, $contentType, $contentId, $action = '')
	{
		$entity = $this->em->create('XF:Ip');
		$entity->user_id = $userId;
		$entity->ip = $ip;
		$entity->content_type = $contentType;
		$entity->content_id = $contentId;
		$entity->action = $action;

		if ($entity->save(false))
		{
			return $entity;
		}
		else
		{
			return null;
		}
	}

	public function logCookieLoginIfNeeded($userId, $ip)
	{
		$userId = intval($userId);
		if (!$userId)
		{
			return null;
		}

		$binaryIp = \XF\Util\Ip::convertIpStringToBinary($ip);
		if ($binaryIp === false)
		{
			return null;
		}

		// only log this if we haven't seen this IP in the last 6 hours
		$cutOff = \XF::$time - 6*3600;
		$recentLog = $this->db()->fetchOne("
			SELECT 1
			FROM xf_ip
			WHERE user_id = ?
				AND log_date > ?
				AND ip = ?
			LIMIT 1
		", [$userId, $cutOff, $binaryIp]);
		if ($recentLog)
		{
			return null;
		}

		return $this->logIp($userId, $ip, 'user', $userId, 'cookie_login');
	}

	public function pruneIps($cutOff = null)
	{
		if ($cutOff === null)
		{
			if (!$this->options()->ipLogCleanUp['enabled'])
			{
				return 0;
			}

			$cutOff = \XF::$time - 86400 * $this->options()->ipLogCleanUp['delay'];
		}

		return $this->db()->delete('xf_ip', 'log_date < ?', $cutOff);
	}

	public function getLoggedIp($contentType, $contentId, $action, $userId = null)
	{
		$params = [$contentType, $contentId, $action];
		if ($userId !== null)
		{
			$params[] = $userId;
		}

		return $this->db()->fetchOne("
			SELECT ip
			FROM xf_ip
			WHERE content_type = ?
				AND content_id = ?
				AND action = ?
				" . ($userId !== null ? 'AND user_id = ?' : '') . "
			ORDER BY log_date DESC
			LIMIT 1
		", $params);
	}

	public function getSharedIpUsers($userId, $logDays)
	{
		$db = $this->db();

		if ($userId instanceof \XF\Entity\User)
		{
			$userId = $userId->user_id;
		}

		$cutOff = \XF::$time - $logDays * 86400;

		// written this way due to mysql's ridiculous sub-query performance
		$recentIps = $db->fetchAllColumn("
			SELECT DISTINCT ip
			FROM xf_ip
			WHERE user_id = ?
				AND log_date > ?
			LIMIT 500
		", [$userId, $cutOff]);
		if (!$recentIps)
		{
			return [];
		}

		$ipLogs = $db->fetchAll('
			SELECT user_id,
				ip,
				MIN(log_date) AS first_date,
				MAX(log_date) AS last_date,
				COUNT(*) AS total
			FROM xf_ip
			WHERE ip IN (' . $db->quote($recentIps) . ')
				AND user_id <> ?
				AND user_id > 0
				AND log_date > ?
			GROUP BY user_id, ip
			LIMIT 1000
		', [$userId, $cutOff]);

		$userIpLogs = [];
		foreach ($ipLogs AS $ipLog)
		{
			$userIpLogs[$ipLog['user_id']][$ipLog['ip']] = [
				'ip' => $ipLog['ip'],
				'first_date' => $ipLog['first_date'],
				'last_date' => $ipLog['last_date'],
				'total' => $ipLog['total']
			];
		}

		if (!$userIpLogs)
		{
			return [];
		}

		$users = $this->em->findByIds('XF:User', array_keys($userIpLogs));
		$output = [];

		foreach ($users AS $user)
		{
			$output[$user->user_id] = [
				'user_id' => $user->user_id,
				'user' => $user,
				'ips' => $userIpLogs[$user->user_id]
			];
		}

		return $output;
	}

	public function getUsersByIpRange($lowerBound, $upperBound)
	{
		$ips = $this->db()->fetchAllKeyed("
			SELECT user_id,
				GROUP_CONCAT(DISTINCT ip ORDER BY ip SEPARATOR '  ') AS ips,
				MIN(log_date) AS first_date,
				MAX(log_date) AS last_date,
				COUNT(*) AS total
			FROM xf_ip
			WHERE ip >= ? AND ip <= ? AND LENGTH(ip) = ?
			GROUP BY user_id
		", 'user_id', [$lowerBound, $upperBound, strlen($lowerBound)]);
		if (!$ips)
		{
			return [];
		}

		$userIds = array_column($ips, 'user_id');
		$userIds = array_unique($userIds);

		$userFinder = $this->finder('XF:User')
			->where('user_id', $userIds)
			->order('username');

		$users = $userFinder->fetch();
		$output = [];
		foreach ($users AS $user)
		{
			$ipInfo = $ips[$user->user_id];

			$matchIps = explode('  ', $ipInfo['ips']);

			$output[$user->user_id] = [
				'user_id' => $ipInfo['user_id'],
				'ips' => $matchIps,
				'ip_total' => count($matchIps),
				'first_date' => $ipInfo['first_date'],
				'last_date' => $ipInfo['last_date'],
				'total' => $ipInfo['total'],
				'user' => $user
			];
		}

		return $output;
	}

	public function getUsersByIp($baseIp)
	{
		$ip = \XF\Util\Ip::convertIpStringToBinary($baseIp);
		if ($ip === false)
		{
			$baseIp = preg_replace('/[^\x20-\x7F]/', '?', $baseIp);
			throw new \InvalidArgumentException("Cannot convert IP '$baseIp' to binary");
		}

		$ips = $this->db()->fetchAllKeyed("
			SELECT user_id,
				ip,
				MIN(log_date) AS first_date,
				MAX(log_date) AS last_date,
				COUNT(*) AS total
			FROM xf_ip
			WHERE ip = ?
			GROUP BY user_id
		", 'user_id', $ip);
		if (!$ips)
		{
			return [];
		}

		$userIds = array_column($ips, 'user_id');
		$userIds = array_unique($userIds);

		$userFinder = $this->finder('XF:User')
			->where('user_id', $userIds)
			->order('username');

		$users = $userFinder->fetch();
		$output = [];
		foreach ($users AS $user)
		{
			$ipInfo = $ips[$user->user_id];

			$output[$user->user_id] = [
				'user_id' => $ipInfo['user_id'],
				'ips' => [$ipInfo['ip']],
				'ip_total' => 1,
				'first_date' => $ipInfo['first_date'],
				'last_date' => $ipInfo['last_date'],
				'total' => $ipInfo['total'],
				'user' => $user
			];
		}

		return $output;
	}

	public function getIpsByUser($userId, $limit = 150, $offset = 0)
	{
		if ($userId instanceof \XF\Entity\User)
		{
			$userId = $userId->user_id;
		}

		return $this->db()->fetchAllKeyed($this->db()->limit("
			SELECT ip,
				MIN(log_date) AS first_date,
				MAX(log_date) AS last_date,
				COUNT(*) AS total
			FROM xf_ip
			WHERE user_id = ?
			GROUP BY ip
			ORDER BY last_date DESC
		", $limit, $offset), 'ip', $userId);
	}

}