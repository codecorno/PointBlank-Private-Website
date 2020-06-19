<?php

namespace XF\Repository;

use XF\Mvc\Entity\Finder;
use XF\Mvc\Entity\Repository;

class Warning extends Repository
{
	/**
	 * @return Finder
	 */
	public function findWarningDefinitionsForList()
	{
		return $this->finder('XF:WarningDefinition')->setDefaultOrder('points_default');
	}

	/**
	 * @return Finder
	 */
	public function findWarningActionsForList()
	{
		return $this->finder('XF:WarningAction')->setDefaultOrder('points');
	}

	/**
	 * @return Finder
	 */
	public function findUserWarningsForList($userId)
	{
		return $this->finder('XF:Warning')
			->where('user_id', $userId)
			->with('WarnedBy')
			->setDefaultOrder('warning_date', 'DESC');
	}

	public function processExpiredWarnings()
	{
		/** @var \XF\Entity\Warning[] $warnings */
		$warnings = $this->finder('XF:Warning')
			->where('expiry_date', '<=', \XF::$time)
			->where('expiry_date', '>', 0)
			->where('is_expired', 0)
			->fetch();
		foreach ($warnings AS $warning)
		{
			$warning->is_expired = true;
			$warning->setOption('log_moderator', false);
			$warning->save();
		}
	}

	/**
	 * @return \XF\Warning\AbstractHandler[]
	 */
	public function getWarningHandlers()
	{
		$handlers = [];

		foreach (\XF::app()->getContentTypeField('warning_handler_class') AS $contentType => $handlerClass)
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
	 * @return \XF\Warning\AbstractHandler|null
	 */
	public function getWarningHandler($type, $throw = false)
	{
		$handlerClass = \XF::app()->getContentTypeFieldValue($type, 'warning_handler_class');
		if (!$handlerClass)
		{
			if ($throw)
			{
				throw new \InvalidArgumentException("No warning handler for '$type'");
			}
			return null;
		}

		if (!class_exists($handlerClass))
		{
			if ($throw)
			{
				throw new \InvalidArgumentException("Warning handler for '$type' does not exist: $handlerClass");
			}
			return null;
		}

		$handlerClass = \XF::extendClass($handlerClass);
		return new $handlerClass($type);
	}

	public function getMinimumUnbanDate($userId)
	{
		$minPoints = null;
		$minUnbanDate = 0;

		$triggers = $this->finder('XF:WarningActionTrigger')
			->where('user_id', $userId)
			->order('trigger_points')
			->fetch();

		foreach ($triggers AS $trigger)
		{
			if ($trigger->action == 'ban')
			{
				$minPoints = $trigger->trigger_points;
				$minUnbanDate = $trigger->min_unban_date;
				break;
			}
		}

		if (!$minPoints)
		{
			return null;
		}

		$totalPoints = 0;
		$expiry = [];
		$points = [];
		foreach ($this->findUserWarningsForList($userId)->fetch() AS $warning)
		{
			if ($warning->is_expired || !$warning->points)
			{
				continue;
			}

			if ($warning->expiry_date)
			{
				$expiry[] = $warning->expiry_date;
				$points[] = $warning->points;
			}

			$totalPoints += $warning->points;
		}

		if ($totalPoints < $minPoints)
		{
			return null;
		}

		asort($expiry);
		foreach ($expiry AS $key => $expiryDate)
		{
			$totalPoints -= $points[$key];
			if ($totalPoints < $minPoints)
			{
				return max($minUnbanDate, $expiryDate);
			}
		}

		return null;
	}
}