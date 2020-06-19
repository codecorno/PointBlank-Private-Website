<?php

namespace XF\Service\AddOn;

use XF\AddOn\AddOn;
use XF\Util\File;

class HashGenerator extends \XF\Service\AbstractService
{
	/**
	 * @var AddOn
	 */
	protected $addOn;

	protected $rootPath;
	protected $hashesPath;

	protected $writeHashes = true;

	protected $filesToHash = [];
	protected $filesPrepared = false;

	public function __construct(\XF\App $app, AddOn $addOn, $rootPath, $hashesPath = null)
	{
		parent::__construct($app);

		$this->addOn = $addOn;
		$this->rootPath = $rootPath;
		$this->hashesPath = $hashesPath;
	}

	public function setWriteHashes($writeHashes)
	{
		$this->writeHashes = $writeHashes;
	}

	protected function prepareFilesToHash()
	{
		$rootPath = $this->rootPath;

		$files = File::getRecursiveDirectoryIterator($rootPath);
		foreach ($files AS $file)
		{
			if ($file->isDir())
			{
				continue;
			}

			$fileName = $file->getFilename();

			// skip hidden dot files, e.g. .DS_Store, .gitignore etc.
			if ($fileName[0] == '.' && $fileName != '.htaccess')
			{
				continue;
			}

			// don't hash the hashes file if it already exists
			if ($fileName == 'hashes.json')
			{
				continue;
			}

			$this->filesToHash[] = $file->getPathname();
		}

		$this->filesPrepared = true;
	}

	/**
	 * Generates the hashes for the given root path. Optionally writes to the specified path (if provided).
	 *
	 * @return array|string The generated hashes JSON
	 *
	 * @throws \ErrorException
	 */
	public function generate()
	{
		if (!$this->filesPrepared)
		{
			$this->prepareFilesToHash();
		}

		$output = [];

		foreach ($this->filesToHash AS $path)
		{
			if (!file_exists($path))
			{
				continue;
			}

			$path = $this->standardizeSeparator($path);
			$root = $this->standardizeSeparator($this->rootPath);

			$key = preg_replace('#^' . $root . '/#', '', $path, 1);
			$output[$key] = \XF\Util\Hash::hashTextFile($path, 'sha256');
		}

		ksort($output, SORT_NATURAL | SORT_FLAG_CASE);

		$output = \XF\Util\Json::jsonEncodePretty($output);

		if ($this->writeHashes)
		{
			if (!$this->hashesPath)
			{
				throw new \InvalidArgumentException('Trying to write hashes file, but no hashes path provided.');
			}

			$written = File::writeFile($this->hashesPath, $output, false);
			if (!$written)
			{
				throw new \ErrorException('Unexpected failure while writing hashes to provided path.');
			}
		}

		return $output;
	}

	protected function standardizeSeparator($path)
	{
		return str_replace('\\', '/', $path);
	}
}