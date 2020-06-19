<?php

namespace XF;

use XF\Mvc\Entity\Entity;
use XF\Util\File;
use XF\Util\Json;

class DesignerOutput
{
	protected $enabled;
	protected $basePath;
	protected $metadataFilename = '_metadata.json';

	protected $metadataCache = [];

	protected $batchMode = false;
	protected $batchesPending = [];

	protected $handlerCache = [];

	public function __construct($enabled, $basePath)
	{
		$this->enabled = $enabled;
		$this->basePath = \XF\Util\File::canonicalizePath($basePath);
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

	public function import($shortName, $name, $styleId, $contents, array $metadata, array $options = [])
	{
		$handler = $this->getHandler($shortName);
		return $handler->import($name, $styleId, $contents, $metadata, $options);
	}

	public function delete(Entity $entity, $new = true)
	{
		$handler = $this->getHandler($entity->structure()->shortName);
		return $handler->delete($entity, $new);
	}

	/**
	 * @param $shortName
	 *
	 * @return \XF\DesignerOutput\AbstractHandler
	 */
	public function getHandler($shortName)
	{
		if (isset($this->handlerCache[$shortName]))
		{
			return $this->handlerCache[$shortName];
		}

		$class = \XF::stringToClass($shortName, '%s\DesignerOutput\%s');
		if (!class_exists($class))
		{
			throw new \InvalidArgumentException("No handler for $shortName found ($class expected)");
		}

		$class = \XF::extendClass($class);

		$handler = new $class($this, $shortName);
		if (!($handler instanceof \XF\DesignerOutput\AbstractHandler))
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
		foreach ($this->batchesPending AS $typeDir => $designerModeIds)
		{
			foreach ($designerModeIds AS $designerModeId => $null)
			{
				$typeMetadata = $this->metadataCache[$typeDir][$designerModeId];

				$metadataPath = $this->getMetadataFileName($typeDir, $designerModeId);
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

	public function getAvailableDesignerModeIds()
	{
		if (!file_exists($this->basePath))
		{
			return [];
		}

		$designerModeIds = [];
		foreach (new \DirectoryIterator($this->basePath) AS $entry)
		{
			if (!$this->isValidDir($entry))
			{
				continue;
			}

			$designerModeIds[] = $entry->getBasename();
		}

		return $designerModeIds;
	}

	public function getMetadata($typeDir, $designerMode)
	{
		if (isset($this->metadataCache[$typeDir][$designerMode]))
		{
			return $this->metadataCache[$typeDir][$designerMode];
		}

		$metadataPath = $this->getMetadataFileName($typeDir, $designerMode);
		if (file_exists($metadataPath))
		{
			$metadata = json_decode(file_get_contents($metadataPath), true);
		}
		else
		{
			$metadata = [];
		}

		$this->metadataCache[$typeDir][$designerMode] = $metadata;

		return $metadata;
	}

	protected function getMetadataFileName($typeDir, $designerMode)
	{
		$ds = \XF::$DS;
		return "{$this->basePath}{$ds}{$designerMode}{$ds}{$typeDir}{$ds}{$this->metadataFilename}";
	}

	protected function writeTypeMetadata($typeDir, $designerMode, array $typeMetadata)
	{
		ksort($typeMetadata);

		$this->metadataCache[$typeDir][$designerMode] = $typeMetadata;

		$metadataPath = $this->getMetadataFileName($typeDir, $designerMode);
		file_put_contents($metadataPath, Json::jsonEncodePretty($typeMetadata));

		if ($this->batchMode)
		{
			$this->batchesPending[$typeDir][$designerMode] = true;
		}
		else
		{
			$metadataPath = $this->getMetadataFileName($typeDir, $designerMode);
			file_put_contents($metadataPath, Json::jsonEncodePretty($typeMetadata));
		}
	}

	protected function updateMetadata($typeDir, $designerMode, $fileName, array $metadata)
	{
		$typeMetadata = $this->getMetadata($typeDir, $designerMode);

		$existing = isset($typeMetadata[$fileName]) ? $typeMetadata[$fileName] : null;
		if ($existing == $metadata)
		{
			return;
		}

		$typeMetadata[$fileName] = $metadata;
		$this->writeTypeMetadata($typeDir, $designerMode, $typeMetadata);
	}

	public function getTypes()
	{
		$types = [];
		$ds = \XF::$DS;
		$dir = $this->basePath;

		$designerModeIds = $this->getAvailableDesignerModeIds();
		foreach ($designerModeIds AS $designerModeId)
		{
			$designerModeDir = "{$dir}{$ds}{$designerModeId}";
			if (!file_exists($designerModeDir))
			{
				continue;
			}
			foreach (new \DirectoryIterator($designerModeDir) AS $designerModeEntry)
			{
				if (!$this->isValidDir($designerModeEntry))
				{
					continue;
				}

				$types[] = $designerModeEntry->getBasename();
			}
		}

		$types = array_unique($types);
		sort($types);
		return $types;
	}

	public function getAvailableTypeFiles($typeDir, $designerMode)
	{
		$files = [];

		$ds = \XF::$DS;
		$dir = $this->basePath;

		$designerModeTypeDir = "{$dir}{$ds}{$designerMode}{$ds}{$typeDir}";
		if (file_exists($designerModeTypeDir))
		{
			foreach (new \DirectoryIterator($designerModeTypeDir) AS $file)
			{
				/** @var \DirectoryIterator $file */
				$fileName = $file->getBasename();
				if (!$file->isDot() && $file->isFile() && $fileName != $this->metadataFilename && substr($fileName, 0, 1) != '.')
				{
					$files[$fileName] = $file->getPathname();
				}
				else if (!$file->isDot() && $file->isDir() && substr($fileName, 0, 1) != '.')
				{
					foreach (new \DirectoryIterator($file->getPathname()) AS $childFile)
					{
						if (!$childFile->isDot() && $childFile->isFile())
						{
							$files[$fileName . '/' . $childFile->getBasename()] = $childFile->getPathname();
						}
					}
				}
			}
		}

		return $files;
	}

	public function rebuildTypeMetadata($type, $designerMode)
	{
		$changes = [];

		$typeFiles = $this->getAvailableTypeFiles($type, $designerMode);

		$metadata = $this->getMetadata($type, $designerMode);
		$changed = false;

		foreach ($typeFiles AS $fileName => $path)
		{
			if (isset($metadata[$fileName]))
			{
				$data = $metadata[$fileName];
				$hash = $this->hashContents(file_get_contents($path));
				if (!isset($data['hash']) || $hash != $data['hash'])
				{
					$metadata[$fileName]['hash'] = $hash;
					$changes[$designerMode][] = $fileName;
					$changed = true;
				}
			}
		}

		if ($changed)
		{
			$this->writeTypeMetadata($type, $designerMode, $metadata);
		}

		return $changes;
	}

	public function writeFile($typeDir, \XF\Entity\Style $style, $fileName, $fileContents, array $metadata = [], $verifyChange = true)
	{
		if (!$this->isDesignerModeEnabled($style))
		{
			return false;
		}

		$fullPath = $this->getFilePath($typeDir, $style->designer_mode, $fileName);

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
		$this->updateMetadata($typeDir, $style->designer_mode, $fileName, $metadata);

		return true;
	}

	public function removeMetadata($typeDir, $designerMode, $fileName)
	{
		$typeMetadata = $this->getMetadata($typeDir, $designerMode);
		if (isset($typeMetadata[$fileName]))
		{
			unset($typeMetadata[$fileName]);

			$this->writeTypeMetadata($typeDir, $designerMode, $typeMetadata);
		}
	}

	public function getFilePath($typeDir, $designerMode, $fileName)
	{
		$ds = \XF::$DS;
		return "{$this->basePath}{$ds}{$designerMode}{$ds}{$typeDir}{$ds}{$fileName}";
	}

	public function getDesignerModePath($id)
	{
		$ds = \XF::$DS;
		return "{$this->basePath}{$ds}{$id}";
	}

	public function deleteFile($typeDir, \XF\Entity\Style $style, $fileName)
	{
		if (!$this->enabled)
		{
			return false;
		}

		$fullPath = $this->getFilePath($typeDir, $style->designer_mode, $fileName);
		if (file_exists($fullPath))
		{
			unlink($fullPath);
		}

		$this->removeMetadata($typeDir, $style->designer_mode, $fileName);

		return true;
	}

	public function writeSpecialFile($designerMode, $fileName, $fileContents)
	{
		if (!$this->enabled)
		{
			return false;
		}

		$fullPath = $this->getSpecialFilePath($designerMode, $fileName);
		File::writeFile($fullPath, $fileContents, false);

		return true;
	}

	public function deleteSpecialFile($designerMode, $fileName)
	{
		if (!$this->enabled)
		{
			return false;
		}

		$fullPath = $this->getSpecialFilePath($designerMode, $fileName);
		if (file_exists($fullPath))
		{
			unlink($fullPath);
		}

		return true;
	}

	public function getSpecialFilePath($designerMode, $fileName)
	{
		$ds = \XF::$DS;
		return "{$this->basePath}{$ds}{$designerMode}{$ds}{$fileName}";
	}

	public function hashContents($contents)
	{
		$contents = str_replace("\r", '', $contents);
		return md5($contents);
	}

	public function isEnabled()
	{
		return $this->enabled;
	}

	protected function isDesignerModeEnabled(\XF\Entity\Style $style)
	{
		return ($style->designer_mode && $this->enabled);
	}
}