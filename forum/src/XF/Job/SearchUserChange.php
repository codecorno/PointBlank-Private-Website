<?php

namespace XF\Job;

class SearchUserChange extends AbstractJob
{
	protected $defaultData = [
		'user_id' => null,
		'before' => null,
		'batch' => 500,
		'none' => false
	];

	public function run($maxRunTime)
	{
		$userId = intval($this->data['user_id']);
		$before = intval($this->data['before']);

		if (!$userId)
		{
			return $this->complete();
		}

		$search = $this->app->search();

		$batchSize = max(1, intval($this->data['batch']));
		$s = microtime(true);

		$query = $search->getQuery();
		$query->byUserId($userId)
			->orderedBy('date');

		if ($before)
		{
			$query->olderThan($before);
		}

		$results = $search->search($query, $batchSize, false);
		if (!$results)
		{
			return $this->complete();
		}

		$newBefore = $before;
		$updated = 0;

		foreach ($results AS $result)
		{
			$type = $result[0];
			$id = $result[1];

			if (!$search->isValidContentType($type))
			{
				continue;
			}

			$handler = $search->handler($type);
			$entity = $handler->getContent($id);
			if (!$entity)
			{
				continue;
			}

			$date = $handler->getResultDate($entity);
			if (!$newBefore || $date < $newBefore)
			{
				$newBefore = $date;
			}

			$search->index($type, $entity);
			$updated++;

			if (microtime(true) - $s >= $maxRunTime)
			{
				break;
			}
		}

		if (!$updated || $before == $newBefore)
		{
			if ($this->data['none'])
			{
				// We didn't process anything on the last attempt and the same thing happened here,
				// so we're likely to run an infinite loop. Bail out and prevent that.
				return $this->complete();
			}

			$this->data['none'] = true;
		}
		else
		{
			$this->data['none'] = false;
		}

		if ($updated && !$newBefore)
		{
			// we found content that has a date of 0, have to break
			return $this->complete();
		}

		$this->data['before'] = $newBefore;

		return $this->resume();
	}

	public function getStatusMessage()
	{
		$actionPhrase = \XF::phrase('rebuilding');
		$typePhrase = \XF::phrase('search_index');
		return sprintf('%s... %s', $actionPhrase, $typePhrase);
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