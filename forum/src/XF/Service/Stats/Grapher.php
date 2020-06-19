<?php

namespace XF\Service\Stats;

class Grapher extends \XF\Service\AbstractService
{
	protected $start;
	protected $end;
	protected $types;

	public function __construct(\XF\App $app, $start, $end, array $types = [])
	{
		parent::__construct($app);

		$this->setDateRange($start, $end);
		$this->types = $types;
	}

	public function addType($type)
	{
		$this->types[] = $type;
	}

	public function setDateRange($start, $end)
	{
		$start = intval($start);
		$start -= $start % 86400; // make sure we always get the start of the day
		$end = intval($end);

		if ($end < $start)
		{
			$end = $start;
		}

		$this->start = $start;
		$this->end = $end;
	}

	protected function getRawData()
	{
		if (!$this->types)
		{
			throw new \LogicException("Must have at least one type selected");
		}

		$output = [];
		$db = $this->db();
		$stats = $db->query('
			SELECT stats_date, stats_type, counter
			FROM xf_stats_daily
			WHERE stats_date BETWEEN ? AND ?
				AND stats_type IN (' . $db->quote($this->types) . ')
			ORDER BY stats_date
		', [$this->start, $this->end]);
		while ($stat = $stats->fetch())
		{
			$output[$stat['stats_date']][$stat['stats_type']] = $stat['counter'];
		}

		return $output;
	}

	protected function handleGroupingNotFound($groupValue, \XF\Stats\Grouper\AbstractGrouper $grouper)
	{
		throw new \LogicException("Grouping {$groupValue} not found. This should have been created internally. Report as a bug.");
	}

	public function getGroupedData(\XF\Stats\Grouper\AbstractGrouper $grouper)
	{
		$baseValues = [];
		foreach ($this->types AS $type)
		{
			$baseValues[$type] = 0;
		}

		$groupings = [];
		foreach ($grouper->getGroupingsInRange($this->start, $this->end) AS $k => $grouping)
		{
			$grouping['count'] = 0;
			$grouping['values'] = $baseValues;
			$grouping['averages'] = $baseValues;
			$groupings[$k] = $grouping;
		}

		$rawData = $this->getRawData();

		foreach ($rawData AS $timestamp => $typeValues)
		{
			$groupValue = $grouper->getGrouping($timestamp);
			if (!isset($groupings[$groupValue]))
			{
				$this->handleGroupingNotFound($groupValue, $grouper);
			}
			else
			{
				$groupings[$groupValue]['count']++;

				foreach ($typeValues AS $type => $value)
				{
					if (isset($groupings[$groupValue]['values'][$type]))
					{
						$groupings[$groupValue]['values'][$type] += $value;
					}
					else
					{
						$groupings[$groupValue]['values'][$type] = $value;
					}

					if ($tip = $grouper->getTotalTooltip($timestamp, $type, $groupings[$groupValue]['values'][$type]))
					{
						$groupings[$groupValue]['values.tips'][$type] = $tip;
					}
				}
			}
		}

		$typeHandlers = $this->repository('XF:Stats')->getStatsTypeHandlers($this->types);

		foreach ($groupings AS $timestamp => $grouping)
		{
			foreach ($grouping['values'] AS $type => $value)
			{
				if (isset($typeHandlers[$type]))
				{
					$value = $typeHandlers[$type]->adjustStatValue($type, $value);
					$groupings[$timestamp]['values'][$type] = $value;
				}

				$average = $value / $grouping['days'];
				if ($grouping['days'] > 1)
				{
					$average = round($average, 2);
				}

				$groupings[$timestamp]['averages'][$type] = $average;

				if ($tip = $grouper->getAverageTooltip($timestamp, $type, $average))
				{
					$groupings[$groupValue]['averages.tips'][$type] = $tip;
				}
			}
		}

		return $groupings;
	}
}