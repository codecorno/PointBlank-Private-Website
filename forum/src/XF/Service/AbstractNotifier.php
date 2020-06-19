<?php

namespace XF\Service;

abstract class AbstractNotifier extends AbstractService
{
	const USERS_PER_CYCLE = 100;

	protected $notifyData = [];

	/**
	 * @var \XF\Notifier\AbstractNotifier[]|null
	 */
	protected $notifiers = null;

	protected $alerted = [];
	protected $emailed = [];

	abstract protected function getExtraJobData();
	abstract protected function loadNotifiers();
	abstract protected function loadExtraUserData(array $users);
	abstract protected function canUserViewContent(\XF\Entity\User $user);

	public static function createForJob(array $extraData)
	{
		throw new \LogicException('createForJob must be overridden');
	}

	public function notify($timeLimit = null)
	{
		$this->ensureDataLoaded();

		$endTime = $timeLimit > 0 ? microtime(true) + $timeLimit : null;

		foreach ($this->getNotifiers() AS $type => $notifier)
		{
			$data = $this->notifyData[$type];
			if (!$data)
			{
				// already processed or nothing to do
				continue;
			}

			$newData = $this->notifyType($notifier, $data, $endTime);
			$this->notifyData[$type] = $newData;

			if ($endTime && microtime(true) >= $endTime)
			{
				break;
			}
		}
	}

	public function notifyAndEnqueue($timeLimit = null)
	{
		$this->notify($timeLimit);
		return $this->enqueueJobIfNeeded();
	}

	protected function notifyType(\XF\Notifier\AbstractNotifier $notifier, array $data, $endTime = null)
	{
		do
		{
			$notifyUsers = array_slice($data, 0, self::USERS_PER_CYCLE, true);
			$users = $notifier->getUserData(array_keys($notifyUsers));

			$this->loadExtraUserData($users);

			foreach ($notifyUsers AS $userId => $notify)
			{
				unset($data[$userId]);

				if (!isset($users[$userId]))
				{
					continue;
				}

				$user = $users[$userId];

				if (!$this->canUserViewContent($user) || !$notifier->canNotify($user))
				{
					continue;
				}

				$alert = ($notify['alert'] && empty($this->alerted[$userId]));
				if ($alert && $notifier->sendAlert($user))
				{
					$this->alerted[$userId] = true;
				}

				$email = ($notify['email'] && empty($this->emailed[$userId]));
				if ($email && $notifier->sendEmail($user))
				{
					$this->emailed[$userId] = true;
				}

				if ($endTime && microtime(true) >= $endTime)
				{
					return $data;
				}
			}
		}
		while ($data);

		return $data;
	}

	public function enqueueJobIfNeeded()
	{
		if ($this->hasMore())
		{
			$this->app->jobManager()->enqueue('XF:Notifier', $this->getJobData());

			return true;
		}
		else
		{
			return false;
		}
	}

	public function getJobData()
	{
		$this->ensureDataLoaded();

		return [
			'service' => $this->app->extension()->resolveExtendedClassToRoot($this),
			'extra' => $this->getExtraJobData(),
			'notifyData' => $this->notifyData,
			'alerted' => $this->alerted,
			'emailed' => $this->emailed
		];
	}

	public function setupFromJobData(array $data)
	{
		$this->notifyData = $data['notifyData'];
		$this->alerted = $data['alerted'];
		$this->emailed =  $data['emailed'];
	}

	public function hasMore()
	{
		$this->ensureDataLoaded();
		$notifiers = $this->getNotifiers();
		if (!$notifiers)
		{
			return false;
		}

		foreach ($notifiers AS $type => $notifier)
		{
			if (!empty($this->notifyData[$type]))
			{
				return true;
			}
		}

		return false;
	}

	public function addNotification($type, $userId, $alert = true, $email = false)
	{
		$notifiers = $this->getNotifiers();

		if (!isset($notifiers[$type]))
		{
			throw new \InvalidArgumentException("Unknown notification type '$type'");
		}

		$this->notifyData[$type][$userId] = [
			'alert' => $alert,
			'email' => $email
		];
	}

	public function addNotifications($type, array $userIds, $alert = true, $email = false)
	{
		$notifiers = $this->getNotifiers();

		if (!isset($notifiers[$type]))
		{
			throw new \InvalidArgumentException("Unknown notification type '$type'");
		}

		$value = [
			'alert' => $alert,
			'email' => $email
		];

		foreach ($userIds AS $userId)
		{
			$this->notifyData[$type][$userId] = $value;
		}
	}

	public function setQuotedUserIds(array $userIds)
	{
		$this->addNotifications('quote', $userIds, true, false);
	}

	public function setMentionedUserIds(array $userIds)
	{
		$this->addNotifications('mention', $userIds, true, false);
	}

	public function setNotifyDataRaw(array $data)
	{
		$this->notifyData = $data;
	}

	public function getNotifyData()
	{
		return $this->notifyData;
	}

	public function setUserAsAlerted($userId)
	{
		$this->alerted[$userId] = true;
	}

	public function setAlertedRaw(array $alerted)
	{
		$this->alerted = $alerted;
	}

	public function getAlerted()
	{
		return $this->alerted;
	}

	public function setUserAsEmailed($userId)
	{
		$this->emailed[$userId] = true;
	}

	public function setEmailedRaw(array $alerted)
	{
		$this->emailed = $alerted;
	}

	public function getEmailed()
	{
		return $this->emailed;
	}

	protected function getNotifiers()
	{
		if ($this->notifiers === null)
		{
			$this->notifiers = $this->loadNotifiers();
		}

		return $this->notifiers;
	}

	protected function ensureDataLoaded()
	{
		foreach ($this->getNotifiers() AS $type => $notifier)
		{
			if (!isset($this->notifyData[$type]))
			{
				$this->notifyData[$type] = $notifier->getDefaultNotifyData();
			}
		}
	}
}