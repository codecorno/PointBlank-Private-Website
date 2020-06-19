<?php

namespace XF\Job;

class SearchIndex extends AbstractJob
{
	protected $defaultData = [
		'content_type' => null,
		'content_ids' => [],
		'batch' => 50
	];

	public function run($maxRunTime)
	{
		$type = $this->data['content_type'];
		$contentIds = $this->data['content_ids'];

		if (!$type || !$contentIds)
		{
			return $this->complete();
		}

		if (!is_array($contentIds))
		{
			$contentIds = [$contentIds];
		}

		$search = $this->app->search();

		if (!$search->isValidContentType($type))
		{
			return $this->complete();
		}

		$batchSize = max(1, intval($this->data['batch']));
		$s = microtime(true);

		do
		{
			$batch = array_slice($contentIds, 0, $batchSize);
			$contentIds = array_slice($contentIds, count($batch));

			$search->indexByIds($this->data['content_type'], $batch);

			if (microtime(true) - $s >= $maxRunTime)
			{
				break;
			}
		}
		while ($contentIds);

		if (!$contentIds)
		{
			return $this->complete();
		}

		$this->data['content_ids'] = $contentIds;

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