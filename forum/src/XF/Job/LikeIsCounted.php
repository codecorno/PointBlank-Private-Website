<?php

namespace XF\Job;

class LikeIsCounted extends AbstractJob
{
	protected $defaultData = [
		'steps' => 0,
		'start' => 0,
		'batch' => 100,
		'type' => null,
		'ids' => null
	];

	public function run($maxRunTime)
	{
		$start = microtime(true);

		$this->data['steps']++;

		$db = $this->app->db();

		/** @var \XF\Repository\LikedContent $likeRepo */
		$likeRepo = $this->app->repository('XF:LikedContent');

		$likeHandler = $likeRepo->getLikeHandler($this->data['type']);
		if (!$likeHandler)
		{
			return $this->complete();
		}

		if (is_array($this->data['ids']))
		{
			if (!$this->data['ids'])
			{
				return $this->complete();
			}

			$idLimit = 'AND content_id IN (' . $db->quote($this->data['ids']) . ')';
		}
		else
		{
			$idLimit = '';
		}

		$likes = $db->fetchPairs($db->limit(
			"
				SELECT reaction_content_id, content_id
				FROM xf_reaction_content
				WHERE reaction_content_id > ?
					AND content_type = ?
					{$idLimit}
				ORDER BY reaction_content_id
			", $this->data['batch']
		), [$this->data['start'], $this->data['type']]);
		if (!$likes)
		{
			return $this->complete();
		}

		$likeRepo->recalculateLikeIsCounted($this->data['type'], $likes);
		$done = count($likes);
		$this->data['start'] = max(array_keys($likes));

		$this->data['batch'] = $this->calculateOptimalBatch($this->data['batch'], $done, $start, $maxRunTime, 1000);

		return $this->resume();
	}

	public function getStatusMessage()
	{
		$actionPhrase = \XF::phrase('rebuilding');
		$typePhrase = \XF::phrase('likes_counted_status');
		return sprintf('%s... %s (%s)', $actionPhrase, $typePhrase, $this->data['start']);
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