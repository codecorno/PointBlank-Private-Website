<?php

namespace XF\Service\CronEntry;

class CalculateNextRun extends \XF\Service\AbstractService
{
	/**
	 * Calculate the next run time for an entry using the given rules. Rules expected in keys:
	 * minutes, hours, dow, dom (all arrays) and day_type (string: dow or dom)
	 * Array rules are in format: -1 means "any", any other value means on those specific
	 * occurances. DoW runs 0 (Sunday) to 6 (Saturday).
	 *
	 * @param array $runRules Run rules. See above for format.
	 * @param integer|null $currentTime Current timestamp; null to use current time from application
	 *
	 * @return integer Next run timestamp
	 */
	public function calculateNextRunTime(array $runRules, $currentTime = null)
	{
		$currentTime = ($currentTime === null ? \XF::$time : $currentTime);

		$nextRun = new \DateTime('@' . $currentTime);
		$nextRun->modify('+1 minute');

		if (empty($runRules['minutes']))
		{
			$runRules['minutes'] = [-1];
		}
		$this->modifyRunTimeMinutes($runRules['minutes'], $nextRun);

		if (empty($runRules['hours']))
		{
			$runRules['hours'] = [-1];
		}
		$this->modifyRunTimeHours($runRules['hours'], $nextRun);

		if (!empty($runRules['day_type']))
		{
			if ($runRules['day_type'] == 'dow')
			{
				if (empty($runRules['dow']))
				{
					$runRules['dow'] = [-1];
				}
				$this->modifyRunTimeDayOfWeek($runRules['dow'], $nextRun);
			}
			else
			{
				if (empty($runRules['dom']))
				{
					$runRules['dom'] = [-1];
				}
				$this->modifyRunTimeDayOfMonth($runRules['dom'], $nextRun);
			}
		}

		return intval($nextRun->format('U'));
	}

	/**
	 * Modifies the next run time based on the minute rules.
	 *
	 * @param array $minuteRules Rules about what minutes are valid (-1, or any number of values 0-59)
	 * @param \DateTime $nextRun Date calculation object. This will be modified.
	 */
	protected function modifyRunTimeMinutes(array $minuteRules, \DateTime &$nextRun)
	{
		$currentMinute = $nextRun->format('i');
		$this->modifyRunTimeUnits($minuteRules, $nextRun, $currentMinute, 'minute', 'hour');
	}

	/**
	 * Modifies the next run time based on the hour rules.
	 *
	 * @param array $hourRules Rules about what hours are valid (-1, or any number of values 0-23)
	 * @param \DateTime $nextRun Date calculation object. This will be modified.
	 */
	protected function modifyRunTimeHours(array $hourRules, \DateTime &$nextRun)
	{
		$currentHour = $nextRun->format('G');
		$this->modifyRunTimeUnits($hourRules, $nextRun, $currentHour, 'hour', 'day');
	}

	/**
	 * Modifies the next run time based on the day of month rules. Note that if
	 * the required DoM doesn't exist (eg, Feb 30), it will be rolled over as if
	 * it did (eg, to Mar 2).
	 *
	 * @param array $hourRules Rules about what days are valid (-1, or any number of values 0-31)
	 * @param \DateTime $nextRun Date calculation object. This will be modified.
	 */
	protected function modifyRunTimeDayOfMonth(array $dayRules, \DateTime &$nextRun)
	{
		$currentDay = $nextRun->format('j');
		$this->modifyRunTimeUnits($dayRules, $nextRun, $currentDay, 'day', 'month');
	}

	/**
	 * Modifies the next run time based on the day of week rules.
	 *
	 * @param array $hourRules Rules about what days are valid (-1, or any number of values 0-6 [sunday to saturday])
	 * @param \DateTime $nextRun Date calculation object. This will be modified.
	 */
	protected function modifyRunTimeDayOfWeek(array $dayRules, \DateTime &$nextRun)
	{
		$currentDay = $nextRun->format('w'); // 0 = sunday, 6 = saturday
		$this->modifyRunTimeUnits($dayRules, $nextRun, $currentDay, 'day', 'week');
	}

	/**
	 * General purpose run time calculator for a set of rules.
	 *
	 * @param array $unitRules List of rules for unit. Array of ints, values -1 to unit-defined max.
	 * @param \DateTime $nextRun Date calculation object. This will be modified.
	 * @param integer $currentUnitValue The current value for the specified unit type
	 * @param string $unitName Name of the current unit (eg, minute, hour, day, etc)
	 * @param string $rolloverUnitName Name of the unit to use when rolling over; one unit bigger (eg, minutes to hours)
	 */
	protected function modifyRunTimeUnits(array $unitRules, \DateTime &$nextRun, $currentUnitValue, $unitName, $rolloverUnitName)
	{
		if (sizeof($unitRules) && reset($unitRules) == -1)
		{
			// correct already
			return;
		}

		$currentUnitValue = intval($currentUnitValue);
		$rollover = null;

		sort($unitRules, SORT_NUMERIC);
		foreach ($unitRules AS $unitValue)
		{
			if ($unitValue == -1 || $unitValue == $currentUnitValue)
			{
				// already in correct position
				$rollover = null;
				break;
			}
			else if ($unitValue > $currentUnitValue)
			{
				// found unit later in date, adjust to time
				$nextRun->modify('+ ' . ($unitValue - $currentUnitValue) . " $unitName");
				$rollover = null;
				break;
			}
			else if ($rollover === null)
			{
				// found unit earlier in the date; use smallest value
				$rollover = $unitValue;
			}
		}

		if ($rollover !== null)
		{
			$nextRun->modify(($rollover - $currentUnitValue) . " $unitName");
			$nextRun->modify("+ 1 $rolloverUnitName");
		}
	}

	/**
	 * Atomically update the next run time for a cron entry. This allows you
	 * to determine whehter a cron entry still needs to be run.
	 *
	 * @param \XF\Entity\CronEntry $entry Cron entry info
	 *
	 * @return boolean True if updated (thus safe to run), false otherwise
	 */
	public function updateCronRunTimeAtomic(\XF\Entity\CronEntry $entry)
	{
		$runRules = $entry['run_rules'];
		$nextRun = $this->calculateNextRunTime($runRules);

		$updateResult = $this->db()->update(
			'xf_cron_entry',
			['next_run' => $nextRun],
			'entry_id = ? AND next_run = ?',
			[$entry['entry_id'], $entry['next_run']]
		);

		return (bool)$updateResult;
	}

	/**
	 * Gets the minimum next run time stamp (ie, time next entry is due to run).
	 *
	 * @return integer
	 */
	public function getMinimumNextRunTime()
	{
		$nextRunTime = $this->db()->fetchOne('
			SELECT MIN(entry.next_run)
			FROM xf_cron_entry AS entry
			LEFT JOIN xf_addon AS addon ON (entry.addon_id = addon.addon_id)
			WHERE entry.active = 1
				AND (addon.addon_id IS NULL OR addon.active = 1)
		');
		if ($nextRunTime)
		{
			return $nextRunTime;
		}
		else
		{
			// no crons? This shouldn't happen so it might be a mistake - check again in 30 minutes
			return \XF::$time + 30 * 60;
		}
	}

	/**
	 * Updates the entry for the minimum next run time.
	 * Cron calls are not needed until that point.
	 *
	 * @return integer Minimum next run time
	 */
	public function updateMinimumNextRunTime()
	{
		$minimumRunTime = intval($this->getMinimumNextRunTime());

		if ($minimumRunTime)
		{
			$this->app->jobManager()->enqueueLater('cron', $minimumRunTime, 'XF\Job\Cron');
		}

		return $minimumRunTime;
	}
}