<?php

namespace XF\Job;

class UserRemoveReactions extends AbstractJob
{
	protected $defaultData = [
		'userId' => null,
		'cutOff' => null,
		'count' => 0,
		'total' => 0
	];

	public function run($maxRunTime)
	{
		$startTime = microtime(true);

		if (!$this->data['userId'] || $this->data['cutOff'] === null)
		{
			return $this->complete();
		}

		/** @var \XF\Repository\Reaction $reactionRepo */
		$reactionRepo = $this->app->repository('XF:Reaction');
		$reactionFinder = $reactionRepo->findReactionsByReactionUserId($this->data['userId'])
			->where('reaction_date', '>', $this->data['cutOff']);

		$count = $reactionFinder->total();
		if (!$count)
		{
			return $this->complete();
		}

		if (!$this->data['total'])
		{
			$this->data['total'] = $count;
		}

		foreach ($reactionFinder->fetch(500) AS $reaction)
		{
			try
			{
				$reaction->delete(false);
			}
			catch(\Exception $e) {}

			$this->data['count']++;

			if ($maxRunTime && microtime(true) - $startTime > $maxRunTime)
			{
				break;
			}
		}

		return $this->resume();
	}

	public function getStatusMessage()
	{
		return sprintf('%s... %s/%s', \XF::phrase('removing_reactions'), $this->data['count'], $this->data['total']);
	}

	public function canCancel()
	{
		return true;
	}

	public function canTriggerByChoice()
	{
		return false;
	}
}