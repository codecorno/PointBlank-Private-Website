<?php

namespace XF\Util;

class File
{
	protected static $tempFiles = [];
	protected static $tempCleanUpRegistered = false;

	protected static $tempDirs = [];
	protected static $tempDirCleanUpRegistered = false;

	protected static $defaultFilePermissions = null;

	public static function getCodeCachePath()
	{
		$config = \XF::app()->config();

		return self::canonicalizePath(
			sprintf($config['codeCachePath'], $config['internalDataPath'])
		);
	}

	public static function getTempDir()
	{
		$config = \XF::app()->config();
		$path = self::canonicalizePath(
			sprintf($config['tempDataPath'], $config['internalDataPath'])
		);
		if (!is_dir($path))
		{
			self::createDirectory($path);
		}

		return $path;
	}

	public static function getTempFile($autoCleanUp = true)
	{
		$dir = self::getTempDir();
		$file = tempnam($dir, 'xf');

		if ($autoCleanUp)
		{
			self::registerTempFile($file);
		}

		return $file;
	}

	public static function getNamedTempFile($fileName, $autoCleanUp = true)
	{
		$file = self::getTempDir() . '/' . $fileName;

		if ($autoCleanUp)
		{
			self::registerTempFile($file);
		}

		return $file;
	}

	public static function createTempDir($autoCleanUp = true)
	{
		$dir = self::getTempDir() . '/' . 'xf' . substr(md5(uniqid()), 0, 6);

		self::createDirectory($dir, false);
		if ($autoCleanUp)
		{
			self::registerTempDirectory($dir);
		}

		return $dir;
	}

	protected static function registerTempFile($file)
	{
		self::$tempFiles[] = $file;

		if (!self::$tempCleanUpRegistered)
		{
			register_shutdown_function([__CLASS__, 'cleanUpTempFiles']);
			self::$tempCleanUpRegistered = true;
		}
	}

	protected static function registerTempDirectory($dir)
	{
		self::$tempDirs[] = $dir;

		if (!self::$tempDirCleanUpRegistered)
		{
			register_shutdown_function([__CLASS__, 'cleanUpTempDirs']);
			self::$tempDirCleanUpRegistered = true;
		}
	}

	public static function cleanUpTempFiles()
	{
		foreach (self::$tempFiles AS $file)
		{
			try
			{
				@unlink($file);
			}
			catch (\Exception $e) {}
		}

		self::$tempFiles = [];
	}

	public static function cleanUpTempDirs()
	{
		foreach (self::$tempDirs AS $dir)
		{
			try
			{
				self::deleteDirectory($dir);
			}
			catch (\Exception $e) {}
		}

		self::$tempDirs = [];
	}

	public static function cleanUpPersistentTempFiles($cutOff = null)
	{
		$tempDir = self::getTempDir();
		if (!file_exists($tempDir) || !is_dir($tempDir))
		{
			return;
		}

		if ($cutOff === null)
		{
			$cutOff = \XF::$time - 3 * 86400;
		}

		$timer = new \XF\Timer(3);

		$dir = new \DirectoryIterator($tempDir);
		foreach ($dir AS $file)
		{
			if (!$file->isFile() || $file->getFilename() == 'index.html')
			{
				continue;
			}

			$mTime = $file->getMTime();
			if ($mTime && $mTime < $cutOff)
			{
				@unlink($file->getPathname());
			}

			if ($timer->limitExceeded())
			{
				break;
			}
		}
	}

	public static function copyStreamToTempFile($sourceResource, $autoCleanUp = true)
	{
		$tempFile = self::getTempFile($autoCleanUp);

		$tempResource = fopen($tempFile, 'w');
		stream_copy_to_stream($sourceResource, $tempResource);
		fclose($tempResource);

		return $tempFile;
	}

	public static function copyAbstractedPathToTempFile($abstractedPath, $autoCleanUp = true)
	{
		$stream = \XF::app()->fs()->readStream($abstractedPath);
		$tempFile = self::copyStreamToTempFile($stream, $autoCleanUp);
		@fclose($stream); // some streams may close after reading

		return $tempFile;
	}

	public static function copyFileToAbstractedPath($file, $abstractedPath, array $config = [])
	{
		$fp = fopen($file, 'r');
		$result = \XF::app()->fs()->putStream($abstractedPath, $fp, $config);
		fclose($fp);

		return $result;
	}

	public static function writeToAbstractedPath($abstractedPath, $contents, array $config = [], $allowOverwrite = true)
	{
		$fs = \XF::app()->fs();

		if ($allowOverwrite)
		{
			return $fs->put($abstractedPath, $contents, $config);
		}
		else
		{
			return $fs->write($abstractedPath, $contents, $config);
		}
	}

	public static function deleteFromAbstractedPath($abstractedPath)
	{
		try
		{
			\XF::app()->fs()->delete($abstractedPath);
		}
		catch (\League\Flysystem\FileNotFoundException $e) {}
	}

	public static function deleteAbstractedDirectory($abstractedDir)
	{
		try
		{
			\XF::app()->fs()->deleteDir($abstractedDir);
		}
		catch (\League\Flysystem\FileNotFoundException $e) {}
	}

	public static function abstractedPathExists($abstractedPath)
	{
		return \XF::app()->fs()->has($abstractedPath);
	}

	public static function createDirectory($path, $createIndexHtml = true)
	{
		if (file_exists($path))
		{
			if (is_dir($path))
			{
				return true;
			}

			throw new \LogicException("Attempting to created directory $path but a non-directory already exists at $path");
		}

		$path = str_replace('\\', '/', $path);
		$path = rtrim($path, '/');
		$parts = explode('/', $path);
		$pathPartCount = count($parts);
		$partialPath = '';

		$rootDir = str_replace('\\', '/', \XF::getRootDirectory());

		// find the "lowest" part that exists (and is a dir)...
		for ($i = $pathPartCount - 1; $i >= 0; $i--)
		{
			$partialPath = implode('/', array_slice($parts, 0, $i + 1));

			if (file_exists($partialPath))
			{
				if (!is_dir($partialPath))
				{
					throw new \LogicException("Attempting to created directory $path but a non-directory already exists at $partialPath");
				}
				else
				{
					break;
				}
			}

			if ($partialPath == $rootDir)
			{
				return false; // can't go above the root dir
			}
		}
		if ($i < 0)
		{
			return false;
		}

		$i++; // skip over the last entry (as it exists)

		// ... now create directories for anything below it
		for (; $i < $pathPartCount; $i++)
		{
			$partialPath .= '/' . $parts[$i];
			if (!mkdir($partialPath))
			{
				return false;
			}

			self::makeWritableByFtpUser($partialPath);

			if ($createIndexHtml)
			{
				if (@file_put_contents($partialPath . '/index.html', ' '))
				{
					self::makeWritableByFtpUser($partialPath . '/index.html');
				}
			}
		}

		return true;
	}

	public static function writeFile($file, $data, $createIndexHtml = true)
	{
		$dir = dirname($file);
		if (self::createDirectory($dir, $createIndexHtml))
		{
			if (file_put_contents($file, $data) !== false)
			{
				self::makeWritableByFtpUser($file);
				return true;
			}
			else
			{
				return false;
			}
		}
		else
		{
			return false;
		}
	}

	public static function copyFile($source, $destination, $createIndexHtml = true)
	{
		$dir = dirname($destination);
		if (self::createDirectory($dir, $createIndexHtml))
		{
			if (copy($source, $destination))
			{
				self::makeWritableByFtpUser($destination);
				return true;
			}
			else
			{
				return false;
			}
		}
		else
		{
			return false;
		}
	}

	/**
	 * Given a source and a destination, this function will iterate through the source directory, copying files and directories to destination.
	 * This function operates recursively.
	 * Generally shouldn't need to create index HTML files with this. But you may need to invalidate opcache.
	 *
	 * @param $source
	 * @param $destination
	 * @param bool $createIndexHtml
	 * @param bool $opcacheInvalidate
	 *
	 * @return bool
	 */
	public static function copyDirectory($source, $destination, $opcacheInvalidate = true, $createIndexHtml = false)
	{
		if (!is_dir($destination))
		{
			if (!self::createDirectory($destination, $createIndexHtml))
			{
				return false;
			}
		}

		foreach (new \DirectoryIterator($source) AS $file)
		{
			$newDestination = $destination . \XF::$DS . $file->getFilename();

			if (!$file->isDot() && $file->isDir())
			{
				self::copyDirectory(
					$file->getRealPath(),
					$newDestination,
					$createIndexHtml,
					$opcacheInvalidate
				);
			}
			else if ($file->isFile())
			{
				self::copyFile(
					$file->getRealPath(),
					$newDestination,
					$createIndexHtml
				);

				if ($opcacheInvalidate)
				{
					\XF\Util\Php::invalidateOpcodeCache($newDestination);
				}
			}
		}

		return true;
	}

	/**
	 * Performs a cross-stream/device safe rename by falling back
	 * to a copy+delete if needed.
	 *
	 * @param string $source
	 * @param string $destination
	 * @param bool $createIndexHtml
	 *
	 * @return bool
	 */
	public static function renameFile($source, $destination, $createIndexHtml = true)
	{
		$dir = dirname($destination);
		if (!self::createDirectory($dir, $createIndexHtml))
		{
			return false;
		}

		try
		{
			$success = rename($source, $destination);
		}
		catch (\Exception $e)
		{
			// possibly a perm problem, but may be an issue moving across streams, so copy+delete instead
			$success = false;
		}

		if (!$success)
		{
			$success = copy($source, $destination);
			if ($success)
			{
				@unlink($source);
			}
		}

		if ($success)
		{
			self::makeWritableByFtpUser($destination);
		}

		return $success;
	}

	public static function getDefaultFilePermissions()
	{
		if (self::$defaultFilePermissions)
		{
			return self::$defaultFilePermissions;
		}

		$chmod = \XF::app()->config('chmodWritableValue');
		if (!$chmod)
		{
			$selfWritable = null;

			if (PHP_SAPI == 'cli' && function_exists('posix_getuid'))
			{
				$lock = self::canonicalizePath(\XF::app()->config('internalDataPath') . '/install-lock.php');
				$uid = @posix_getuid();
				if (file_exists($lock))
				{
					// if we're running as who created the lock file,
					// then the web access is likely running with the same user
					$selfWritable = ($uid == fileowner($lock));
				}
				else if ($uid == 0)
				{
					// if we're root, just force w+w
					$selfWritable = false;
				}
			}

			if ($selfWritable === null)
			{
				$selfWritable = @is_writable(__FILE__);
			}

			if ($selfWritable)
			{
				// writable - probably owned by ftp user already
				$chmod = 0644;
			}
			else
			{
				// not writable, so file is probably owned by "nobody", need to w+w it
				$chmod = 0666;
			}
		}

		self::$defaultFilePermissions = [
			'file' => $chmod,
			'dir' => $chmod | 0111
		];

		return self::$defaultFilePermissions;
	}

	protected static $dirWritableCache = [];

	/**
	 * Determines if the named file is writable if it exists or if it can likely be created if it doesn't.
	 * Writable directory results will be cached and can be cleared via clearDirWritableCache().
	 *
	 * @param string $file
	 *
	 * @return bool
	 */
	public static function isWritable($file)
	{
		$file = rtrim(str_replace('\\', '/', $file), '/');

		if (file_exists($file))
		{
			return is_writable($file);
		}

		$rootDir = rtrim(str_replace('\\', '/', \XF::getRootDirectory()), '/');

		$dir = dirname($file);
		$originalDir = $dir;

		if (isset(self::$dirWritableCache[$dir]))
		{
			return self::$dirWritableCache[$dir];
		}

		while ($dir)
		{
			if (isset(self::$dirWritableCache[$dir]))
			{
				$result = self::$dirWritableCache[$dir];

				if (!isset(self::$dirWritableCache[$originalDir]))
				{
					self::$dirWritableCache[$originalDir] = $result;
				}

				return $result;
			}

			if (file_exists($dir))
			{
				$result = (is_dir($dir) && is_writable($dir));

				if (!isset(self::$dirWritableCache[$dir]))
				{
					self::$dirWritableCache[$dir] = $result;
				}
				self::$dirWritableCache[$originalDir] = $result;

				return $result;
			}

			if ($dir === $rootDir)
			{
				// can't go up a level
				return false;
			}

			$dir = dirname($dir);
		}

		return false;
	}

	/**
	 * Clears the writable directory cache values used by isWritable().
	 */
	public static function clearDirWritableCache()
	{
		self::$dirWritableCache = [];
	}

	/**
	 * Ensures that the specified file can be written to by the FTP user.
	 * This generally doesn't need to do anything if PHP is running via
	 * some form of suexec. It's primarily needed when running as apache.
	 *
	 * @param string $file
	 * @return boolean
	 */
	public static function makeWritableByFtpUser($file)
	{
		if (@is_readable($file))
		{
			$permissions = self::getDefaultFilePermissions();
			$chmod = @is_file($file) ? $permissions['file'] : $permissions['dir'];
			return @chmod($file, $chmod);
		}
		else
		{
			return false;
		}
	}

	public static function deleteDirectory($path)
	{
		$path = self::canonicalizePath($path);
		if (!is_readable($path) || !is_dir($path))
		{
			throw new \InvalidArgumentException("$path is not readable or not a directory");
		}

		if (!is_writable($path))
		{
			throw new \InvalidArgumentException("$path is not writable");
		}

		$files = self::getRecursiveDirectoryIterator($path);
		foreach ($files AS $file)
		{
			if ($file->isLink())
			{
				unlink($file->getPath() . \XF::$DS . $file->getFilename());
			}
			else if ($file->isDir())
			{
				rmdir($file->getRealPath());
			}
			else
			{
				unlink($file->getRealPath());
			}
		}

		rmdir($path);
	}

	public static function canonicalizePath($path, $root = null)
	{
		if (preg_match('#^(/|\\\\|[a-z0-9]+:)#i', $path))
		{
			return $path; // absolute already
		}

		if ($root === null)
		{
			$root = \XF::getRootDirectory();
		}

		return $root . \XF::$DS . $path;
	}

	public static function stripRootPathPrefix($path, $root = null)
	{
		if ($root === null)
		{
			$root = \XF::getRootDirectory();
		}
		$root = rtrim($root, '/\\') . \XF::$DS;

		$realPath = realpath($path);
		if ($realPath)
		{
			$path = realpath($path);
		}

		if (strpos($path, $root) === 0)
		{
			return strval(substr($path, strlen($root)));
		}
		else
		{
			return $path;
		}
	}

	/**
	 * @param $dir
	 * @param null $dirIteratorFlags
	 * @param null $iteratorMode
	 * @return \RecursiveIteratorIterator|\SplFileInfo[]
	 */
	public static function getRecursiveDirectoryIterator($dir, $iteratorMode = \RecursiveIteratorIterator::CHILD_FIRST, $dirIteratorFlags = \RecursiveDirectoryIterator::SKIP_DOTS)
	{
		if ($dirIteratorFlags === null)
		{
			$dirIterator = new \RecursiveDirectoryIterator($dir);
		}
		else
		{
			$dirIterator = new \RecursiveDirectoryIterator($dir, $dirIteratorFlags);
		}

		if ($iteratorMode === null)
		{
			return new \RecursiveIteratorIterator($dirIterator);
		}
		else
		{
			return new \RecursiveIteratorIterator($dirIterator, $iteratorMode);
		}
	}

	/**
	 * Method for writing out a file log.
	 *
	 * @param string $logName 'foo' will write to {tempDataPath}/foo.log
	 * @param string $logEntry The string to write into the log. Line break not required.
	 * @param boolean $append Append the log entry to the end of the existing log. Otherwise, start again.
	 *
	 * @return boolean True on successful log write
	 */
	public static function log($logName, $logEntry, $append = true)
	{
		$logName = preg_replace('/[^a-z0-9._-]/i', '', $logName);
		$path = self::getTempDir();
		$file = "$path/$logName.log";

		if ($fp = @fopen($file, ($append ? 'a' : 'w')))
		{
			fwrite($fp, date('Y-m-d H:i:s') . ' ' . $logEntry . "\n");
			fclose($fp);

			return true;
		}

		return false;
	}

	public static function writeInstallLock()
	{
		$contents = '<?php header(\'Location: ../index.php\'); /* Installed: ' . date(DATE_RFC822) . ' */';
		\XF::fs()->write('internal-data://install-lock.php', $contents);
	}

	public static function installLockExists()
	{
		try
		{
			// If this path doesn't exist, then this will throw an exception. We need to handle this elsewhere.
			return \XF::fs()->has('internal-data://install-lock.php');
		}
		catch (\Exception $e)
		{
			return false;
		}
	}

	/**
	 * Gets a file's extension in lower case. This only includes the last
	 * extension (eg, x.tar.gz -> gz).
	 *
	 * @param string $filename
	 *
	 * @return string
	 */
	public static function getFileExtension($filename)
	{
		return strtolower(pathinfo($filename, PATHINFO_EXTENSION));
	}

	public static function isImageInlineDisplaySafe($extension, &$contentType = null)
	{
		if (!$extension)
		{
			return false;
		}

		$extension = strtolower($extension);

		$types = \XF::app()->inlineImageTypes;

		if (!isset($types[$extension]))
		{
			return false;
		}

		$contentType = $types[$extension];

		return true;
	}

	public static function isVideoInlineDisplaySafe($extension, &$contentType = null)
	{
		if (!$extension)
		{
			return false;
		}

		$extension = strtolower($extension);

		$types = \XF::app()->inlineVideoTypes;

		if (!isset($types[$extension]))
		{
			return false;
		}

		$contentType = $types[$extension];

		return true;
	}
}