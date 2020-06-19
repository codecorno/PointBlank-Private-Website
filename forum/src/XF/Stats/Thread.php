<?php

namespace XF\Stats;

class Thread extends AbstractHandler
{
	public function getStatsTypes()
	{
		return [
			'thread' => \XF::phrase('threads')
		];
	}

	public function getData($start, $end)
	{
		$threads = $this->db()->fetchPairs(
			$this->getBasicDataQuery('xf_thread', 'post_date', 'discussion_state = ?'),
			[$start, $end, 'visible']
		);

		return [
			'thread' => $threads
		];
	}
}