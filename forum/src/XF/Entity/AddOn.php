<?php

namespace XF\Entity;

use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Structure;

/**
 * COLUMNS
 * @property string addon_id
 * @property string title
 * @property string version_string
 * @property int version_id
 * @property string json_hash
 * @property bool active
 * @property bool is_legacy
 * @property bool is_processing
 * @property string|null last_pending_action
 *
 * GETTERS
 * @property string addon_id_url
 */
class AddOn extends Entity
{
	public function canEdit()
	{
		return ($this->addon_id != 'XF');
	}

	/**
	 * @return string
	 */
	public function getAddonIdUrl()
	{
		// casing matches the addon_id_url getter
		return $this->repository('XF:AddOn')->convertAddOnIdToUrlVersion($this->addon_id);
	}

	public function getLastActionStep($expectedType)
	{
		$parts = $this->last_pending_action ? explode(':', $this->last_pending_action) : null;
		if (!$parts)
		{
			return null;
		}

		if ($parts[0] !== $expectedType)
		{
			return null;
		}

		if ($expectedType == 'upgrade')
		{
			if (!isset($parts[1]) || !isset($parts[2]))
			{
				return null;
			}

			$versionId = intval($parts[1]);
			$step = intval($parts[2]);

			if ($versionId > 0 && $step > 0)
			{
				return [$versionId, $step];
			}
			else
			{
				return null;
			}
		}
		else
		{
			if (!isset($parts[1]))
			{
				return null;
			}

			$step = intval($parts[1]);
			if ($expectedType == 'install')
			{
				// Step 0 on install means we've triggered it but the first step failed. We have an add-on record
				// but we shouldn't consider it as installed.
				return $step >= 0 ? $step : null;
			}
			else
			{
				return $step > 0 ? $step : null;
			}
		}
	}

	/**
	 * @return null|\XF\AddOn\AddOn
	 */
	public function getAddOnClass()
	{
		return $this->app()->addOnManager()->getById($this->addon_id);
	}

	// ********************************* LIFE CYCLE ***************************

	protected function _preSave()
	{
		if ($this->isUpdate() && !$this->canEdit())
		{
			$this->error(\XF::phrase('this_add_on_cannot_be_modified_or_deleted'));
		}
	}

	protected function _postSave()
	{
		$manager = $this->getDataManager();

		if ($this->isUpdate() && $this->isChanged('addon_id'))
		{
			$manager->updateRelatedIds($this, $this->getExistingValue('addon_id'));
		}

		if ($this->isUpdate() && $this->isChanged('active') && $this->getOption('rebuild_active_change'))
		{
			$manager->triggerRebuildActiveChange($this);
		}
		else if ($this->isUpdate() && $this->isChanged('is_processing'))
		{
			$manager->triggerRebuildProcessingChange($this);
		}

		if ($this->isChanged(['active', 'version_id', 'json_hash']))
		{
			$manager->rebuildActiveAddOnCache();
		}

		if ($this->isInsert() || $this->isChanged('addon_id'))
		{
			// we need to ensure the add-on manager can pull this entity, so wipe out the cache
			$this->app()->addOnManager()->resetAddOnCache();
		}

		if ($this->isChanged('version_id'))
		{
			$this->triggerUpgradeReCheck();
		}
	}

	protected function _preDelete()
	{
		if (!$this->canEdit())
		{
			$this->error(\XF::phrase('this_add_on_cannot_be_modified_or_deleted'));
		}
	}

	protected function _postDelete()
	{
		// immediately trigger so we don't run code inconsistently
		$this->repository('XF:CodeEventListener')->rebuildListenerCache();
		$this->repository('XF:ClassExtension')->rebuildExtensionCache();

		$manager = $this->getDataManager();
		$manager->enqueueRemoveAddOnData($this->addon_id);
		$manager->rebuildActiveAddOnCache();

		$this->triggerUpgradeReCheck();
	}

	protected function triggerUpgradeReCheck()
	{
		if (substr($this->addon_id, 0, 2) === 'XF')
		{
			$this->app()->jobManager()->enqueueUnique('xfUpgradeCheck', 'XF:UpgradeCheck', [], false);
		}
	}

	public static function getStructure(Structure $structure)
	{
		$structure->table = 'xf_addon';
		$structure->shortName = 'XF:AddOn';
		$structure->primaryKey = 'addon_id';
		$structure->columns = [
			'addon_id' => ['type' => self::BINARY, 'maxLength' => 50,
				'required' => 'please_enter_valid_addon_id',
				'unique' => 'add_on_ids_must_be_unique',
				'match' => [
					'#^[a-z][a-z0-9]*(/[a-z][a-z0-9]*)?$#i',
					'please_enter_valid_add_on_id_using_rules'
				]
			],
			'title' => ['type' => self::STR, 'maxLength' => 75,
				'required' => 'please_enter_valid_title'
			],
			'version_string' => ['type' => self::STR, 'maxLength' => 30, 'default' => ''],
			'version_id' => ['type' => self::UINT, 'default' => 0],
			'json_hash' => ['type' => self::STR, 'maxLength' => 64, 'default' => ''],
			'active' => ['type' => self::BOOL, 'default' => true],
			'is_legacy' => ['type' => self::BOOL, 'default' => false],
			'is_processing' => ['type' => self::BOOL, 'default' => false],
			'last_pending_action' => ['type' => self::STR, 'maxLength' => 50, 'nullable' => true],
		];
		$structure->getters = [
			'addon_id_url' => true
		];
		$structure->relations = [];

		$structure->options = [
			'rebuild_active_change' => true
		];

		return $structure;
	}

	/**
	 * @return \XF\AddOn\DataManager
	 */
	protected function getDataManager()
	{
		return $this->app()->addOnDataManager();
	}
}