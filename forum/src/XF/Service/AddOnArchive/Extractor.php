<?php

namespace XF\Service\AddOnArchive;

use XF\Service\AbstractService;
use XF\Util\File;

// ######## NOTE: SIMILARITY TO XF CORE UPGRADER CODE ############
// Much of this code is similar to the XFUpgraderExtractor class in src/XF/Install/_upgrader/core.php.
// Changes should be mirrored when appropriate.

class Extractor extends AbstractService
{
	protected $addOnId;

	protected $fileName;

	/**
	 * @var \ZipArchive|null
	 */
	protected $_zip;

	public function __construct(\XF\App $app, $addOnId, $fileName)
	{
		parent::__construct($app);

		$this->addOnId = $addOnId;
		$this->fileName = $fileName;
	}

	public function open()
	{
		if (!$this->_zip)
		{
			$zip = new \ZipArchive();
			$openResult = $zip->open($this->fileName);
			if ($openResult !== true)
			{
				throw new \LogicException("File could not be opened as a zip ($openResult)");
			}

			$addOnJson = $zip->locateName($this->getZipAddOnRootDir() . "/addon.json");
			if ($addOnJson === false)
			{
				throw new \LogicException("Zip isn't an add-on");
			}

			$this->_zip = $zip;
		}

		return true;
	}

	protected function zip()
	{
		$this->open();
		return $this->_zip;
	}

	public function compareHashes(array $existingHashes)
	{
		$newHashes = $this->getNewHashes();
		if (!$newHashes)
		{
			return null;
		}

		$changes = [];
		foreach ($newHashes AS $file => $newHash)
		{
			if (!isset($existingHashes[$file]))
			{
				$changes[$file] = 'create';
			}
			else if ($newHash !== $existingHashes[$file])
			{
				$changes[$file] = 'update';
			}
		}

		$changes[preg_replace('#^upload/#', '', $this->getHashFileName())] = 'update';

		foreach ($existingHashes AS $oldFile => $null)
		{
			if (!isset($newHashes[$oldFile]))
			{
				$changes[$oldFile] = 'delete';
			}
		}

		return $changes;
	}

	public function checkWritable(array $changeset = null, &$failures = [])
	{
		$zip = $this->zip();
		$failures = [];

		for ($i = 0; $i < $zip->numFiles; $i++)
		{
			$zipFileName = $zip->getNameIndex($i);
			$fsFileName = $this->getFsFileNameFromZipName($zipFileName);
			if ($fsFileName === null)
			{
				continue;
			}

			if (is_array($changeset) && !isset($changeset[$fsFileName]))
			{
				// we're not changing this file
				continue;
			}

			if (!File::isWritable($this->getFinalFsFileName($fsFileName)))
			{
				$failures[] = $fsFileName;
			}
		}

		return $failures ? false : true;
	}

	public function copyFiles(array $changeset = null, $start = 0, \XF\Timer $timer = null)
	{
		$zip = $this->zip();
		$lastComplete = $start;

		for ($i = $start; $i < $zip->numFiles; $i++)
		{
			$lastComplete = $i;

			$zipFileName = $zip->getNameIndex($i);
			$fsFileName = $this->getFsFileNameFromZipName($zipFileName);
			if ($fsFileName === null)
			{
				continue;
			}

			if (is_array($changeset) && !isset($changeset[$fsFileName]))
			{
				// we're not changing this file
				continue;
			}

			$finalFileName = $this->getFinalFsFileName($fsFileName);

			$dataStream = $zip->getStream($zipFileName);
			$targetWritten = @File::writeFile($finalFileName, $dataStream, false);

			if (!$targetWritten)
			{
				return [
					'status' => 'error',
					'error' => "Failed write to {$fsFileName}"
				];
			}

			if ($timer && $timer->limitExceeded())
			{
				break;
			}
		}

		$complete = ($i >= $zip->numFiles);

		if ($complete)
		{
			// if we don't have a new hashes file, we need to remove the old one if it exists as it will be wrong
			$hashZipFileName = $this->getHashFileName();
			if ($zip->locateName($hashZipFileName) === false)
			{
				$fsFileName = $this->getFsFileNameFromZipName($hashZipFileName);
				$finalFileName = $this->getFinalFsFileName($fsFileName);
				if (file_exists($finalFileName) && !@unlink($finalFileName))
				{
					return [
						'status' => 'error',
						'error' => "Failed write to {$fsFileName}"
					];
				}
			}
		}

		return [
			'status' => ($complete ? 'complete' : 'incomplete'),
			'last' => $lastComplete
		];
	}

	protected function getNewHashes()
	{
		$newHashesJson = $this->zip()->getFromName($this->getHashFileName());
		if (!$newHashesJson)
		{
			return null;
		}

		return json_decode($newHashesJson, true);
	}

	protected function getFsFileNameFromZipName($fileName)
	{
		if (substr($fileName, -1) === '/')
		{
			// this is a directory we can just skip this
			return null;
		}

		if (!preg_match("#^upload/.#", $fileName))
		{
			// file outside of "upload" so we can just skip this
			return null;
		}

		return substr($fileName, 7); // remove "upload/"
	}

	protected function getFinalFsFileName($fileName)
	{
		return \XF::getRootDirectory() . \XF::$DS . $fileName;
	}

	protected function getHashFileName()
	{
		return $this->getZipAddOnRootDir() . "/hashes.json";
	}

	protected function getZipAddOnRootDir()
	{
		return "upload/src/addons/{$this->addOnId}";
	}
}