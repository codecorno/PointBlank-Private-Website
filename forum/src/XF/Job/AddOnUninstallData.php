<?php

namespace XF\Job;

use XF\AddOn\DataType\AbstractDataType;

class AddOnUninstallData extends AbstractJob
{
	protected $defaultData = [
		'addon_id' => null,
		'data_types' => null,
		'current_type' => null,
	];

	protected $currentType = null;

	public function run($maxRunTime)
	{
		if ($this->data['addon_id'] == 'XF')
		{
			return $this->complete();
		}

		$dataManager = $this->app->addOnDataManager();
		$addOnId = $this->data['addon_id'];

		if ($this->data['data_types'] === null)
		{
			$dataTypes = $dataManager->getDataTypeClasses();
			$this->data['data_types'] = $dataTypes;
		}

		$start = microtime(true);

		do
		{
			if (!$this->data['current_type'])
			{
				$this->data['current_type'] = array_shift($this->data['data_types']);
				if (!$this->data['current_type'])
				{
					$dataManager->finalizeRemoveAddOnData($addOnId);

					return $this->complete();
				}
			}

			$type = $this->data['current_type'];
			$this->currentType = $type;

			$handler = $dataManager->getDataTypeHandler($type);

			$timeLeft = max(1, $maxRunTime - (microtime(true) - $start));
			$isComplete = $handler->deleteAddOnData($addOnId, $timeLeft);
			if ($isComplete)
			{
				$this->data['current_type'] = null;
			}

			\XF::triggerRunOnce();

			if (microtime(true) - $start >= $maxRunTime)
			{
				break;
			}
		}
		while ($this->data['data_types']);

		return $this->resume();
	}

	public function getStatusMessage()
	{
		// TODO: These (and the types) probably should be phrased
		$actionPhrase = 'Deleting';
		$typePhrase = 'Add-on data';

		$currentType = ucfirst(str_replace('_', ' ', $this->currentType));

		 if ($currentType)
		{
			return sprintf('%s... %s (%s)', $actionPhrase, $typePhrase, $currentType);
		}
		else
		{
			return sprintf('%s... %s', $actionPhrase, $typePhrase);
		}
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