<?php

namespace XF\Job;

class ReactionIsCounted extends AbstractJob
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

		/** @var \XF\Repository\Reaction $reactionRepo */
		$reactionRepo = $this->app->repository('XF:Reaction');

		$reactionHandler = $reactionRepo->getReactionHandler($this->data['type']);
		if (!$reactionHandler)
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

		$reactions = $db->fetchPairs($db->limit(
			"
				SELECT reaction_content_id, content_id
				FROM xf_reaction_content
				WHERE reaction_content_id > ?
					AND content_type = ?
					{$idLimit}
				ORDER BY reaction_content_id
			", $this->data['batch']
		), [$this->data['start'], $this->data['type']]);
		if (!$reactions)
		{
			return $this->complete();
		}

		$reactionRepo->recalculateReactionIsCounted($this->data['type'], $reactions);
		$done = count($reactions);
		$this->data['start'] = max(array_keys($reactions));

		$this->data['batch'] = $this->calculateOptimalBatch($this->data['batch'], $done, $start, $maxRunTime, 1000);

		return $this->resume();
	}

	public function getStatusMessage()
	{
		$actionPhrase = \XF::phrase('rebuilding');
		$typePhrase = \XF::phrase('reactions_counted_status');
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