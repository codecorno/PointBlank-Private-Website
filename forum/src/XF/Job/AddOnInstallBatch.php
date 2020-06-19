<?php

namespace XF\Job;

use XF\Util\File;

class AddOnInstallBatch extends AbstractJob
{
	protected $defaultData = [
		'batch_id' => 0,

		// array in format of [$addOnId => true] where $addOnIds
		// will skip hash checks and overwrite all files
		'force_overwrite' => [],

		'addon_ids' => null,

		'current_addon_id' => null,
		'current_data' => [],
		'current_step' => null,
		'current_step_params' => [],
		'current_ticks' => 0,

		'results' => [],
	];

	/**
	 * @var \XF\Entity\AddOnInstallBatch
	 */
	protected $batch;

	/**
	 * @var \XF\AddOn\AddOn
	 */
	protected $currentExistingAddOn;

	public function run($maxRunTime)
	{
		$timer = new \XF\Timer($maxRunTime);

		// make sure any errors get logged here
		$this->app->error()->setIgnorePendingUpgrade(true);

		/** @var \XF\Entity\AddOnInstallBatch $batch */
		$batch = $this->app->em()->find('XF:AddOnInstallBatch', $this->data['batch_id']);
		if (!$batch)
		{
			return $this->complete();
		}

		$this->batch = $batch;

		if ($this->data['addon_ids'] === null)
		{
			$this->data['addon_ids'] = array_keys($batch->addon_ids);
		}

		if (!$this->data['addon_ids'] && !$this->data['current_addon_id'])
		{
			$this->finalizeBatch();
			return $this->complete();
		}

		if (!$this->data['current_addon_id'])
		{
			$this->setupNextAddOn();
		}

		$this->currentExistingAddOn = $this->app->addOnManager()->getById($this->data['current_addon_id']);

		$this->data['current_ticks']++;

		try
		{
			switch ($this->data['current_step'])
			{
				case 'init':
					$this->stepInit($timer);
					break;

				case 'copy':
					$this->stepCopy($timer);
					break;

				case 'pre_action':
					$this->stepPreAction($timer);
					break;

				case 'action';
					$this->stepAction($timer);
					break;

				case 'data':
					$this->stepData($timer);
					break;

				case 'finalize';
					$this->stepFinalize($timer);
					break;

				default:
					throw new \LogicException("Unknown current step: " . $this->data['current_step']);
			}
		}
		catch (\Exception $e)
		{
			\XF::logException($e, true, "Batch install error: ");

			$this->logErrorAndReset("Exception: " . $e->getMessage());
		}

		if (!$this->data['current_addon_id'] || !$this->data['current_step'])
		{
			$this->setupNextAddOn();

			if (!$this->data['current_addon_id'])
			{
				$this->finalizeBatch();
				return $this->complete();
			}
		}

		return $this->resume();
	}

	protected function stepInit(\XF\Timer $timer)
	{
		$addOnId = $this->data['current_addon_id'];
		$this->data['results'][$addOnId] = [
			'status' => null,
			'error' => null,
			'action' => null,
			'old_version' => $this->currentExistingAddOn ? $this->currentExistingAddOn->version_string : null
		];

		$zipFile = File::copyAbstractedPathToTempFile($this->batch->getAbstractedAddOnBatchPath($addOnId), false);
		$this->data['current_data']['file'] = $zipFile;
		$this->data['current_data']['file_temp'] = true;

		/** @var \XF\Service\AddOnArchive\Extractor $extractor */
		$extractor = $this->app->service('XF:AddOnArchive\Extractor', $addOnId, $zipFile);
		if ($this->currentExistingAddOn && empty($this->data['force_overwrite'][$addOnId]))
		{
			$existingHashes = $this->currentExistingAddOn->getHashes();
			$hashChanges = $extractor->compareHashes($existingHashes);
		}
		else
		{
			$hashChanges = null;
		}

		$this->data['current_data']['hash_changes'] = $hashChanges;

		if (!$extractor->checkWritable($hashChanges, $failures))
		{
			$failedSubset = array_slice($failures, 0, 5);
			\XF::logError("Failed to write files for $addOnId action, including " . implode(', ', $failedSubset));

			$this->logErrorAndReset("Not all files are writable (example: $failures[0]). Cannot continue.");
		}

		$this->changeStep('copy');
	}

	protected function stepCopy(\XF\Timer $timer)
	{
		$params = array_replace([
			'start' => 0
		], $this->data['current_step_params']);

		$addOnId = $this->data['current_addon_id'];
		$zipFile = $this->data['current_data']['file'];
		$hashChanges = $this->data['current_data']['hash_changes'];

		/** @var \XF\Service\AddOnArchive\Extractor $extractor */
		$extractor = $this->app->service('XF:AddOnArchive\Extractor', $addOnId, $zipFile);
		$result = $extractor->copyFiles($hashChanges, $params['start'], $timer);

		switch ($result['status'])
		{
			case 'error':
				$this->logErrorAndReset($result['error']);
				break;

			case 'incomplete':
				$params['start'] = $result['last'] + 1;
				$this->data['current_step_params'] = $params;
				break;

			case 'complete':
				\XF\Util\Php::resetOpcache();
				$this->changeStep('pre_action');
				break;

			default:
				throw new \LogicException("Unknown result from copy '$result[status]'");
		}
	}

	protected function stepPreAction(\XF\Timer $timer)
	{
		if (!$this->currentExistingAddOn)
		{
			throw new \LogicException("Add-on should be available");
		}

		$addOn = $this->currentExistingAddOn;
		$title = $addOn->title;

		$action = null;
		if ($addOn->canInstall())
		{
			// do this first, technically, getInstalledAddOn can return something but where we haven't completed
			// the install steps yet
			$action = 'install';
		}
		else if ($addOn->getInstalledAddOn())
		{
			if ($addOn->canUpgrade())
			{
				$action = 'upgrade';
			}
			else if ($addOn->canRebuild())
			{
				$action = 'rebuild';
			}
		}

		if (!$action)
		{
			$this->logErrorAndReset("No completable action available.");
			return;
		}

		$addOn->checkRequirements($errors, $warnings);
		if ($errors)
		{
			$this->logErrorAndReset(
				\XF::phrase('cannot_proceed_with_installation_upgrade_of_x_because_minimum_requirements', ['addOnTitle' => $title])
			);
			return;
		}

		// Note: this is intentionally bypassed because the hash comparison method means that we only update
		// changed files. If someone has edited a file that the upgrade doesn't change, that will be maintained
		// and thus could cause a health check failure. This can be revisited if needed.
		/*if ($addOn->getHashes())
		{
			$addOn->passesHealthCheck($missing, $inconsistent);
			if ($missing || $inconsistent)
			{
				$this->logErrorAndReset(
					\XF::phrase('cannot_proceed_with_installation_upgrade_of_x_because_files_not_uploaded', ['addOnTitle' => $title])
				);
				return;
			}
		}*/

		$method = 'pre' . ucfirst($action);
		$addOn->{$method}();

		$this->data['current_data']['action'] = $action;

		$this->changeStep('action');
	}

	protected function stepAction(\XF\Timer $timer)
	{
		if (empty($this->data['current_data']['action']))
		{
			throw new \LogicException("No action available");
		}
		if (!$this->currentExistingAddOn)
		{
			throw new \LogicException("Add-on should be available");
		}

		\XF::app()->error()->setIgnorePendingUpgrade(true);

		$params = array_replace([
			'step_params' => []
		], $this->data['current_step_params']);

		$setup = $this->currentExistingAddOn->getSetup();
		if ($setup)
		{
			$action = $this->data['current_data']['action'];

			$setup->prepareForAction($action);
			switch ($action)
			{
				case 'install';
					$result = $setup->install($params['step_params']);
					break;

				case 'upgrade':
					$result = $setup->upgrade($params['step_params']);
					break;

				default:
					$result = null;
			}
		}
		else
		{
			$result = null;
		}

		if (!$result)
		{
			$this->changeStep('data');
			return;
		}

		$params['step_params'] = $result->params;
		$params['step_params']['step'] = $result->step;
		if ($result->version)
		{
			$params['step_params']['version_id'] = $result->version;
		}
		$this->data['current_step_params'] = $params;
	}

	protected function stepData(\XF\Timer $timer)
	{
		$params = array_replace([
			'started' => false,
			'data' => null,
			'message' => null
		], $this->data['current_step_params']);

		if (!$params['started'])
		{
			$params['data'] = ['addon_id' => $this->data['current_addon_id']];
			$params['started'] = true;
		}
		$job = $this->app->job('XF:AddOnData', 0, $params['data']);

		$result = $job->run($timer->remaining() ?: 2);
		if ($result->completed)
		{
			$this->changeStep('finalize');
		}
		else
		{
			$params['data'] = $result->data;
			$params['message'] = $result->statusMessage;
			$this->data['current_step_params'] = $params;
		}
	}

	protected function stepFinalize(\XF\Timer $timer)
	{
		if (empty($this->data['current_data']['action']))
		{
			throw new \LogicException("No action available");
		}
		if (!$this->currentExistingAddOn)
		{
			throw new \LogicException("Add-on should be available");
		}

		$addOn = $this->currentExistingAddOn;

		$installed = $addOn->getInstalledAddOn();
		if ($installed)
		{
			// this is a sanity check, it shouldn't happen
			$installed->is_processing = false;
			$installed->save();
		}

		$null = [];

		switch ($this->data['current_data']['action'])
		{
			case 'upgrade':
				$addOn->postUpgrade($null);
				break;

			case 'install':
				$addOn->postInstall($null);
				break;

			case 'rebuild':
				$addOn->postRebuild();
				break;

			// default shouldn't really happen here
		}

		$addOnId = $this->data['current_addon_id'];
		$this->data['results'][$addOnId]['status'] = 'success';
		$this->data['results'][$addOnId]['action'] = $this->data['current_data']['action'];

		$this->changeStep(''); // this will setup the next add-on, but keep the current_addon_id for now
	}

	protected function setupNextAddOn()
	{
		if ($this->data['current_addon_id'])
		{
			$currentData = $this->data['current_data'];
			if (!empty($currentData['file']) && !empty($currentData['file_temp']))
			{
				@unlink($currentData['file']);
			}
		}

		$this->data['current_addon_id'] = null;
		$this->data['current_data'] = [];
		$this->data['current_step'] = null;
		$this->data['current_step_params'] = [];
		$this->data['current_ticks'] = 0;

		if ($this->data['addon_ids'])
		{
			$addOnId = array_shift($this->data['addon_ids']);

			$this->data['current_addon_id'] = $addOnId;
			$this->changeStep('init');
		}
	}

	protected function changeStep($name, array $params = [])
	{
		$this->data['current_step'] = $name;
		$this->data['current_step_params'] = $params;
	}


	protected function logErrorAndReset($error)
	{
		$addOnId = $this->data['current_addon_id'];
		if ($addOnId)
		{
			$this->data['results'][$addOnId]['status'] = 'error';
			$this->data['results'][$addOnId]['error'] = strval($error);
		}

		$this->setupNextAddOn();
	}

	protected function finalizeBatch()
	{
		$batch = $this->batch;
		$batch->complete_date = time();
		$batch->results = $this->data['results'];
		$batch->save();
	}

	public function getStatusMessage()
	{
		$addOnId = $this->data['current_addon_id'];

		if (!$addOnId || !$this->data['current_step'])
		{
			return \XF::phrase('processing...');
		}

		switch ($this->data['current_step'])
		{
			case 'init': $message = \XF::phrase('copying_files...'); break;
			case 'copy': $message = \XF::phrase('copying_files...'); break;
			case 'data': $message = \XF::phrase('importing...'); break;
			case 'finalize': $message = \XF::phrase('finalizing...'); break;

			case 'pre_action':
			case 'action':
				if (empty($this->data['current_data']['action']))
				{
					$message = \XF::phrase('running_setup...');
				}
				else
				{
					switch ($this->data['current_data']['action'])
					{
						case 'install': $message = \XF::phrase('installing...'); break;
						case 'upgrade': $message = \XF::phrase('upgrading...'); break;
						case 'rebuild': $message = \XF::phrase('rebuilding...'); break;
						default: $message = \XF::phrase('running_setup...');
					}
				}
				break;


			default: $message = $this->data['current_step'];
		}

		return "$addOnId - $message " . str_repeat('. ', $this->data['current_ticks']);
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