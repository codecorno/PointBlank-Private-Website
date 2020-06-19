<?php

namespace XF;

class LocalFsAdapter extends \League\Flysystem\Adapter\Local
{
	protected function ensureDirectory($root)
	{
		if (!is_dir($root))
		{
			$umask = umask(0);
			@mkdir($root, $this->permissionMap['dir']['public'], true);
			umask($umask);

			if (!is_dir($root)){
				throw new \League\Flysystem\Exception(sprintf('Impossible to create the root directory "%s".', $root));
			}

			if ($this->pathPrefix && strpos($root, $this->pathPrefix) === 0)
			{
				$this->createIndexHtmlRecursively(
					substr($root, strlen($this->pathPrefix))
				);
			}
			else if (!$this->pathPrefix)
			{
				// initializing, make sure the root has an index.html file
				$this->createIndexHtmlIfNeeded($root);
			}
		}

		return realpath($root);
	}

	protected function createIndexHtmlRecursively($subPath)
	{
		$subPath = trim(str_replace('\\', '/', $subPath), '/');
		if (!strlen($subPath))
		{
			$this->createIndexHtmlIfNeeded($this->pathPrefix);
			return;
		}

		$rollingPath = rtrim($this->pathPrefix, '/\\');
		foreach (explode('/', $subPath) AS $subDir)
		{
			$rollingPath .= "/$subDir";
			$this->createIndexHtmlIfNeeded($rollingPath);
		}
	}

	protected function createIndexHtmlIfNeeded($path)
	{
		$path = rtrim($path, '/\\');
		$file = $path . '/index.html';

		if (!file_exists($file))
		{
			file_put_contents($file, ' ');
			@chmod($file, $this->permissionMap['file']['public']);
			return true;
		}

		return false;
	}

	public function setVisibility($path, $visibility)
	{
		try
		{
			return parent::setVisibility($path, $visibility);
		}
		catch (\ErrorException $e)
		{
			// this is likely the chmod failing, so silence this as it shouldn't be a problem
			return false;
		}
	}
}