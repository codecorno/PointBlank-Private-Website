<?php

namespace XF\Job;

class ThreadAction extends AbstractJob
{
	protected $defaultData = [
		'start' => 0,
		'count' => 0,
		'total' => null,
		'criteria' => null,
		'threadIds' => null,
		'actions' => []
	];

	public function run($maxRunTime)
	{
		if (is_array($this->data['criteria']) && is_array($this->data['threadIds']))
		{
			throw new \LogicException("Cannot have both criteria and threadIds values; one must be null");
		}

		$startTime = microtime(true);
		$em = $this->app->em();

		$ids = $this->prepareThreadIds();
		if (!$ids)
		{
			return $this->complete();
		}

		$db = $this->app->db();
		$db->beginTransaction();

		$limitTime = ($maxRunTime > 0);
		foreach ($ids AS $key => $id)
		{
			$this->data['count']++;
			$this->data['start'] = $id;
			unset($ids[$key]);

			/** @var \XF\Entity\Thread $thread */
			$thread = $em->find('XF:Thread', $id);
			if ($thread)
			{
				if ($this->getActionValue('delete'))
				{
					$thread->delete(false, false);
					continue; // no further action required
				}

				$this->applyInternalThreadChange($thread);
				$thread->save(false, false);

				$this->applyExternalThreadChange($thread);
			}

			if ($limitTime && microtime(true) - $startTime > $maxRunTime)
			{
				break;
			}
		}

		if (is_array($this->data['threadIds']))
		{
			$this->data['threadIds'] = $ids;
		}

		$db->commit();

		return $this->resume();
	}

	protected function getActionValue($action)
	{
		$value = null;
		if (!empty($this->data['actions'][$action]))
		{
			$value = $this->data['actions'][$action];
		}
		return $value;
	}

	protected function prepareThreadIds()
	{
		if (is_array($this->data['criteria']))
		{
			$searcher = $this->app->searcher('XF:Thread', $this->data['criteria']);
			$results = $searcher->getFinder()
				->where('thread_id', '>', $this->data['start'])
				->order('thread_id')
				->limit(1000)
				->fetchColumns('thread_id');
			$ids = array_column($results, 'thread_id');
		}
		else if (is_array($this->data['threadIds']))
		{
			$ids = $this->data['threadIds'];
		}
		else
		{
			$ids = [];
		}
		sort($ids, SORT_NUMERIC);
		return $ids;
	}

	protected function applyInternalThreadChange(\XF\Entity\Thread $thread)
	{
		if ($nodeId = $this->getActionValue('node_id'))
		{
			$thread->node_id = $nodeId;
		}

		if ($this->getActionValue('apply_thread_prefix'))
		{
			$thread->prefix_id = intval($this->getActionValue('prefix_id'));
		}

		if ($this->getActionValue('stick'))
		{
			$thread->sticky = true;
		}
		if ($this->getActionValue('unstick'))
		{
			$thread->sticky = false;
		}

		if ($this->getActionValue('lock'))
		{
			$thread->discussion_open = false;
		}
		if ($this->getActionValue('unlock'))
		{
			$thread->discussion_open = true;
		}

		if ($this->getActionValue('approve'))
		{
			$thread->discussion_state = 'visible';
		}
		if ($this->getActionValue('unapprove'))
		{
			$thread->discussion_state = 'moderated';
		}

		if ($this->getActionValue('soft_delete'))
		{
			$thread->discussion_state = 'deleted';
		}
	}

	protected function applyExternalThreadChange(\XF\Entity\Thread $thread)
	{
	}

	public function getStatusMessage()
	{
		$actionPhrase = \XF::phrase('updating');
		$typePhrase = \XF::phrase('threads');

		if ($this->data['total'] !== null)
		{
			return sprintf('%s... %s (%d/%d)', $actionPhrase, $typePhrase, $this->data['count'], $this->data['total']);
		}
		else
		{
			return sprintf('%s... %s (%d)', $actionPhrase, $typePhrase, $this->data['start']);
		}
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