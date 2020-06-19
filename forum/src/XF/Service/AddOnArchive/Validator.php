<?php

namespace XF\Service\AddOnArchive;

use XF\Service\AbstractService;
use XF\Util\File;

class Validator extends AbstractService
{
	protected $addOnId;

	protected $fileName;

	/**
	 * @var \ZipArchive|null
	 */
	protected $_zip;

	protected $json;

	public function __construct(\XF\App $app, $fileName, $addOnId = null)
	{
		parent::__construct($app);

		$this->fileName = $fileName;
		$this->addOnId = $addOnId ?: $this->resolveAddOnFromZip();
	}

	public function getAddOnId()
	{
		return $this->addOnId;
	}

	public function getAddOnJson()
	{
		return $this->json;
	}

	protected function resolveAddOnFromZip()
	{
		$addOnIds = [];
		$zip = $this->zip();

		for ($i = 0; $i < $zip->numFiles; $i++)
		{
			$fileName = $zip->getNameIndex($i);
			if (preg_match("#^upload/src/addons/([a-z][a-z0-9]*(?:/[a-z][a-z0-9]*)?)/addon\.json$#i", $fileName, $match))
			{
				$addOnIds[] = $match[1];
			}
		}

		if (count($addOnIds) == 1)
		{
			return reset($addOnIds);
		}
		else
		{
			return null;
		}
	}

	public function validate(&$error = null)
	{
		if (!$this->addOnId)
		{
			// couldn't find the add-on
			$error = \XF::phrase('file_does_not_appear_to_be_valid_add_on_archive_as_expected');
			return false;
		}

		$zip = $this->zip();
		$jsonFile = $this->getZipAddOnRootDir() . "/addon.json";

		if ($zip->locateName($jsonFile) === false)
		{
			$error = \XF::phrase('file_does_not_appear_to_be_valid_add_on_archive_as_expected');
			return false;
		}

		if (!$zip->extractTo(File::createTempDir(), $jsonFile))
		{
			$error = \XF::phrase('files_could_not_be_extracted_from_add_on_archive');
			return false;
		}

		$json = json_decode($zip->getFromName($jsonFile), true);
		$addOnManager = $this->app->addOnManager();

		$title = $json['title'];
		$newVersionId = $json['version_id'];
		$installedAddOns = $addOnManager->getInstalledAddOns();

		if (isset($installedAddOns[$this->addOnId]))
		{
			if ($newVersionId < $installedAddOns[$this->addOnId]->version_id)
			{
				$error = \XF::phrase('version_of_x_older_than_currently_installed', ['title' => $title]);
				return false;
			}

			// TODO: block in dev mode if we have output?
		}

		if (!empty($json['require']))
		{
			if (!$addOnManager->checkAddOnRequirements($json['require'], $title, $requirementErrors))
			{
				$error = \XF::phrase('following_requirements_for_x_were_not_met_y', ['errors' => implode(" ", $requirementErrors), 'title' => $title]);
				return false;
			}
		}

		$this->json = $json;
		return true;
	}

	protected function zip()
	{
		if (!$this->_zip)
		{
			$zip = new \ZipArchive();
			$openResult = $zip->open($this->fileName);
			if ($openResult !== true)
			{
				throw new \LogicException("File could not be opened as a zip ($openResult)");
			}

			$this->_zip = $zip;
		}

		return $this->_zip;
	}

	protected function getZipAddOnRootDir()
	{
		return "upload/src/addons/{$this->addOnId}";
	}
}