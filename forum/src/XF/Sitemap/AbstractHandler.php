<?php

namespace XF\Sitemap;

use XF\App;

abstract class AbstractHandler
{
	protected $contentType;
	protected $app;

	public function __construct($contentType, App $app)
	{
		$this->contentType = $contentType;
		$this->app = $app;
	}

	protected function getIds($table, $column, $start, $limit = 2000)
	{
		$db = $this->app->db();

		$ids = $db->fetchAllColumn($db->limit(
			"
				SELECT $column
				FROM $table
				WHERE $column > ?
				ORDER BY $column
			", $limit
		), $start);

		return $ids;
	}

	abstract public function getRecords($start);
	abstract public function getEntry($record);

	/**
	 * Performs the base, global permission check before checking for records. This
	 * can be bypassed on a per-content basis if needed.
	 *
	 * @return bool
	 */
	public function basePermissionCheck()
	{
		return \XF::visitor()->hasPermission('general', 'view');
	}

	public function isIncluded($record)
	{
		return true;
	}
}