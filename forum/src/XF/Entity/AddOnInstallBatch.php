<?php

namespace XF\Entity;

use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Structure;

/**
 * COLUMNS
 * @property int|null batch_id
 * @property int start_date
 * @property int complete_date
 * @property array addon_ids
 * @property array results
 */
class AddOnInstallBatch extends Entity
{
	protected $pendingAddOnFiles = [];

	public function getAbstractedBatchPath()
	{
		if (!$this->batch_id)
		{
			throw new \LogicException("Cannot get batch path until saved");
		}

		return "internal-data://addon_batch/{$this->batch_id}";
	}

	public function getAbstractedAddOnBatchPath($addOnId)
	{
		/** @var \XF\Repository\AddOn $addOnRepo */
		$addOnRepo = $this->repository('XF:AddOn');

		$addOnIdPath = $addOnRepo->convertAddOnIdToUrlVersion($addOnId);
		$addOnIdPath = preg_replace('#[^a-z0-9_\\-]#i', '', $addOnIdPath);

		return $this->getAbstractedBatchPath() . "/{$addOnIdPath}.zip";
	}

	public function getPlannedActions()
	{
		$addOnManager = $this->app()->addOnManager();
		$installedAddOns = $addOnManager->getInstalledAddOns();

		$actions = [];
		foreach ($this->addon_ids AS $addOnId => $newInfo)
		{
			$newTitle = $newInfo['title'];
			$newVersionId = $newInfo['version_id'];
			$newVersionString = $newInfo['version_string'];

			if (!isset($installedAddOns[$addOnId]))
			{
				$actions[$addOnId] = [
					'action' => 'install',
					'title' => $newTitle,
					'version' => $newVersionString
				];
			}
			else
			{
				$installed = $installedAddOns[$addOnId];
				$existingVersionId = $installed->version_id;
				if ($existingVersionId === $newVersionId)
				{
					$actions[$addOnId] = [
						'action' => 'rebuild',
						'title' => $installed->title,
						'version' => $newVersionString
					];
				}
				else
				{
					$actions[$addOnId] = [
						'action' => 'upgrade',
						'title' => $installed->title,
						'version' => $newVersionString,
						'oldVersion' => $installed->version_string
					];
				}
			}
		}

		return $actions;
	}

	public function addAddOn($addOnId, $title, $newVersionId, $newVersionString, $tempFile)
	{
		$addOnIds = $this->addon_ids;
		$addOnIds[$addOnId] = [
			'title' => $title,
			'version_id' => $newVersionId,
			'version_string' => $newVersionString,
		];
		$this->addon_ids = $addOnIds;

		$this->pendingAddOnFiles[$addOnId] = $tempFile;
	}

	protected function _preSave()
	{
		if (!$this->addon_ids)
		{
			$this->error(\XF::phrase('cannot_proceed_with_installation_batch_without_any_valid_add_ons'));
		}
	}

	protected function _postSave()
	{
		if ($this->pendingAddOnFiles)
		{
			foreach ($this->pendingAddOnFiles AS $addOnId => $tempFile)
			{
				$abstractedPath = $this->getAbstractedAddOnBatchPath($addOnId);
				\XF\Util\File::copyFileToAbstractedPath($tempFile, $abstractedPath);
			}
			$this->pendingAddOnFiles = [];
		}
	}

	protected function _postDelete()
	{
		\XF\Util\File::deleteAbstractedDirectory($this->getAbstractedBatchPath());
	}

	public static function getStructure(Structure $structure)
	{
		$structure->table = 'xf_addon_install_batch';
		$structure->shortName = 'XF:AddOnInstallBatch';
		$structure->primaryKey = 'batch_id';
		$structure->columns = [
			'batch_id' => ['type' => self::UINT, 'autoIncrement' => true, 'nullable' => true],
			'start_date' => ['type' => self::UINT, 'default' => \XF::$time],
			'complete_date' => ['type' => self::UINT, 'default' => 0],
			'addon_ids' => ['type' => self::JSON_ARRAY, 'default' => []],
			'results' => ['type' => self::JSON_ARRAY, 'default' => []]
		];
		$structure->getters = [];
		$structure->relations = [];

		return $structure;
	}
}