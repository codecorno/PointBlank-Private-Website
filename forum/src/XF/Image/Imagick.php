<?php

namespace XF\Image;

class Imagick extends AbstractDriver
{
	/**
	 * @var \Imagick
	 */
	protected $imagick;

	public static function isDriverUsable()
	{
		return class_exists('Imagick');
	}

	protected function _imageFromFile($file, $type)
	{
		switch ($type)
		{
			case IMAGETYPE_GIF:
			case IMAGETYPE_JPEG:
			case IMAGETYPE_PNG:
				$image = new \Imagick($file);
				break;

			default:
				throw new \InvalidArgumentException("Unknown image type '$type'");
		}

		$this->setImage($image);

		return true;
	}

	protected function _createImage($width, $height)
	{
		$image = new \Imagick();
		$background = new \ImagickPixel('white');
		$image->newImage($width, $height, $background);

		$this->setImage($image);

		return true;
	}

	public function getImage()
	{
		return $this->imagick;
	}

	public function setImage(\Imagick $image)
	{
		$image->setImageBackgroundColor(new \ImagickPixel('transparent'));
		$image = $image->coalesceImages();

		$this->imagick = $image;

		$this->updateDimensions();
	}

	protected function updateDimensions()
	{
		$this->width = $this->imagick->getImageWidth();
		$this->height = $this->imagick->getImageHeight();
	}

	protected function isOldImagick()
	{
		// imagick module < 3 or ImageMagick < 6.3.2 don't support the 4th thumbnailImage param
		$oldImagick = version_compare(phpversion('imagick'), '3', '<');

		$version = $this->imagick->getVersion();
		if (preg_match('#ImageMagick (\d+\.\d+\.\d+)#i', $version['versionString'], $match))
		{
			if (version_compare($match[1], '6.3.2', '<'))
			{
				$oldImagick = true;
			}
		}

		return $oldImagick;
	}

	public function resizeTo($width, $height)
	{
		$scaleUp = ($width > $this->width || $height > $this->height);

		try
		{
			foreach ($this->imagick AS $frame)
			{
				if ($scaleUp)
				{
					$frame->resizeImage($width, $height, \Imagick::FILTER_QUADRATIC, .5, true);
				}
				else if ($this->isOldImagick())
				{
					$frame->thumbnailImage($width, $height, false);
				}
				else
				{
					$frame->thumbnailImage($width, $height, false, true);
				}
				$frame->setImagePage($width, $height, 0, 0);
			}

			$this->updateDimensions();
		}
		catch (\Exception $e) {}

		return $this;
	}

	public function crop($width, $height, $x = 0, $y = 0, $srcWidth = null, $srcHeight = null)
	{
		foreach ($this->imagick AS $frame)
		{
			$frame->cropImage($srcWidth ?: $width, $srcHeight ?: $height, $x, $y);
			if ($this->isOldImagick())
			{
				$frame->thumbnailImage($width, $height, false);
			}
			else
			{
				$frame->thumbnailImage($width, $height, false, true);
			}
			$frame->setImagePage($frame->getImageWidth(), $frame->getImageHeight(), 0, 0);
		}
		$this->updateDimensions();

		return $this;
	}

	public function rotate($angle)
	{
		foreach ($this->imagick AS $frame)
		{
			$frame->rotateImage(new \ImagickPixel('none'), $angle);
		}
		$this->updateDimensions();

		return $this;
	}

	public function flip($mode)
	{
		foreach ($this->imagick AS $frame)
		{
			switch ($mode)
			{
				case self::FLIP_HORIZONTAL:
					$frame->flopImage();
					break;

				case self::FLIP_VERTICAL:
					$frame->flipImage();
					break;

				case self::FLIP_BOTH:
					$frame->flopImage();
					$frame->flipImage();
					break;

				default:
					throw new \InvalidArgumentException("Unknown flip mode");
			}
		}

		$this->updateDimensions();

		return $this;
	}

	public function setOpacity($opacity)
	{
		foreach ($this->imagick AS $frame)
		{
			$frame->evaluateImage(\Imagick::EVALUATE_MULTIPLY, $opacity, \Imagick::CHANNEL_ALPHA);
		}

		return $this;
	}

	public function appendImageAt($x, $y, $toAppend)
	{
		if (!($toAppend instanceof \Imagick))
		{
			throw new \InvalidArgumentException('Image to append must be a valid Imagick object.');
		}

		foreach ($this->imagick AS $frame)
		{
			$frame->compositeImage($toAppend, $toAppend->getImageCompose(), $x, $y);
		}

		return $this;
	}

	protected function _unsharpMask($radius, $sigma, $amount, $threshold)
	{
		foreach ($this->imagick AS $frame)
		{
			$frame->unsharpMaskImage($radius, $sigma, $amount, $threshold);
		}

		return $this;
	}

	public function save($file, $format = null, $quality = null)
	{
		if ($format === null)
		{
			$format = $this->type;
		}

		if ($quality === null)
		{
			$quality = 85;
		}

		if (method_exists($this->imagick, 'getImageProfiles'))
		{
			$profiles = $this->imagick->getImageProfiles('icc');
			$this->imagick->stripImage();

			if ($profiles && !empty($profiles['icc']))
			{
				$this->imagick->setImageProfile('icc', $profiles['icc']);
			}
		}
		else
		{
			$this->imagick->stripImage();
		}

		switch ($format)
		{
			case IMAGETYPE_GIF:
				if (is_callable(array($this->imagick, 'optimizeimagelayers')))
				{
					$optimized = @$this->imagick->optimizeimagelayers();
					if ($optimized instanceof \Imagick)
					{
						$this->imagick = $optimized;
					}

					$deconstructed = @$this->imagick->deconstructImages();
					if ($deconstructed instanceof \Imagick)
					{
						$this->imagick = $deconstructed;
					}
				}
				$success = $this->imagick->setImageFormat('gif');
				break;

			case IMAGETYPE_JPEG:
				$success = $this->imagick->setImageFormat('jpeg')
					&& $this->imagick->setImageCompression(\Imagick::COMPRESSION_JPEG)
					&& $this->imagick->setImageCompressionQuality($quality);
				break;

			case IMAGETYPE_PNG:
				$success = $this->imagick->setImageFormat('png');
				break;

			default:
				throw new \InvalidArgumentException('Invalid format given. Expects IMAGETYPE_XXX constant.');
		}

		if ($success)
		{
			try
			{
				return $this->imagick->writeImages($file, true);
			}
			catch (\ImagickException $e) {}
		}

		return false;
	}

	public function output($format = null, $quality = null)
	{
		if ($format === null)
		{
			$format = $this->type;
		}

		if ($quality === null)
		{
			$quality = 85;
		}

		$this->imagick->stripImage();

		switch ($format)
		{
			case IMAGETYPE_GIF:
				$success = $this->imagick->optimizeImageLayers();
				break;

			case IMAGETYPE_JPEG:
				$success = $this->imagick->setImageFormat('jpeg')
					&& $this->imagick->setImageCompression(\Imagick::COMPRESSION_JPEG)
					&& $this->imagick->setImageCompressionQuality($quality);
				break;

			case IMAGETYPE_PNG:
				$success = $this->imagick->setImageFormat('png');
				break;

			default:
				throw new \InvalidArgumentException('Invalid format given. Expects IMAGETYPE_XXX constant.');
		}

		if ($success)
		{
			try
			{
				echo $this->imagick->getImagesBlob();
			}
			catch (\ImagickException $e) {}
		}
	}

	public function isValid()
	{
		return $this->imagick->valid();
	}

	public function __destruct()
	{
		if ($this->imagick)
		{
			$this->imagick->destroy();
			$this->imagick = null;
		}
	}
}