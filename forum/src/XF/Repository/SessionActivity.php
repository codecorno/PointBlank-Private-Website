<?php

namespace XF\Repository;

use XF\Mvc\Entity\Finder;
use XF\Mvc\Entity\Repository;
use XF\Mvc\ParameterBag;

class SessionActivity extends Repository
{
	public function getOnlineCounts($onlineCutOff = null)
	{
		if ($onlineCutOff === null)
		{
			$onlineCutOff = \XF::$time - $this->options()->onlineStatusTimeout * 60;
		}

		return $this->db()->fetchRow("
			SELECT
				SUM(IF(user_id >= 0 AND robot_key = '', 1, 0)) AS total,
				SUM(IF(user_id > 0, 1, 0)) AS members,
				SUM(IF(user_id = 0 AND robot_key = '', 1, 0)) AS guests
			FROM xf_session_activity
			WHERE view_date >= ?
		", $onlineCutOff);
	}

	public function getOnlineUsersList($limit)
	{
		/** @var \XF\Finder\SessionActivity $finder */
		$finder = $this->finder('XF:SessionActivity');
		$finder->restrictType('member')
			->applyMemberVisibilityRestriction()
			->activeOnly()
			->with('User')
			->order('view_date', 'DESC');

		if ($limit)
		{
			$finder->limit($limit);
		}

		return $finder->fetch()->pluckNamed('User', 'user_id');
	}

	public function getOnlineStaffList()
	{
		/** @var \XF\Finder\SessionActivity $finder */
		$finder = $this->finder('XF:SessionActivity');
		$finder->restrictType('member')
			->applyMemberVisibilityRestriction()
			->activeOnly()
			->with('User')
			->where('User.is_staff', 1)
			->order('view_date', 'DESC');

		return $finder->fetch()->pluckNamed('User', 'user_id');
	}

	public function getOnlineStatsBlockData($forceIncludeVisitor = true, $userLimit, $staffQuery = false)
	{
		$counts = $this->getOnlineCounts();
		$users = $this->getOnlineUsersList($userLimit)->toArray();

		if ($forceIncludeVisitor)
		{
			$visitor = \XF::visitor();
			if ($visitor->user_id && !isset($users[$visitor->user_id]))
			{
				$users = [$visitor->user_id => $visitor] + $users;
				$counts['members']++;
				$counts['total']++;
			}
		}

		// run extra query to show all online staff
		if ($staffQuery)
		{
			$users += $this->getOnlineStaffList()->toArray();
		}

		$counts['unseen'] = ($userLimit ? max($counts['members'] - $userLimit, 0) : 0);

		return [
			'counts' => $counts,
			'users' => $users
		];
	}

	public function isTypeRestrictionValid($type)
	{
		switch ($type)
		{
			case 'member':
			case 'guest':
			case 'robot':
			case '':
				return true;

			default:
				return false;
		}
	}

	public function findForOnlineList($typeLimit)
	{
		/** @var \XF\Finder\SessionActivity $finder */
		$finder = $this->finder('XF:SessionActivity');
		$finder->activeOnly()
			->restrictType($typeLimit)
			->withFullUser()
			->order('view_date', 'DESC');

		return $finder;
	}

	public function updateSessionActivity($userId, $ip, $controller, $action, array $params, $viewState, $robotKey)
	{
		$userId = intval($userId);
		$binaryIp = \XF\Util\Ip::convertIpStringToBinary($ip);
		$uniqueKey = ($userId ? $userId : $binaryIp);

		if ($userId)
		{
			$robotKey = '';
		}

		$logParams = [];
		foreach ($params AS $paramKey => $paramValue)
		{
			if (!strlen($paramKey) || !is_scalar($paramValue))
			{
				continue;
			}

			$logParams[] = "$paramKey=" . urlencode($paramValue);
		}
		$paramList = implode('&', $logParams);

		$controller = substr($controller, 0, 100);
		$action = substr($action, 0, 75);
		$paramList = substr($paramList, 0, 100);
		$robotKey = substr($robotKey, 0, 25);

		$this->db()->query("
			-- XFDB=noForceAllWrite
			INSERT INTO xf_session_activity
				(`user_id`, `unique_key`, `ip`, `controller_name`, `controller_action`, `view_state`, `params`, `view_date`, `robot_key`)
			VALUES
				(?, ?, ?, ?, ?, ?, ?, ?, ?)
			ON DUPLICATE KEY UPDATE ip = VALUES(ip),
				controller_name = VALUES(controller_name),
				controller_action = VALUES(controller_action),
				view_state = VALUES(view_state),
				params = VALUES(params),
				view_date = VALUES(view_date),
				robot_key = VALUES(robot_key)
		", [$userId, $uniqueKey, $binaryIp, $controller, $action, $viewState, $paramList, \XF::$time, $robotKey]);
		// TODO: swallow errors if upgrade is pending
	}

	public function updateUserLastActivityFromSession()
	{
		$this->db()->query("
			UPDATE xf_user AS u
			INNER JOIN xf_session_activity AS a ON (a.user_id > 0 AND a.user_id = u.user_id)
			SET u.last_activity = a.view_date
		");
	}

	public function pruneExpiredActivityRecords($cutOff = null)
	{
		if ($cutOff === null)
		{
			$cutOff = \XF::$time - 3600;
		}

		$this->db()->delete('xf_session_activity', 'view_date < ?', $cutOff);
	}

	public function clearUserActivity($userId, $ip)
	{
		$userId = intval($userId);
		$binaryIp = \XF\Util\Ip::convertIpStringToBinary($ip);
		$uniqueKey = ($userId ? $userId : $binaryIp);

		$this->db()->delete('xf_session_activity',
			'user_id = ? AND unique_key = ?',
			[$userId, $uniqueKey]
		);
	}

	public function applyActivityDetails($activities)
	{
		if ($activities instanceof \XF\Entity\SessionActivity)
		{
			$activities = [$activities];
		}

		$controllers = [];
		foreach ($activities AS $key => $activity)
		{
			$controllers[$activity->controller_name][$key] = $activity;
		}

		foreach ($controllers AS $controller => $entries)
		{
			$controller = $this->app()->extension()->extendClass($controller);
			try
			{
				$valid = ($controller
					&& class_exists($controller)
					&& is_callable([$controller, 'getActivityDetails'])
				);
			}
			catch (\Exception $e)
			{
				// don't let a class load error (XFCP) error
				$valid = false;
			}

			if ($valid)
			{
				$controllerOutput = call_user_func([$controller, 'getActivityDetails'], $entries);
			}
			else
			{
				$controllerOutput = false;
			}

			if (is_array($controllerOutput))
			{
				foreach ($controllerOutput AS $key => $info)
				{
					if (!isset($entries[$key]))
					{
						continue;
					}

					/** @var \XF\Entity\SessionActivity $activity */
					$activity = $entries[$key];

					if (is_array($info))
					{
						$activity->setItemDetails($info['description'], $info['title'], $info['url']);
					}
					else
					{
						$activity->setItemDetails($info);
					}
				}
			}
			else
			{
				foreach ($entries AS $key => $activity)
				{
					$activity->setItemDetails($controllerOutput);
				}
			}
		}
	}
}