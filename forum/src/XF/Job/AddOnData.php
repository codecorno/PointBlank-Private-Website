<?php

namespace XF\Job;

class AddOnData extends AbstractJob
{
	protected $defaultData = [
		'addon_id' => null,
		'data_dir' => null,

		'data_types' => null,
		'current_type' => null,

		'start' => 0
	];

	protected $currentType = null;
	protected $lastIndex = 0;
	protected $totalEntries = 0;

	public function run($maxRunTime)
	{
		if ($this->data['addon_id'] != 'XF')
		{
			$addOnManager = $this->app->addOnManager();
			$addOn = $addOnManager->getById($this->data['addon_id']);
			if (!$addOn)
			{
				// add-on isn't installed, can't do anything
				return $this->complete();
			}

			if ($this->data['data_dir'] === null)
			{
				$this->data['data_dir'] = $addOn->getDataDirectory();
			}
		}
		else
		{
			$addOn = null; // don't populate this for XF since it wouldn't be the same type of object

			if ($this->data['data_dir'] === null)
			{
				$ds = \XF::$DS;
				$this->data['data_dir'] = \XF::getAddOnDirectory() . $ds . 'XF' . $ds . '_data';
			}
		}

		$dataManager = $this->app->addOnDataManager();

		$addOnId = $this->data['addon_id'];
		$dataDir = $this->data['data_dir'];

		if ($this->data['data_types'] === null)
		{
			$dataTypes = $dataManager->getDataTypeClasses();
			foreach ($dataTypes AS $key => $dataType)
			{
				$handler = $dataManager->getDataTypeHandler($dataType);
				$xml = $handler->openTypeFile($dataDir);
				if (!$xml || !$xml->count())
				{
					// We can skip this type entirely, but clean up any existing data first
					$handler->deleteAddOnData($addOnId);
					unset($dataTypes[$key]);
				}
			}
			$this->data['data_types'] = $dataTypes;
		}

		if (!$this->data['current_type'])
		{
			$this->data['current_type'] = array_shift($this->data['data_types']);
			if (!$this->data['current_type'])
			{
				if ($addOn)
				{
					$addOn->postDataImport();
				}

				return $this->complete();
			}

			$this->data['start'] = 0;
		}

		$start = $this->data['start'];
		$type = $this->data['current_type'];

		$handler = $dataManager->getDataTypeHandler($type);

		$this->currentType = $handler->getContainerTag();
		$this->lastIndex = $start;

		$xml = $handler->openTypeFile($dataDir);
		if (!$xml || !$xml->count())
		{
			// No data so skip and delete existing data for type (if any)
			$this->data['current_type'] = null;
			$handler->deleteAddOnData($addOnId);
			return $this->resume();
		}

		$this->totalEntries = $xml->count();

		$last = $handler->importAddOnData($addOnId, $xml, $start, $maxRunTime);
		if (!$last)
		{
			// finished type, delete any existing data not in the XML file for this type
			$handler->deleteOrphanedAddOnData($addOnId, $xml);

			\XF::triggerRunOnce();
			$this->data['current_type'] = null;
			return $this->resume();
		}

		$this->data['start'] = $last;
		return $this->resume();
	}

	public function getStatusMessage()
	{
		// TODO: These (and the types) probably should be phrased
		$actionPhrase = 'Importing';
		if ($this->data['addon_id'] == 'XF')
		{
			$typePhrase = 'Master data';
		}
		else
		{
			$typePhrase = 'Add-on data';
		}

		$currentType = ucfirst(str_replace('_', ' ', $this->currentType));

		if ($currentType && $this->lastIndex)
		{
			$percentage = $this->app->language()->numberFormat(($this->lastIndex / $this->totalEntries) * 100, 1);
			return sprintf('%s... %s (%s: %s%%)', $actionPhrase, $typePhrase, $currentType, $percentage);
		}
		else if ($currentType)
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