<?php

namespace XF\Job;

class Sitemap extends AbstractJob
{
	protected $defaultData = [
		'state' => null
	];

	public function run($maxRunTime)
	{
		$s = microtime(true);

		$builder = $this->app->sitemapBuilder();
		if ($this->data['state'] && is_array($this->data['state']))
		{
			$builder->setupFromJobData($this->data['state']);
		}

		$timeRemaining = $maxRunTime;

		do
		{
			$hasMore = $builder->build($timeRemaining);

			$this->data['state'] = $builder->getDataForJob();
			$this->saveIncrementalData();

			// we need to throw away the previous entity cache to prevent potential memory issues
			$this->app->em()->clearEntityCache();

			$timeRemaining = $maxRunTime - (microtime(true) - $s);
		}
		while ($hasMore && $timeRemaining >= 0.5);

		if (!$hasMore)
		{
			return $this->complete();
		}

		return $this->resume();
	}

	public function getStatusMessage()
	{
		$actionPhrase = \XF::phrase('rebuilding');
		$typePhrase = \XF::phrase('sitemap');

		$state = $this->data['state'];

		if (!empty($state['finalizing']))
		{
			$phrase = \XF::phrase('finalizing');
			$text = "$phrase - $state[finalize_file]";

			return sprintf('%s... %s (%s)', $actionPhrase, $typePhrase, $text);
		}

		if (!empty($state['current_type']))
		{
			$contentType = $this->app->getContentTypePhrase($state['current_type'], true);
			$text = "$contentType $state[last_type_id]";

			return sprintf('%s... %s (%s)', $actionPhrase, $typePhrase, $text);
		}

		return sprintf('%s... %s', $actionPhrase, $typePhrase);
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