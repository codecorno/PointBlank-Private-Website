<?php

namespace XF\Job;

class Forum extends AbstractRebuildJob
{
	protected function getNextIds($start, $batch)
	{
		$db = $this->app->db();

		return $db->fetchAllColumn($db->limit(
			"
				SELECT node_id
				FROM xf_forum
				WHERE node_id > ?
				ORDER BY node_id
			", $batch
		), $start);
	}

	protected function rebuildById($id)
	{
		/** @var \XF\Entity\Forum $forum */
		$forum = $this->app->em()->find('XF:Forum', $id);
		if ($forum)
		{
			$forum->rebuildCounters();
			$forum->save();
		}
	}

	protected function getStatusType()
	{
		return \XF::phrase('forums');
	}
}