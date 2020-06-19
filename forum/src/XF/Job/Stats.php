<?php

namespace XF\Job;

class Stats extends AbstractJob
{
	protected $defaultData = [
		'position' => 0,
		'batch' => 28,
		'delete' => false
	];

	public function run($maxRunTime)
	{
		$db = $this->app->db();

		if ($this->data['position'] == 0)
		{
			// delete old stats cache if required
			if ($this->data['delete'])
			{
				$db->emptyTable('xf_stats_daily');
			}

			// an appropriate date from which to start... first thread, or earliest user reg?
			$this->data['position'] = min(
				$db->fetchOne('SELECT MIN(post_date) FROM xf_thread') ?: \XF::$time,
				$db->fetchOne('SELECT MIN(register_date) FROM xf_user') ?: \XF::$time
			);

			// start on a 24 hour increment point
			$this->data['position'] = $this->data['position'] - $this->data['position'] % 86400;
		}
		else if ($this->data['position'] > \XF::$time)
		{
			return $this->complete();
		}

		$end = $this->data['position'] + $this->data['batch'] * 86400;

		/** @var \XF\Repository\Stats $statsRepo */
		$statsRepo = $this->app->repository('XF:Stats');
		$statsRepo->build($this->data['position'], $end);

		$this->data['position'] = $end;

		return $this->resume();
	}

	public function getStatusMessage()
	{
		$actionPhrase = \XF::phrase('rebuilding');
		$typePhrase = \XF::phrase('daily_statistics');
		return sprintf('%s... %s (%s)', $actionPhrase, $typePhrase, \XF::language()->date($this->data['position'], 'absolute'));
	}

	public function canCancel()
	{
		return true;
	}

	public function canTriggerByChoice()
	{
		return true;
	}
}