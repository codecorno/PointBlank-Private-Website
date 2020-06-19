<?php

namespace XF\Entity;

use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Structure;

/**
 * COLUMNS
 * @property int|null check_id
 * @property string|null error_code
 * @property int check_date
 * @property bool|null board_url_valid
 * @property bool|null branding_valid
 * @property bool|null license_expired
 * @property array|null invalid_add_ons
 * @property array|null available_updates
 *
 * GETTERS
 * @property mixed error_message
 * @property mixed RelevantAddOns
 */
class UpgradeCheck extends Entity
{
	public function getErrorMessage()
	{
		if (!$this->error_code)
		{
			return null;
		}

		return \XF::phrase('upgrade_check_error.' . strtolower($this->error_code), [
			'upgradeCheck' => $this->app()->router('admin')->buildLink('tools/upgrade-check')
		]);
	}

	public function hasLicenseErrors()
	{
		return ($this->error_code || !$this->branding_valid || $this->invalid_add_ons);
	}

	public function canDownload()
	{
		return (!$this->error_code && !$this->hasLicenseErrors());
	}

	public function hasNotice()
	{
		return (
			$this->error_code
			|| $this->hasLicenseErrors()
			|| !$this->board_url_valid
			|| $this->available_updates
			|| $this->license_expired
		);
	}

	public function hasAvailableUpdate($product, &$error = null, &$availableUpdate = null, &$addOn = null)
	{
		/** @var AddOn $addOn */
		$addOn = $this->em()->find('XF:AddOn', $product);

		if (!$addOn)
		{
			$error = \XF::phrase('x_is_not_installed_so_cannot_be_upgraded', ['title' => $product]);
			return false;
		}

		if (!$addOn->active)
		{
			$error = \XF::phrase('x_is_installed_but_not_enabled_so_cannot_be_upgraded', ['title' => $product]);
			return false;
		}

		if (!isset($this->available_updates[$product]))
		{
			$error = \XF::phrase('there_currently_no_upgrades_available_for_x', ['title' => $addOn->title]);
			return false;
		}

		$availableUpdate = $this->available_updates[$product];
		$versionId = $product == 'XF' ? \XF::$versionId : $addOn->version_id;

		if ($availableUpdate['version_id'] <= $versionId)
		{
			$error = \XF::phrase('x_is_already_up_to_date', ['title' => $addOn->title]);
			return false;
		}

		return true;
	}

	public function hasAvailableAddOnUpdate()
	{
		$availableUpdates = $this->available_updates;
		unset($availableUpdates['XF']);

		if (!$availableUpdates)
		{
			return false;
		}

		foreach ($availableUpdates AS $addOnId => $update)
		{
			if ($this->hasAvailableUpdate($addOnId))
			{
				return true;
			}
		}

		return false;
	}

	public function getRelevantAddOns()
	{
		$addOnIds = array_merge(array_keys($this->available_updates), array_keys($this->invalid_add_ons));
		if ($addOnIds)
		{
			return $this->_em->findByIds('XF:AddOn', $addOnIds)->toArray();
		}
		else
		{
			return [];
		}
	}

	public static function getStructure(Structure $structure)
	{
		$structure->table = 'xf_upgrade_check';
		$structure->shortName = 'XF:UpgradeCheck';
		$structure->primaryKey = 'check_id';
		$structure->columns = [
			'check_id' => ['type' => self::UINT, 'autoIncrement' => true, 'nullable' => true],
			'error_code' => ['type' => self::STR, 'nullable' => true],
			'check_date' => ['type' => self::UINT, 'default' => \XF::$time],
			'board_url_valid' => ['type' => self::BOOL, 'nullable' => true],
			'branding_valid' => ['type' => self::BOOL, 'nullable' => true],
			'license_expired' => ['type' => self::BOOL, 'nullable' => true],
			'invalid_add_ons' => ['type' => self::JSON_ARRAY, 'nullable' => true],
			'available_updates' => ['type' => self::JSON_ARRAY, 'nullable' => true]
		];
		$structure->getters = [
			'error_message' => true,
			'RelevantAddOns' => true
		];
		$structure->relations = [];

		return $structure;
	}
}