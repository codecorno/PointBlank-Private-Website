<?php

namespace XF\Job;

class ForumDelete extends AbstractJob
{
	protected $defaultData = [
		'node_id' => null,
		'count' => 0,
		'total' => null
	];

	public function run($maxRunTime)
	{
		$s = microtime(true);

		if (!$this->data['node_id'])
		{
			throw new \InvalidArgumentException('Cannot delete threads without a node_id.');
		}

		$threadFinder = $this->app->finder('XF:Thread')
			->where('node_id', $this->data['node_id']);

		if ($this->data['total'] === null)
		{
			$this->data['total'] = $threadFinder->total();
			if (!$this->data['total'])
			{
				return $this->complete();
			}
		}

		$threadIds = $threadFinder->pluckFrom('thread_id')->fetch(1000)->toArray();
		if (!$threadIds)
		{
			return $this->complete();
		}

		$continue = count($threadIds) < 1000 ? false : true;

		foreach ($threadIds AS $threadId)
		{
			$this->data['count']++;

			$thread = $this->app->find('XF:Thread', $threadId);
			if (!$thread)
			{
				continue;
			}
			$thread->delete(false);

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
		$typePhrase = \XF::phrase('threads');
		return sprintf('%s... %s (%s/%s)', $actionPhrase, $typePhrase,
			\XF::language()->numberFormat($this->data['count']), \XF::language()->numberFormat($this->data['total'])
		);
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