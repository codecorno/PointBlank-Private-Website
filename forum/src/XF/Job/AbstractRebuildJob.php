<?php

namespace XF\Job;

abstract class AbstractRebuildJob extends AbstractJob
{
	protected $rebuildDefaultData = [
		'steps' => 0,
		'start' => 0,
		'batch' => 100,
	];

	abstract protected function getNextIds($start, $batch);
	abstract protected function rebuildById($id);
	abstract protected function getStatusType();

	protected function setupData(array $data)
	{
		$this->defaultData = array_merge($this->rebuildDefaultData, $this->defaultData);

		return parent::setupData($data);
	}

	public function run($maxRunTime)
	{
		$startTime = microtime(true);

		$this->data['steps']++;

		$ids = $this->getNextIds($this->data['start'], $this->data['batch']);
		if (!$ids)
		{
			return $this->complete();
		}

		$done = 0;

		foreach ($ids AS $id)
		{
			if (microtime(true) - $startTime >= $maxRunTime)
			{
				break;
			}

			$this->data['start'] = $id;

			$this->rebuildById($id);

			$done++;
		}

		$this->data['batch'] = $this->calculateOptimalBatch($this->data['batch'], $done, $startTime, $maxRunTime, 1000);

		return $this->resume();
	}

	public function getStatusMessage()
	{
		$actionPhrase = \XF::phrase('rebuilding');
		$typePhrase = $this->getStatusType();
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