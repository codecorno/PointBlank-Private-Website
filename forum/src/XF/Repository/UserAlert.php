<?php

namespace XF\Repository;

use XF\Mvc\Entity\Finder;
use XF\Mvc\Entity\Repository;

class UserAlert extends Repository
{
	/**
	 * @param int $userId
	 * @param null|int $cutOff
	 *
	 * @return Finder
	 */
	public function findAlertsForUser($userId, $cutOff = null)
	{
		$finder = $this->finder('XF:UserAlert')
			->where('alerted_user_id', $userId)
			->whereAddOnActive([
				'column' => 'depends_on_addon_id'
			])
			->order('event_date', 'desc')
			->with('User');

		if ($cutOff)
		{
			$finder->whereOr(
				['view_date', '=', 0],
				['view_date', '>=', $cutOff]
			);
		}

		return $finder;
	}

	public function userReceivesAlert(\XF\Entity\User $receiver, $senderId, $contentType, $action)
	{
		if (!$receiver->user_id)
		{
			return false;
		}

		if ($senderId && $receiver->isIgnoring($senderId))
		{
			return false;
		}

		if ($receiver->Option)
		{
			/** @var \XF\Entity\UserOption $userOption */
			$userOption = $receiver->Option;
			return $userOption->doesReceiveAlert($contentType, $action);
		}
		else
		{
			return true;
		}
	}

	public function userReceivesPush(\XF\Entity\User $receiver, $senderId, $contentType, $action)
	{
		if (!$receiver->user_id)
		{
			return false;
		}

		if ($senderId && $receiver->isIgnoring($senderId))
		{
			return false;
		}

		if ($receiver->Option)
		{
			/** @var \XF\Entity\UserOption $userOption */
			$userOption = $receiver->Option;
			return $userOption->doesReceivePush($contentType, $action);
		}
		else
		{
			return true;
		}
	}

	public function alertFromUser(
		\XF\Entity\User $receiver, \XF\Entity\User $sender = null,
		$contentType, $contentId, $action, array $extra = []
	)
	{
		$senderId = $sender ? $sender->user_id : 0;
		$senderName = $sender ? $sender->username : '';

		if (!$this->userReceivesAlert($receiver, $senderId, $contentType, $action))
		{
			return false;
		}

		return $this->insertAlert($receiver->user_id, $senderId, $senderName, $contentType, $contentId, $action, $extra);
	}

	public function alert(
		\XF\Entity\User $receiver, $senderId, $senderName,
		$contentType, $contentId, $action, array $extra = []
	)
	{
		if (!$this->userReceivesAlert($receiver, $senderId, $contentType, $action))
		{
			return false;
		}

		return $this->insertAlert($receiver->user_id, $senderId, $senderName, $contentType, $contentId, $action, $extra);
	}

	public function insertAlert(
		$receiverId, $senderId, $senderName,
		$contentType, $contentId, $action, array $extra = []
	)
	{
		if (!$receiverId)
		{
			return false;
		}

		$dependsOn = '';
		if (isset($extra['depends_on_addon_id']))
		{
			$dependsOn = $extra['depends_on_addon_id'];
			unset($extra['depends_on_addon_id']);
		}

		/** @var \XF\Entity\UserAlert $alert */
		$alert = $this->em->create('XF:UserAlert');
		$alert->alerted_user_id = $receiverId;
		$alert->user_id = $senderId;
		$alert->username = $senderName;
		$alert->content_type = $contentType;
		$alert->content_id = $contentId;
		$alert->action = $action;
		$alert->extra_data = $extra;
		$alert->depends_on_addon_id = $dependsOn;
		$alert->save();

		if ($alert->Receiver && $this->userReceivesPush($alert->Receiver, $senderId, $contentType, $action))
		{
			/** @var \XF\Service\Alert\Pusher $pusher */
			$pusher = $this->app()->service('XF:Alert\Pusher', $alert->Receiver, $alert);
			$pusher->push();
		}

		return true;
	}

	public function fastDeleteAlertsToUser($toUserId, $contentType, $contentId, $action)
	{
		$finder = $this->finder('XF:UserAlert')
			->where([
				'content_type' => $contentType,
				'content_id' => $contentId,
				'action' => $action,
				'alerted_user_id' => $toUserId
			]);
		$this->deleteAlertsInternal($finder);
		// TODO: approach will need to change if there's alert folding
	}

	public function fastDeleteAlertsFromUser($fromUserId, $contentType, $contentId, $action)
	{
		$finder = $this->finder('XF:UserAlert')
			->where([
				'content_type' => $contentType,
				'content_id' => $contentId,
				'action' => $action,
				'user_id' => $fromUserId
			]);
		$this->deleteAlertsInternal($finder);
		// TODO: approach will need to change if there's alert folding
	}

	public function fastDeleteAlertsForContent($contentType, $contentId)
	{
		$finder = $this->finder('XF:UserAlert')
			->where([
				'content_type' => $contentType,
				'content_id' => $contentId
			]);
		$this->deleteAlertsInternal($finder);
	}

	protected function deleteAlertsInternal(Finder $matches)
	{
		$results = $matches->fetchColumns('alert_id', 'alerted_user_id', 'view_date');
		if (!$results)
		{
			return;
		}

		$countChange = [];
		$delete = [];
		foreach ($results AS $result)
		{
			$delete[] = $result['alert_id'];
			if (!$result['view_date'])
			{
				if (isset($countChange[$result['alerted_user_id']]))
				{
					$countChange[$result['alerted_user_id']]++;
				}
				else
				{
					$countChange[$result['alerted_user_id']] = 1;
				}
			}
		}

		$db = $this->db();
		$db->beginTransaction();

		$db->delete('xf_user_alert', 'alert_id IN (' . $db->quote($delete) . ')');
		foreach ($countChange AS $userId => $change)
		{
			$db->query("
				UPDATE xf_user
				SET alerts_unread = GREATEST(0, CAST(alerts_unread AS SIGNED) - ?)
				WHERE user_id = ?
			", [$change, $userId]);
		}

		$db->commit();
	}

	public function markUserAlertsRead(\XF\Entity\User $user, $viewDate = null)
	{
		if (!$viewDate)
		{
			$viewDate = \XF::$time;
		}

		if (!$user->user_id)
		{
			throw new \LogicException("Trying to mark alerts read for an invalid user");
		}

		$db = $this->db();
		$db->executeTransaction(function() use ($db, $viewDate, $user)
		{
			$db->update('xf_user_alert',
				['view_date' => $viewDate],
				'alerted_user_id = ? AND view_date = 0',
				$user->user_id
			);

			$user->alerts_unread = 0;
			$user->save(true, false);
		}, \XF\Db\AbstractAdapter::ALLOW_DEADLOCK_RERUN);
	}

	public function markUserAlertsReadForContent($contentType, $contentIds, $onlyActions = null, \XF\Entity\User $user = null, $viewDate = null)
	{
		if ($user === null)
		{
			$user = \XF::visitor();
		}

		if (!$user->user_id || !$user->alerts_unread)
		{
			return;
		}

		if (!is_array($contentIds))
		{
			$contentIds = [$contentIds];
		}

		if (!$contentIds)
		{
			return;
		}

		if (!$viewDate)
		{
			$viewDate = \XF::$time;
		}

		$db = $this->db();

		$excludeActionsClause = '';
		if ($onlyActions)
		{
			if (!is_array($onlyActions))
			{
				$onlyActions = [$onlyActions];
			}

			$excludeActionsClause = ' AND action IN(' . $db->quote($onlyActions) . ')';
		}

		$unviewedAlertIds = $db->fetchAllColumn('
			SELECT alert_id
			FROM xf_user_alert
			WHERE content_type = ?
			AND content_id IN(' . $db->quote($contentIds) . ')
			AND alerted_user_id = ?
			AND view_date = 0
			AND event_date < ?
		' . $excludeActionsClause, [$contentType, $user->user_id, $viewDate]);

		if (!$unviewedAlertIds)
		{
			return;
		}

		$db->executeTransaction(function() use ($db, $unviewedAlertIds, $viewDate, $user)
		{
			$db->update('xf_user_alert',
				['view_date' => $viewDate],
				'alert_id IN(' . $db->quote($unviewedAlertIds) . ')'
			);

			$user->alerts_unread = ($user->alerts_unread - count($unviewedAlertIds));
			$user->save(true, false);
		}, \XF\Db\AbstractAdapter::ALLOW_DEADLOCK_RERUN);
	}

	public function pruneReadAlerts($cutOff = null)
	{
		if ($cutOff === null)
		{
			$cutOff = \XF::$time - $this->options()->alertExpiryDays * 86400;
		}

		$this->db()->delete('xf_user_alert', 'view_date > 0 AND view_date < ?', $cutOff);
	}

	public function pruneUnreadAlerts($cutOff = null)
	{
		if ($cutOff === null)
		{
			$cutOff = \XF::$time - 30 * 86400;
		}

		$this->db()->delete('xf_user_alert', 'view_date = 0 AND event_date < ?', $cutOff);
	}

	/**
	 * @param \XF\Entity\User $user
	 *
	 * @return bool
	 */
	public function updateUnreadCountForUser(\XF\Entity\User $user)
	{
		if (!$user->user_id)
		{
			return false;
		}

		$count = $this->findAlertsForUser($user->user_id)
			->where('view_date', 0)
			->total();

		$user->alerts_unread = $count;
		$user->saveIfChanged($updated);

		return $updated;
	}

	/**
	 * @return \XF\Alert\AbstractHandler[]
	 */
	public function getAlertHandlers()
	{
		$handlers = [];

		foreach (\XF::app()->getContentTypeField('alert_handler_class') AS $contentType => $handlerClass)
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
	 * @param bool $throw
	 *
	 * @return \XF\Alert\AbstractHandler|null
	 */
	public function getAlertHandler($type, $throw = false)
	{
		$handlerClass = \XF::app()->getContentTypeFieldValue($type, 'alert_handler_class');
		if (!$handlerClass)
		{
			if ($throw)
			{
				throw new \InvalidArgumentException("No Alert handler for '$type'");
			}
			return null;
		}

		if (!class_exists($handlerClass))
		{
			if ($throw)
			{
				throw new \InvalidArgumentException("Alert handler for '$type' does not exist: $handlerClass");
			}
			return null;
		}

		$handlerClass = \XF::extendClass($handlerClass);
		return new $handlerClass($type);
	}

	/**
	 * @param \XF\Mvc\Entity\ArrayCollection|\XF\Entity\UserAlert[] $alerts
	 */
	public function addContentToAlerts($alerts)
	{
		$contentMap = [];
		foreach ($alerts AS $key => $alert)
		{
			$contentType = $alert->content_type;
			if (!isset($contentMap[$contentType]))
			{
				$contentMap[$contentType] = [];
			}
			$contentMap[$contentType][$key] = $alert->content_id;
		}

		foreach ($contentMap AS $contentType => $contentIds)
		{
			$handler = $this->getAlertHandler($contentType);
			if (!$handler)
			{
				continue;
			}
			$data = $handler->getContent($contentIds);
			foreach ($contentIds AS $alertId => $contentId)
			{
				$content = isset($data[$contentId]) ? $data[$contentId] : null;
				$alerts[$alertId]->setContent($content);
			}
		}
	}

	public function getAlertOptOuts()
	{
		$handlers = $this->getAlertHandlers();

		$alertOptOuts = [];
		$orderedTypes = [];

		foreach ($handlers AS $contentType => $handler)
		{
			$optOuts = $handler->getOptOutsMap();
			if (!$optOuts)
			{
				continue;
			}

			$alertOptOuts[$contentType] = $optOuts;
			$orderedTypes[$contentType] = $handler->getOptOutDisplayOrder();
		}
		asort($orderedTypes);

		$orderedOptOuts = [];
		foreach ($orderedTypes AS $contentType => $null)
		{
			$orderedOptOuts[$contentType] = $alertOptOuts[$contentType];
		}

		return $orderedOptOuts;
	}

	public function getAlertOptOutActions()
	{
		$handlers = $this->getAlertHandlers();

		$actions = [];
		foreach ($handlers AS $contentType => $handler)
		{
			foreach ($handler->getOptOutActions() AS $action)
			{
				$actions[$contentType . '_' . $action] = true;
			}
		}

		return $actions;
	}
}