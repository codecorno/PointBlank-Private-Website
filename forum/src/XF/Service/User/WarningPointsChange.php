<?php

namespace XF\Service\User;

use XF\Entity\User;

class WarningPointsChange extends \XF\Service\AbstractService
{
	/**
	 * @var User
	 */
	protected $user;

	public function __construct(\XF\App $app, User $user)
	{
		parent::__construct($app);

		$this->user = $user;
	}

	public function shiftPoints($amount, $fromWarningDelete = false)
	{
		$this->setToPoints($this->user->warning_points + $amount, $fromWarningDelete);
	}

	public function setToPoints($newPoints, $fromWarningDelete = false)
	{
		$oldPoints = $this->user->warning_points;
		$newPoints = max(0, intval($newPoints));

		if ($oldPoints == $newPoints)
		{
			return;
		}

		$this->db()->beginTransaction();

		$this->user->warning_points = $newPoints;
		$this->user->save(true, false);

		if ($newPoints > $oldPoints)
		{
			$this->processPointsIncrease($oldPoints, $newPoints);
		}
		else if ($newPoints < $oldPoints)
		{
			$this->processPointsDecrease($oldPoints, $newPoints, $fromWarningDelete);
		}

		$this->db()->commit();
	}

	protected function processPointsIncrease($oldPoints, $newPoints)
	{
		$actions = $this->finder('XF:WarningAction')->order('points')->fetch();
		if (!$actions->count())
		{
			return;
		}

		/** @var \XF\Entity\WarningAction $action */
		foreach ($actions AS $action)
		{
			if ($action->points > $oldPoints && $action->points <= $newPoints)
			{
				$this->applyWarningAction($action);
			}
		}
	}

	protected function applyWarningAction(\XF\Entity\WarningAction $action)
	{
		$permanent = ($action->action_length_type == 'permanent');
		$endByPoints = ($action->action_length_type == 'points');

		if ($permanent || $endByPoints)
		{
			$actionEndDate = null;
		}
		else
		{
			$actionEndDate = min(pow(2,32) - 1, strtotime("+{$action->action_length} {$action->action_length_type}"));
		}

		$tempChangeKey = $action->getTempUserChangeKey();

		switch ($action->action)
		{
			case 'ban':
				/** @var \XF\Entity\UserBan $ban */
				$ban = $this->user->Ban;
				if ($endByPoints)
				{
					if ($ban)
					{
						if (!$ban->end_date)
						{
							// Permanent ban. If it was triggered, that should be with a lower point
							// value, so use that as the trigger. If not triggered, it was manual already so do nothing.
							break;
						}

						$minUnbanDate = $ban->end_date;
					}
					else
					{
						$minUnbanDate = 0;
					}

					$this->applyUserBan(0, true);
					$this->insertPointsActionTrigger($action, $minUnbanDate);
				}
				else
				{
					if ($ban)
					{
						if (!$ban->end_date || ($actionEndDate && $ban->end_date > $actionEndDate))
						{
							// already banned and the ban is longer than what would happen here so do nothing
							break;
						}
					}

					$this->applyUserBan($actionEndDate ?: 0, false);
				}
				break;

			case 'discourage':
				/** @var \XF\Service\User\TempChange $changeService */
				$changeService = $this->service('XF:User\TempChange');
				$changeService->applyFieldChange(
					$this->user, $tempChangeKey, 'Option.is_discouraged', true, $actionEndDate
				);

				if ($endByPoints)
				{
					$this->insertPointsActionTrigger($action);
				}
				break;

			case 'groups':
				$userGroupChangeKey = 'warning_action_' . $action->warning_action_id;

				/** @var \XF\Service\User\TempChange $changeService */
				$changeService = $this->service('XF:User\TempChange');
				$changeService->applyGroupChange(
					$this->user, $tempChangeKey, $action->extra_user_group_ids, $userGroupChangeKey, $actionEndDate
				);

				if ($endByPoints)
				{
					$this->insertPointsActionTrigger($action);
				}
				break;
		}
	}

	protected function insertPointsActionTrigger(\XF\Entity\WarningAction $action, $minUnbanDate = 0)
	{
		$this->db()->insert('xf_warning_action_trigger', [
			'warning_action_id' => $action->warning_action_id,
			'user_id' => $this->user->user_id,
			'action_date' => \XF::$time,
			'trigger_points' => $action->points,
			'action' => $action->action,
			'min_unban_date' => $minUnbanDate
		]);

		return $this->db()->lastInsertId();
	}

	protected function applyUserBan($endDate, $setTriggered)
	{
		/** @var \XF\Entity\UserBan $ban */
		$ban = $this->user->Ban;
		if (!$ban)
		{
			$reason = strval(\XF::phrase('warning_ban_reason'));

			$ban = $this->user->getRelationOrDefault('Ban', false);
			$ban->user_id = $this->user->user_id;
			$ban->ban_user_id = 0;
			$ban->user_reason = utf8_substr($reason, 0, 255);
		}

		$ban->end_date = $endDate;
		if ($setTriggered)
		{
			$ban->triggered = true;
		}
		$ban->save();

		return $ban;
	}

	protected function processPointsDecrease($oldPoints, $newPoints, $fromWarningDelete = false)
	{
		$triggers = $this->db()->fetchAllKeyed("
			SELECT *
			FROM xf_warning_action_trigger
			WHERE user_id = ?
			ORDER BY trigger_points DESC
		", 'action_trigger_id', $this->user->user_id);
		if ($triggers)
		{
			$remainingTriggers = $triggers;

			foreach ($triggers AS $key => $trigger)
			{
				if ($trigger['trigger_points'] > $newPoints)
				{
					unset($remainingTriggers[$key]);
					$this->removeActionTrigger($trigger, $remainingTriggers);
				}
			}
		}

		if ($fromWarningDelete)
		{
			$actions = $this->finder('XF:WarningAction')->order('points', 'desc')->fetch();
			/** @var \XF\Entity\WarningAction $action */
			foreach ($actions AS $action)
			{
				// If we're deleting, we need to undo warning action effects, even if they're time limited.
				// Points-based will be handled by the triggers so skip. Then only consider where we cross
				// the points threshold from the old (higher) to the new (lower) point values
				if (
					$action->action_length_type == 'points'
					|| $action->points > $oldPoints // threshold above where we were
					|| $action->points <= $newPoints // we're still at/above the threshold
				)
				{
					continue;
				}

				$this->removeWarningActionEffects($action);
			}
		}
	}

	protected function removeActionTrigger(array $trigger, array $otherTriggers)
	{
		$this->db()->beginTransaction();

		$changed = $this->db()->delete(
			'xf_warning_action_trigger', 'action_trigger_id = ?', $trigger['action_trigger_id']
		);
		if (!$changed)
		{
			$this->db()->rollback();
			return;
		}

		switch ($trigger['action'])
		{
			case 'ban':
				if ($trigger['min_unban_date'] && \XF::$time < $trigger['min_unban_date'])
				{
					// another ban trigger is still running
					break;
				}

				$remove = true;
				foreach ($otherTriggers AS $otherTrigger)
				{
					if ($otherTrigger['action'] == 'ban')
					{
						$remove = false;
						break;
					}
				}
				if (!$remove)
				{
					// there's still another ban trigger
					break;
				}

				if ($this->user->Ban)
				{
					/** @var \XF\Entity\UserBan $ban */
					$ban = $this->user->Ban;
					if (!$ban->end_date && $ban->triggered)
					{
						$ban->delete();
					}
				}
				break;

			case 'discourage':
			case 'groups';
				$tempChangeKey = 'warning_action_' . $trigger['warning_action_id'] . '_' . $trigger['action'];

				/** @var \XF\Repository\UserChangeTemp $changeRepo */
				$changeRepo = $this->repository('XF:UserChangeTemp');
				$changeRepo->expireUserChangeByKey($this->user, $tempChangeKey);
				break;
		}

		$this->db()->commit();
	}

	protected function removeWarningActionEffects(\XF\Entity\WarningAction $action)
	{
		switch ($action->action)
		{
			case 'ban':
				if ($this->user->Ban)
				{
					/** @var \XF\Entity\UserBan $ban */
					$ban = $this->user->Ban;

					$isPermanent = ($action->action_length_type == 'permanent');
					$endByPoints = ($action->action_length_type == 'points');

					// we can only undo this ban if:
					// - we inserted a permanent ban and this is one
					// - we inserted a points-based ban and this is one
					// - we inserted a time-based ban and this is one
					if (
						($isPermanent && !$ban->end_date)
						|| ($endByPoints && $ban->triggered)
						|| (!$isPermanent && !$endByPoints && $ban->end_date && !$ban->triggered)
					)
					{
						$ban->delete();
					}
				}
				break;

			case 'discourage':
			case 'groups';
				$tempChangeKey = $action->getTempUserChangeKey();

				/** @var \XF\Repository\UserChangeTemp $changeRepo */
				$changeRepo = $this->repository('XF:UserChangeTemp');
				$changeRepo->expireUserChangeByKey($this->user, $tempChangeKey);
				break;
		}
	}
}