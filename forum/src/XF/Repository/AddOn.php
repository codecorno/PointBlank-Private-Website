<?php

namespace XF\Repository;

use XF\Mvc\Entity\Finder;
use XF\Mvc\Entity\Repository;

class AddOn extends Repository
{
	/**
	 * @return Finder
	 */
	public function findAddOnsForList()
	{
		return $this->finder('XF:AddOn')->order('title');
	}

	/**
	 * @return Finder
	 */
	public function findActiveAddOnsForList()
	{
		return $this->findAddOnsForList()->where('active', 1);
	}

	public function hasAddOnsBeingProcessed()
	{
		$processing = $this->db()->fetchOne("
			SELECT addon_id
			FROM xf_addon
			WHERE is_processing = 1
			LIMIT 1
		");
		return $processing ? true : false;
	}

	public function getInstalledAddOnData()
	{
		return $this->db()->fetchAllKeyed("
			SELECT addon_id, version_id, active
			FROM xf_addon
		", 'addon_id');
	}

	public function getDefaultAddOnId()
	{
		if (\XF::$developmentMode)
		{
			return $this->app()->config('development')['defaultAddOn'];
		}

		return '';
	}

	public function canChangeAddOn()
	{
		return \XF::$developmentMode;
	}

	public function canInstallFromArchive(&$error, $bypassConfig = false)
	{
		if (!$bypassConfig && !$this->app()->config('enableAddOnArchiveInstaller'))
		{
			$error = \XF::phrase('installing_from_archives_must_be_explicitly_enabled_explain');
			return false;
		}

		if (!class_exists('ZipArchive'))
		{
			$error = \XF::phrase('installing_from_archives_is_only_supported_if_you_have_ziparchive_support');
			return false;
		}

		$root = \XF::getRootDirectory();
		$ds = DIRECTORY_SEPARATOR;

		$mustBeWritable = [
			"{$root}",
			"{$root}{$ds}js",
			"{$root}{$ds}src{$ds}addons",
			"{$root}{$ds}styles",
			__FILE__
		];

		$writable = true;

		foreach ($mustBeWritable AS $path)
		{
			if (!is_writable($path))
			{
				$writable = false;
				break;
			}
		}

		if (!$writable)
		{
			unset($mustBeWritable[0]);
			$relativePaths = array_map('XF\Util\File::stripRootPathPrefix', $mustBeWritable);

			$error = \XF::phrase('cannot_install_from_archive_as_not_all_required_directories_writable', ['relativePaths' => implode(', ', $relativePaths)]);
			return false;
		}

		return true;
	}

	public function getEnabledAddOns()
	{
		$registry = $this->app()->registry();
		return $registry['addOns'];
	}

	public function getDisabledAddOnsCache()
	{
		$registry = $this->app()->registry();
		return $registry['disabledAddOns'];
	}

	public function setDisabledAddOnsCache(array $cache)
	{
		$registry = $this->app()->registry();
		return $registry['disabledAddOns'] = $cache;
	}

	public function convertAddOnIdToUrlVersion($id)
	{
		return str_replace('/', '-', $id);
	}

	public function convertAddOnIdUrlVersionToBase($id)
	{
		return str_replace('-', '/', $id);
	}

	public function inferVersionStringFromId($versionId)
	{
		$versionString = '';
		$revVersionId = strrev($versionId);

		// Match our traditional version ID with an optional '90' prefix. Used in XFMG to offset legacy versioning.
		// The '90' prefix can be repeated up to four times if a longer ID is needed.
		// Has also been recommended to add-on devs as a convention for overcoming similar legacy version issues.
		// Note: The regex works backwards on the reversed version ID.
		if (preg_match('/^(\d)(\d)(\d{2})(\d{2})(\d{1,2})(?:09){0,4}$/', $revVersionId, $matches))
		{
			$matches = array_map('strrev', $matches);
			list($null, $build, $status, $patch, $minor, $major) = $matches;

			$versionString = intval($major) . '.' . intval($minor) . '.' . intval($patch);
			switch ($status)
			{
				case 1:
				case 2:
					$versionString .= ' Alpha';
					if ($status == 2)
					{
						$build += 10;
					}
					break;

				case 3:
				case 4:
					$versionString .= ' Beta';
					if ($status == 4)
					{
						$build += 10;
					}
					break;

				case 5:
				case 6:
					$versionString .= ' Release Candidate';
					if ($status == 6)
					{
						$build += 10;
					}
					break;

				case 7:
				case 8:
					if ($status == 8)
					{
						$build += 10;
					}
					if ($build > 0)
					{
						$versionString .= ".$build";
						$build = 0;
					}
					break;
				case 9:
					$versionString .= ' Patch Level';
					break;
			}

			if ($build)
			{
				$build = intval($build);
				$versionString .= " $build";
			}
		}

		return $versionString;
	}

	public function cleanUpAddOnBatches($cutOff = null)
	{
		if ($cutOff === null)
		{
			$cutOff = \XF::$time - 86400;
		}

		/** @var \XF\Entity\AddOnInstallBatch[] $batches */
		$batches = $this->finder('XF:AddOnInstallBatch')
			->where('start_date', '<', $cutOff)
			->order('start_date', 'ASC')
			->fetch(1000);
		foreach ($batches AS $batch)
		{
			$batch->delete();
		}
	}
}