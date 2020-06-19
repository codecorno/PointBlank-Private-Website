<?php

namespace XF\Job;

class ReactionDelete extends AbstractJob
{
	protected $defaultData = [
		'reaction_id' => null,
		'reaction_score' => 0,
		'count' => 0,
		'total' => null
	];

	public function run($maxRunTime)
	{
		$s = microtime(true);

		if (!$this->data['reaction_id'])
		{
			throw new \InvalidArgumentException('Cannot delete without a reaction_id.');
		}

		$reactionFinder = $this->app->finder('XF:ReactionContent')
			->where('reaction_id', $this->data['reaction_id']);

		if ($this->data['total'] === null)
		{
			$this->data['total'] = $reactionFinder->total();
			if (!$this->data['total'])
			{
				return $this->complete();
			}
		}

		$maxFetch = 1000;

		// note that the order doesn't matter here -- we're deleting every entry
		$reactionContents = $reactionFinder->fetch($maxFetch);
		$continue = $reactionContents->count() < $maxFetch ? false : true;

		foreach ($reactionContents AS $reactionContent)
		{
			/** @var \XF\Entity\ReactionContent $reactionContent */
			$this->data['count']++;

			// the base reaction is gone, so we need to force this to recalculate properly
			$reactionContent->setOption('force_reaction_score', $this->data['reaction_score']);
			$reactionContent->delete(false);

			if ($maxRunTime && microtime(true) - $s > $maxRunTime)
			{
				$continue = true;
				break;
			}
		}

		if ($continue)
		{
			return $this->resume();
		}
		else
		{
			return $this->complete();
		}
	}

	public function getStatusMessage()
	{
		$actionPhrase = \XF::phrase('deleting');
		$typePhrase = \XF::phrase('reactions');
		return sprintf('%s... %s (%s/%s)', $actionPhrase, $typePhrase,
			\XF::language()->numberFormat($this->data['count']), \XF::language()->numberFormat($this->data['total'])
		);
	}

	public function canCancel()
	{
		return false;
	}

	public function canTriggerByChoice()
	{
		return false;
	}
}