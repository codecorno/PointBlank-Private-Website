<?php

namespace XF\Image;

class Manager
{
	protected $drivers = [
		'gd' => '\XF\Image\Gd',
		'imPecl' => '\XF\Image\Imagick'
	];

	protected $defaultDriver = 'gd';

	protected $maxResizePixels = null;

	public function __construct($defaultDriver = 'gd', array $extraDrivers = [])
	{
		$this->addDrivers($extraDrivers);
		$this->setDefaultDriver($defaultDriver);
	}

	public function addDriver($name, $class)
	{
		$this->drivers[$name] = $class;
	}

	public function addDrivers(array $drivers)
	{
		foreach ($drivers AS $name => $class)
		{
			$this->addDriver($name, $class);
		}
	}

	public function setDefaultDriver($driver, $fallbackIfInvalid = true)
	{
		if (!$driver)
		{
			$driver = 'gd';
		}
		else if ($fallbackIfInvalid && !isset($this->drivers[$driver]))
		{
			$driver = 'gd';
		}

		$this->defaultDriver = $driver;
	}

	/**
	 * @param string $file
	 * @param null|string $driver
	 * @return null|AbstractDriver
	 */
	public function imageFromFile($file, $driver = null)
	{
		$image = $this->getImageDriver($driver);

		try
		{
			if ($image->imageFromFile($file))
			{
				return $image;
			}
		}
		catch (\Exception $e) {}

		return null;
	}

	public function createImage($width, $height, $driver = null)
	{
		$image = $this->getImageDriver($driver);

		try
		{
			if ($image->createImage($width, $height))
			{
				return $image;
			}
		}
		catch (\Exception $e) {}

		return null;
	}

	/**
	 * @param null $driver
	 * @return AbstractDriver
	 */
	protected function getImageDriver($driver = null)
	{
		if (!$driver)
		{
			$driver = $this->defaultDriver;
		}

		if (!isset($this->drivers[$driver]))
		{
			throw new \LogicException("Unknown driver '$driver'");
		}

		$driverClass = $this->drivers[$driver];
		$driverClass = \XF::extendClass($driverClass);

		if (!$driverClass::isDriverUsable() && $driver != 'gd')
		{
			return $this->getImageDriver('gd');
		}

		return new $driverClass($this);
	}

	public function canResize($width, $height)
	{
		if (!$this->maxResizePixels === null)
		{
			return true;
		}

		$total = $width * $height;
		return ($total <= $this->maxResizePixels);
	}

	public function setMaxResizePixels($max)
	{
		$this->maxResizePixels = $max;
	}
}