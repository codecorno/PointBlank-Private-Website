<?php

namespace XF;

use XF\Mvc\Entity\Entity;
use XF\Util\File;
use XF\Util\Json;

class DevelopmentOutput
{
	protected $enabled;
	protected $basePath;
	protected $metadataFilename = '_metadata.json';
	protected $skipAddOns = [];

	protected $metadataCache = [];
	protected $typeLoaded = [];

	protected $batchMode = false;
	protected $batchesPending = [];

	protected $handlerCache = [];

	protected $validFileExtensions = ['txt', 'html', 'less', 'css', 'xhtml', 'json', 'xml', ''];
	protected $invalidFileExtensions = [
		'bak',
		'git',
		'gitignore',
		'mine',
		'tmp',
		'temp',
		'bad',
		'vagrant',
		'ds_store'
	];

	public function __construct($enabled, $basePath, array $skipAddOns = [])
	{
		$this->enabled = $enabled;
		$this->basePath = File::canonicalizePath($basePath);
		$this->skipAddOns = array_fill_keys($skipAddOns, true);
	}

	public function hasNameChange(Entity $entity)
	{
		$handler = $this->getHandler($entity->structure()->shortName);
		return $handler->hasNameChange($entity);
	}

	public function export(Entity $entity)
	{
		$handler = $this->getHandler($entity->structure()->shortName);
		return $handler->export($entity);
	}

	public function import($shortName, $name, $addOnId, $contents, array $metadata, array $options = [])
	{
		$handler = $this->getHandler($shortName);
		return $handler->import($name, $addOnId, $contents, $metadata, $options);
	}

	public function delete(Entity $entity, $new = true)
	{
		$handler = $this->getHandler($entity->structure()->shortName);
		return $handler->delete($entity, $new);
	}

	/**
	 * @param $shortName
	 *
	 * @return \XF\DevelopmentOutput\AbstractHandler
	 */
	public function getHandler($shortName)
	{
		if (isset($this->handlerCache[$shortName]))
		{
			return $this->handlerCache[$shortName];
		}

		$class = \XF::stringToClass($shortName, '%s\DevelopmentOutput\%s');
		if (!class_exists($class))
		{
			throw new \InvalidArgumentException("No handler for $shortName found ($class expected)");
		}

		$class = \XF::extendClass($class);

		$handler = new $class($this, $shortName);
		if (!($handler instanceof \XF\DevelopmentOutput\AbstractHandler))
		{
			throw new \InvalidArgumentException("Cannot instantiate development output type handler for class '$class'");
		}
		$this->handlerCache[$shortName] = $handler;

		return $handler;
	}

	public function enableBatchMode()
	{
		$this->batchMode = true;
	}

	public function clearBatchMode()
	{
		$this->flushBatches();
		$this->batchMode = false;
	}

	public function flushBatches()
	{
		foreach ($this->batchesPending AS $typeDir => $addOnIds)
		{
			foreach ($addOnIds AS $addOnId => $null)
			{
				$typeMetadata = $this->metadataCache[$typeDir][$addOnId];

				$metadataPath = $this->getMetadataFileName($typeDir, $addOnId);
				file_put_contents($metadataPath, Json::jsonEncodePretty($typeMetadata));
			}
		}

		$this->batchesPending = [];
	}

	protected function isValidDir(\DirectoryIterator $entry)
	{
		/** @var \DirectoryIterator $entry */
		if (!$entry->isDir()
			|| $entry->isDot()
			|| !preg_match('/^[a-z0-9_]+$/i', $entry->getBasename())
		)
		{
			return false;
		}

		return true;
	}

	protected function prepareAddOnIdForPath($addOnId)
	{
		if (strpos($addOnId, '/') !== false)
		{
			$addOnId = str_replace('/', \XF::$DS, $addOnId);
		}
		return $addOnId;
	}

	protected function isDirAddOnRoot(\DirectoryIterator $entry)
	{
		$ds = \XF::$DS;

		$pathname = $entry->getPathname();
		$addOnJson = "{$pathname}{$ds}addon.json";
		$outputDir = "{$pathname}{$ds}_output";

		if (file_exists($addOnJson) || file_exists($outputDir))
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	public function getAvailableAddOnIds()
	{
		if (!file_exists($this->basePath))
		{
			return [];
		}

		$addOnIds = [];
		foreach (new \DirectoryIterator($this->basePath) AS $entry)
		{
			if (!$this->isValidDir($entry))
			{
				continue;
			}

			if ($this->isDirAddOnRoot($entry) || $entry->getBasename() == 'XF')
			{
				$addOnId = $entry->getBasename();
				if (!$this->isAddOnSkipped($addOnId))
				{
					$addOnIds[] = $addOnId;
				}
			}
			else
			{
				$vendorPrefix = $entry->getBasename();
				foreach (new \DirectoryIterator($entry->getPathname()) AS $addOnDir)
				{
					if (!$this->isValidDir($addOnDir))
					{
						continue;
					}

					if ($this->isDirAddOnRoot($addOnDir))
					{
						$addOnId = "$vendorPrefix/{$addOnDir->getBasename()}";
						if (!$this->isAddOnSkipped($addOnId))
						{
							$addOnIds[] = $addOnId;
						}
					}
				}
			}
		}

		return $addOnIds;
	}

	protected function loadTypeMetadata($typeDir)
	{
		$typeMetadata = [];
		$this->typeLoaded[$typeDir] = true;

		$ds = \XF::$DS;
		$dir = $this->basePath;

		$addOnIds = $this->getAvailableAddOnIds();
		foreach ($addOnIds AS $addOnId)
		{
			$addOnIdDir = $this->prepareAddOnIdForPath($addOnId);
			$metadataFile = "{$dir}{$ds}{$addOnIdDir}{$ds}_output{$ds}{$typeDir}{$ds}{$this->metadataFilename}";
			if (file_exists($metadataFile))
			{
				$json = json_decode(file_get_contents($metadataFile), true);
				$typeMetadata[$addOnId] = $json;
			}
			else
			{
				$typeMetadata[$addOnId] = [];
			}
		}

		$this->metadataCache[$typeDir] = $typeMetadata;
	}

	public function getTypeMetadata($typeDir)
	{
		if (empty($this->typeLoaded[$typeDir]))
		{
			$this->loadTypeMetadata($typeDir);
		}

		return $this->metadataCache[$typeDir];
	}

	public function getMetadata($typeDir, $addOnId)
	{
		if ($this->isAddOnSkipped($addOnId))
		{
			return [];
		}

		if (isset($this->metadataCache[$typeDir][$addOnId]))
		{
			return $this->metadataCache[$typeDir][$addOnId];
		}

		$metadataPath = $this->getMetadataFileName($typeDir, $addOnId);
		if (file_exists($metadataPath))
		{
			$metadata = json_decode(file_get_contents($metadataPath), true);
		}
		else
		{
			$metadata = [];
		}

		$this->metadataCache[$typeDir][$addOnId] = $metadata;

		return $metadata;
	}

	protected function getMetadataFileName($typeDir, $addOnId)
	{
		$ds = \XF::$DS;
		$addOnIdDir = $this->prepareAddOnIdForPath($addOnId);
		return "{$this->basePath}{$ds}{$addOnIdDir}{$ds}_output{$ds}{$typeDir}{$ds}{$this->metadataFilename}";
	}

	protected function writeTypeMetadata($typeDir, $addOnId, array $typeMetadata)
	{
		if ($this->isAddOnSkipped($addOnId))
		{
			return;
		}

		ksort($typeMetadata);

		$this->metadataCache[$typeDir][$addOnId] = $typeMetadata;

		if ($this->batchMode)
		{
			$this->batchesPending[$typeDir][$addOnId] = true;
		}
		else
		{
			$metadataPath = $this->getMetadataFileName($typeDir, $addOnId);
			file_put_contents($metadataPath, Json::jsonEncodePretty($typeMetadata));
		}
	}

	protected function updateMetadata($typeDir, $addOnId, $fileName, array $metadata)
	{
		if ($this->isAddOnSkipped($addOnId))
		{
			return;
		}

		$typeMetadata = $this->getMetadata($typeDir, $addOnId);

		$existing = isset($typeMetadata[$fileName]) ? $typeMetadata[$fileName] : null;
		if ($existing == $metadata)
		{
			return;
		}

		$typeMetadata[$fileName] = $metadata;
		$this->writeTypeMetadata($typeDir, $addOnId, $typeMetadata);
	}

	public function getTypes()
	{
		$types = [];
		$ds = \XF::$DS;
		$dir = $this->basePath;

		$addOnIds = $this->getAvailableAddOnIds();
		foreach ($addOnIds AS $addOnId)
		{
			$addOnIdDir = $this->prepareAddOnIdForPath($addOnId);
			$addOnOutputDir = "{$dir}{$ds}{$addOnIdDir}{$ds}_output";
			if (!file_exists($addOnOutputDir))
			{
				continue;
			}
			foreach (new \DirectoryIterator($addOnOutputDir) AS $addOnEntry)
			{
				if (!$this->isValidDir($addOnEntry))
				{
					continue;
				}

				$types[] = $addOnEntry->getBasename();
			}
		}

		$types = array_unique($types);
		sort($types);
		return $types;
	}

	public function getAvailableTypeFilesByAddOn($typeDir)
	{
		$files = [];

		$ds = \XF::$DS;
		$dir = $this->basePath;

		$addOnIds = $this->getAvailableAddOnIds();
		foreach ($addOnIds AS $addOnId)
		{
			$addOnIdDir = $this->prepareAddOnIdForPath($addOnId);
			$addOnTypeDir = "{$dir}{$ds}{$addOnIdDir}{$ds}_output{$ds}{$typeDir}";
			if (file_exists($addOnTypeDir))
			{
				foreach (new \DirectoryIterator($addOnTypeDir) AS $file)
				{
					/** @var \DirectoryIterator $file */
					$fileName = $file->getBasename();

					if ($this->isValidTypeFile($file))
					{
						$files[$addOnId][$fileName] = $file->getPathname();
					}
					else if (!$file->isDot() && $file->isDir() && $fileName[0] != '.')
					{
						foreach (new \DirectoryIterator($file->getPathname()) AS $childFile)
						{
							if ($this->isValidTypeFile($childFile))
							{
								$files[$addOnId][$fileName . '/' . $childFile->getBasename()] = $childFile->getPathname();
							}
						}
					}
				}

				if (empty($files[$addOnId]) && file_exists($addOnTypeDir . $ds . $this->metadataFilename))
				{
					$files[$addOnId] = [];
				}
			}
		}

		return $files;
	}

	/**
	 * @param \DirectoryIterator $file
	 *
	 * @return bool
	 */
	protected function isValidTypeFile(\DirectoryIterator $file)
	{
		if ($file->isDot()) return false;

		if (!$file->isFile()) return false;

		$fileName = $file->getBasename();

		if ($fileName == $this->metadataFilename) return false;

		if ($fileName[0] == '.' && $fileName != '.htaccess') return false;

		return $this->isValidFileExtension($file->getExtension());
	}

	protected function isValidFileExtension($extension)
	{
		$extension = strtolower($extension);

		if (in_array($extension, $this->getValidFileExtensions()))
		{
			return true;
		}
		else if (in_array($extension, $this->getInvalidFileExtensions()))
		{
			return false;
		}
		else
		{
			return $this->isValidUnknownExtension($extension);
		}
	}

	protected function getValidFileExtensions()
	{
		return $this->validFileExtensions;
	}

	protected function getInvalidFileExtensions()
	{
		return $this->invalidFileExtensions;
	}

	protected function isValidUnknownExtension($extension)
	{
		// don't allow .r{digits} SVN conflict files
		return preg_match('/^r\d+$/', $extension) ? false : true;
	}

	public function cleanTypeMetadata($type)
	{
		$changes = [];

		$typeFiles = $this->getAvailableTypeFilesByAddOn($type);
		foreach ($typeFiles AS $addOnId => $files)
		{
			$metadata = $this->getMetadata($type, $addOnId);
			$changed = false;

			foreach ($metadata AS $metadataFile => $info)
			{
				if (!isset($files[$metadataFile]))
				{
					unset($metadata[$metadataFile]);
					$changes[$addOnId][] = $metadataFile;
					$changed = true;
				}
			}

			if ($changed)
			{
				$this->writeTypeMetadata($type, $addOnId, $metadata);
			}
		}

		return $changes;
	}

	public function rebuildTypeMetadata($type)
	{
		$changes = [];

		$typeFiles = $this->getAvailableTypeFilesByAddOn($type);
		foreach ($typeFiles AS $addOnId => $files)
		{
			$metadata = $this->getMetadata($type, $addOnId);
			$changed = false;

			foreach ($files AS $fileName => $path)
			{
				if (isset($metadata[$fileName]))
				{
					$data = $metadata[$fileName];
					$hash = $this->hashContents(file_get_contents($path));
					if (!isset($data['hash']) || $hash != $data['hash'])
					{
						$metadata[$fileName]['hash'] = $hash;
						$changes[$addOnId][] = $fileName;
						$changed = true;
					}
				}
			}

			if ($changed)
			{
				$this->writeTypeMetadata($type, $addOnId, $metadata);
			}
		}

		return $changes;
	}

	public function writeFile($typeDir, $addOnId, $fileName, $fileContents, array $metadata = [], $verifyChange = true)
	{
		if (!$this->enabled || $this->isAddOnSkipped($addOnId))
		{
			return false;
		}

		$fullPath = $this->getFilePath($typeDir, $addOnId, $fileName);

		if ($verifyChange)
		{
			if (!file_exists($fullPath))
			{
				$write = true;
			}
			else
			{
				$write = file_get_contents($fullPath) != $fileContents;
			}
		}
		else
		{
			$write = true;
		}

		if ($write)
		{
			File::writeFile($fullPath, $fileContents, false);
		}

		$metadata['hash'] = $this->hashContents($fileContents);
		$this->updateMetadata($typeDir, $addOnId, $fileName, $metadata);

		return true;
	}

	public function removeMetadata($typeDir, $addOnId, $fileName)
	{
		$typeMetadata = $this->getMetadata($typeDir, $addOnId);
		if (isset($typeMetadata[$fileName]))
		{
			unset($typeMetadata[$fileName]);

			$this->writeTypeMetadata($typeDir, $addOnId, $typeMetadata);
		}
	}

	public function getFilePath($typeDir, $addOnId, $fileName)
	{
		$ds = \XF::$DS;
		$addOnIdDir = $this->prepareAddOnIdForPath($addOnId);
		return "{$this->basePath}{$ds}{$addOnIdDir}{$ds}_output{$ds}{$typeDir}{$ds}{$fileName}";
	}

	public function deleteFile($typeDir, $addOnId, $fileName)
	{
		if (!$this->enabled || $this->isAddOnSkipped($addOnId))
		{
			return false;
		}

		$fullPath = $this->getFilePath($typeDir, $addOnId, $fileName);
		if (file_exists($fullPath))
		{
			unlink($fullPath);
		}

		$this->removeMetadata($typeDir, $addOnId, $fileName);

		return true;
	}

	public function writeSpecialFile($addOnId, $fileName, $fileContents)
	{
		if (!$this->enabled || $this->isAddOnSkipped($addOnId))
		{
			return false;
		}

		$fullPath = $this->getSpecialFilePath($addOnId, $fileName);
		File::writeFile($fullPath, $fileContents, false);

		return true;
	}

	public function deleteSpecialFile($addOnId, $fileName)
	{
		if (!$this->enabled || $this->isAddOnSkipped($addOnId))
		{
			return false;
		}

		$fullPath = $this->getSpecialFilePath($addOnId, $fileName);
		if (file_exists($fullPath))
		{
			unlink($fullPath);
		}

		return true;
	}

	public function getSpecialFilePath($addOnId, $fileName)
	{
		$ds = \XF::$DS;
		$addOnIdDir = $this->prepareAddOnIdForPath($addOnId);
		return "{$this->basePath}{$ds}{$addOnIdDir}{$ds}_output{$ds}{$fileName}";
	}

	public function hashContents($contents)
	{
		$contents = str_replace("\r", '', $contents);
		return md5($contents);
	}

	public function renameAddOn($oldId, $newId)
	{
		if (!$this->enabled || $this->isAddOnSkipped($oldId)  || $this->isAddOnSkipped($newId))
		{
			return;
		}

		$changed = false;
		$dir = $this->basePath;
		if (file_exists($dir))
		{
			foreach (new \DirectoryIterator($dir) AS $entry)
			{
				/** @var \DirectoryIterator $entry */
				if ($entry->isDir() && !$entry->isDot() && $entry->getBasename() == $oldId)
				{
					rename($entry->getPathname(), $entry->getPath() . \XF::$DS . $newId);
					$changed = true;
				}
			}
		}

		if ($changed)
		{
			$this->typeLoaded = [];
			$this->metadataCache = [];
		}
	}

	public function deleteAddOn($addOnId)
	{
		if (!$this->enabled || $this->isAddOnSkipped($addOnId))
		{
			return;
		}

		$changed = false;
		$dir = $this->basePath;
		if (file_exists($dir))
		{
			foreach (new \DirectoryIterator($dir) AS $entry)
			{
				/** @var \DirectoryIterator $entry */
				if ($entry->isDir() && !$entry->isDot() && $entry->getBasename() == $addOnId)
				{
					\XF\Util\File::deleteDirectory($entry->getPathname());
					$changed = true;
				}
			}
		}

		if ($changed)
		{
			$this->typeLoaded = [];
			$this->metadataCache = [];
		}
	}

	public function isEnabled()
	{
		return $this->enabled;
	}

	public function isAddOnSkipped($addOnId)
	{
		if (!$addOnId || isset($this->skipAddOns[$addOnId]))
		{
			return true;
		}

		if (isset($this->skipAddOns['XF*']) && preg_match('#^XF[a-z0-9_]*$#i', $addOnId))
		{
			return true;
		}

		return false;
	}

	public function getSkippedAddOns()
	{
		return array_keys($this->skipAddOns);
	}

	public function isCoreXfDataAvailable()
	{
		if ($this->isAddOnSkipped('XF'))
		{
			return false;
		}

		// this is likely to be the smallest metadata so easiest to load
		$metadata = $this->getMetadata('help_pages', 'XF');
		return (bool)$metadata;
	}

	public function isAddOnOutputAvailable($addOnId)
	{
		$addOnIdDir = $this->prepareAddOnIdForPath($addOnId);
		$ds = \XF::$DS;

		$outputPath = $this->basePath . $ds . $addOnIdDir . $ds . '_output';

		if ($this->isAddOnSkipped($addOnId) || !$this->enabled || !file_exists($outputPath))
		{
			return false;
		}

		// Returns false  if the directory is totally empty
		return (new \FilesystemIterator($outputPath))->valid();
	}
}