<?php

namespace XF\Stats;

use XF\App;

abstract class AbstractHandler
{
	protected $contentType;

	/**
	 * @var App
	 */
	protected $app;

	public function __construct($contentType, App $app)
	{
		$this->contentType = $contentType;
		$this->app = $app;
	}

	/**
	 * @return \XF\Db\AbstractAdapter
	 */
	public function db()
	{
		return $this->app->db();
	}

	abstract public function getStatsTypes();
	abstract public function getData($start, $end);

	/**
	 * Manipulates a statistic type before display. Must still return a number (no formatting).
	 *
	 * @param string $statsType
	 * @param number $counter
	 *
	 * @return number
	 */
	public function adjustStatValue($statsType, $counter)
	{
		return $counter;
	}

	/**
	 * Returns SQL for a basic stats prepared statement.
	 *
	 * @param string	Name of table from which to select data
	 * @param string	Name of date field
	 * @param string	Extra SQL conditions
	 * @param string	SQL calculation function (COUNT(*), SUM(field_name)...)
	 *
	 * @return string
	 */
	protected function getBasicDataQuery($tableName, $dateField, $extraWhere = '', $calcFunction = 'COUNT(*)')
	{
		// for 2.0 add-ons using likes, silently convert the stats to reactions to avoid DB errors
		if ($tableName == 'xf_liked_content')
		{
			$tableName = 'xf_reaction_content';

			if ($dateField == 'like_date')
			{
				$dateField = 'reaction_date';
			}
			if ($extraWhere == 'content_type = ?')
			{
				$extraWhere = 'content_type = ? AND is_counted = 1';
			}
		}

		return '
			SELECT
				' . $dateField . ' - ' . $dateField . ' % 86400 AS unixDate,
				' . $calcFunction . '
			FROM ' . $tableName . '
			WHERE ' . $dateField . ' BETWEEN ? AND ?
			' . ($extraWhere ? 'AND ' . $extraWhere : '') . '
			GROUP BY unixDate
		';
	}
}