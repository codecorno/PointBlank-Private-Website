<?php

namespace XF\Job;

class SearchRebuild extends AbstractJob
{
	protected $defaultData = [
		'type' => null,
		'rebuild_types' => null,
		'start' => 0,
		'batch' => 500,
		'delay' => 0,
		'truncate' => false,
	];

	protected $builtType;
	protected $builtLast;

	public function run($maxRunTime)
	{
		$search = $this->app->search();

		if (!is_array($this->data['rebuild_types']) && $this->data['truncate'])
		{
			$search->truncate($this->data['type']);
			$this->data['truncate'] = false;
		}

		if (!is_array($this->data['rebuild_types']))
		{
			$this->data['rebuild_types'] = $this->data['type'] ? [$this->data['type']] : $search->getAvailableTypes();
			$this->data['type'] = null;
		}

		if (!$this->data['type'])
		{
			$this->data['type'] = array_shift($this->data['rebuild_types']);
			if (!$this->data['type'])
			{
				return $this->complete();
			}

			$this->data['start'] = 0;
		}

		$type = $this->data['type'];
		$start = $this->data['start'];

		if (!$search->isValidContentType($type))
		{
			$this->data['type'] = null;
			return $this->resume();
		}

		$this->builtType = $this->data['type'];

		$last = $search->indexRange($this->data['type'], $start, $this->data['batch']);
		if (!$last)
		{
			// done this type
			$this->data['type'] = null;
			return $this->resume();
		}

		$this->builtLast = $last;

		$this->data['start'] = $last;
		return $this->resume();
	}

	public function getStatusMessage()
	{
		$actionPhrase = \XF::phrase('rebuilding');
		$typePhrase = \XF::phrase('search_index');
		if ($this->builtType && $this->builtLast)
		{
			return sprintf('%s... %s (%s %s)', $actionPhrase, $typePhrase, $this->app->getContentTypePhrase($this->builtType), $this->builtLast);
		}
		else
		{
			return sprintf('%s... %s', $actionPhrase, $typePhrase);
		}
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