<?php

namespace XF\Job;

use XF\Util\File;
use XF\Util\Json;

class FileCheck extends AbstractJob
{
	protected $defaultData = [
		'addon_ids' => null,
		'hash_files' => null,

		'current_addon_id' => null,
		'current_hash_file' => null,

		'steps' => 0,
		'start' => 0,
		'initialized' => false,

		'check_id' => 0,
		'automated' => false
	];

	protected $currentAddOn;

	public function run($maxRunTime)
	{
		$startTime = microtime(true);

		$fs = $this->app->fs();

		/** @var \XF\Entity\FileCheck $fileCheck */
		$fileCheck = $this->app->em()->find('XF:FileCheck', $this->data['check_id']);
		if (!$fileCheck)
		{
			throw new \InvalidArgumentException('Cannot perform a file health check without an associated file check record.');
		}

		if ($this->data['addon_ids'] === null) // run on all add-on IDs
		{
			$this->data['addon_ids'] = $this->app->db()->fetchAllColumn('
				SELECT addon_id
				FROM xf_addon
			');
		}
		else if (!is_array($this->data['addon_ids'])) // run on a specific add-on ID
		{
			$this->data['addon_ids'] = [$this->data['addon_ids']];
		}

		$addOnManager = $this->app->addOnManager();
		$ds = \XF::$DS;

		if ($this->data['hash_files'] === null)
		{
			$this->data['hash_files'] = [];
			foreach ($this->data['addon_ids'] AS $addOnId)
			{
				if ($addOnId == 'XF')
				{
					$this->data['hash_files']['XF'] = \XF::getAddOnDirectory() . $ds . 'XF' . $ds . 'hashes.json';
				}
				else
				{
					$addOn = $addOnManager->getById($addOnId);
					if ($addOn)
					{
						$this->data['hash_files'][$addOnId] = $addOn->getHashesPath();
					}
				}
			}
		}

		if (!$this->data['initialized'])
		{
			$results = [
				'missing' => [],
				'inconsistent' => [],
				'total_missing' => 0,
				'total_inconsistent' => 0,
				'total_checked' => 0,
			];

			$this->data['initialized'] = true;
		}
		else
		{
			$tempPath = $fileCheck->getAbstractedCheckPath(true);

			if (!$fs->has($tempPath))
			{
				// somewhat unrecoverable state which generally shouldn't happen so silently fail
				// the file check state will remain as pending so as to indicate a problem.
				return $this->complete();
			}

			$results = json_decode($fs->read($tempPath), true);
		}

		if (!$this->data['current_addon_id'])
		{
			$addOnId = array_shift($this->data['addon_ids']);
			$this->data['current_addon_id'] = $addOnId;

			if (!$addOnId)
			{
				$this->completeFileCheck($fileCheck, $results);

				return $this->complete();
			}

			if (isset($this->data['hash_files'][$addOnId]))
			{
				$hashFile = $this->data['hash_files'][$addOnId];
			}
			else
			{
				$hashFile = null;
			}

			$this->data['current_hash_file'] = $hashFile;
			$this->data['steps'] = 0;
			$this->data['start'] = 0;
		}

		if ($this->data['current_addon_id'] == 'XF')
		{
			$this->currentAddOn = 'XenForo';
		}
		else
		{
			$addOn = $addOnManager->getById($this->data['current_addon_id']);
			$this->currentAddOn = $addOn ? $addOn->title : $this->data['current_addon_id'];
		}

		if (!$this->data['current_hash_file'] || !file_exists($this->data['current_hash_file']))
		{
			$fs->put($fileCheck->getAbstractedCheckPath(true), json_encode($results));

			$this->resetForNextAddOn();

			return $this->resume();
		}

		$this->data['steps']++;

		$last = $this->data['start'];
		$addOnId = $this->data['current_addon_id'];
		$rootPrefix = \XF::getRootDirectory() . $ds;

		$json = json_decode(file_get_contents($this->data['current_hash_file']), true);

		if ($last)
		{
			$json = array_slice($json, $last, null, true);
		}

		foreach ((array)$json AS $file => $hash)
		{
			$path = $rootPrefix . $file;

			$results['total_checked']++;
			if (!file_exists($path))
			{
				$results['missing'][$addOnId][] = $file;
				$results['total_missing']++;
			}
			else if (\XF\Util\Hash::hashTextFile($path, 'sha256') !== $hash)
			{
				$results['inconsistent'][$addOnId][] = $file;
				$results['total_inconsistent']++;
			}

			$last++;
			unset($json[$file]);

			if (microtime(true) - $startTime >= $maxRunTime)
			{
				break;
			}
		}

		$this->data['start'] = $last;

		$fs->put($fileCheck->getAbstractedCheckPath(true), json_encode($results));

		if (!$json)
		{
			$this->resetForNextAddOn();
		}

		return $this->resume();
	}

	protected function completeFileCheck(\XF\Entity\FileCheck $fileCheck, array $results)
	{
		$fileCheck->total_missing = $results['total_missing'];
		$fileCheck->total_inconsistent = $results['total_inconsistent'];
		$fileCheck->total_checked = $results['total_checked'];

		if ($results['total_missing'] || $results['total_inconsistent'])
		{
			$fileCheck->check_state = 'failure';
		}
		else
		{
			$fileCheck->check_state = 'success';
		}

		$contents = Json::jsonEncodePretty($results);
		File::writeToAbstractedPath($fileCheck->getAbstractedCheckPath(), $contents);

		$fileCheck->check_hash = \XF\Util\Hash::hashText($contents, 'sha256');
		$fileCheck->save();

		$options = $this->app->options();

		$emailWarning = $options->emailFileCheckWarning;
		if ($this->data['automated'] && $emailWarning['enabled'] && $this->isUniqueFailure($fileCheck))
		{
			$toEmail = $emailWarning['email'] ?: $options->contactEmailAddress;
			if ($toEmail)
			{
				$mail = $this->app->mailer()->newMail()->setTo($toEmail);
				$mail->setTemplate('file_check_warning', [
					'fileCheck' => $fileCheck
				]);
				$mail->send();
			}
		}
	}

	protected function resetForNextAddOn()
	{
		$this->data['current_addon_id'] = null;
		$this->data['current_hash_file'] = null;
		$this->data['start'] = 0;
	}

	protected function isUniqueFailure(\XF\Entity\FileCheck $fileCheck)
	{
		if ($fileCheck->check_state != 'failure')
		{
			return false;
		}

		/** @var \XF\Entity\FileCheck $lastFileCheck */
		$lastFileCheck = $this->app->finder('XF:FileCheck')
			->where('check_state', '!=', 'pending')
			->where('check_id', '!=', $fileCheck->check_id)
			->order('check_date', 'DESC')
			->fetchOne();

		return (!$lastFileCheck || $fileCheck->check_hash !== $lastFileCheck->check_hash);
	}

	public function getStatusMessage()
	{
		$actionPhrase = \XF::phrase('checking_integrity_of_files_for');
		$typePhrase = $this->currentAddOn ?: '';
		$steps = ($this->data['steps'] > 1) ? str_repeat('. ', $this->data['steps'] - 1) : '';

		return sprintf('%s... %s %s', $actionPhrase, $typePhrase, $steps);
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