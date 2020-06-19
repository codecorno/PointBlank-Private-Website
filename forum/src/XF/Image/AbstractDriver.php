<?php

namespace XF\Image;

abstract class AbstractDriver
{
	const ORIENTATION_LANDSCAPE = 'landscape';
	const ORIENTATION_PORTRAIT = 'portrait';
	const ORIENTATION_SQUARE = 'square';

	const FLIP_HORIZONTAL = 1;
	const FLIP_VERTICAL = 2;
	const FLIP_BOTH = 3;

	/**
	 * @var Manager
	 */
	protected $manager;

	protected $type;
	protected $width;
	protected $height;

	abstract protected function _imageFromFile($file, $type);
	abstract protected function _createImage($width, $height);

	abstract public function resizeTo($width, $height);
	abstract public function crop($width, $height, $x = 0, $y = 0, $srcWidth = null, $srcHeight = null);
	abstract public function rotate($angle);
	abstract public function flip($mode);
	abstract public function setOpacity($opacity);
	abstract public function appendImageAt($x, $y, $toAppend);

	abstract public function save($file, $format = null, $quality = null);
	abstract public function output($format = null, $quality = null);
	abstract public function isValid();

	public function __construct(Manager $manager)
	{
		$this->manager = $manager;
	}

	public static function isDriverUsable()
	{
		return true;
	}

	/**
	 * Resizes an image. If height is specified, aspect ratio is not maintained.
	 *
	 * @param      $width
	 * @param null $height
	 * @param bool $upsize
	 *
	 * @return $this
	 */
	public function resize($width, $height = null, $upsize = false)
	{
		$width = max(1, intval($width));
		if ($height === null)
		{
			$height = $width;
		}
		else
		{
			$height = max(1, intval($height));
		}

		if ($this->width < $width && $this->height < $height && !$upsize)
		{
			// already done
			return $this;
		}

		$ratio = $this->width / $this->height;
		$newRatio = ($width / $height);

		if ($newRatio > $ratio)
		{
			$newWidth = max(1, ceil($height * $ratio));
			$newHeight = $height;
		}
		else
		{
			$newWidth = $width;
			$newHeight = max(1, ceil($width / $ratio));
		}

		$this->resizeTo($newWidth, $newHeight);

		return $this;
	}

	public function resizeAndCrop($maxWidth, $maxHeight = null)
	{
		$maxWidth = max(1, intval($maxWidth));
		if ($maxHeight === null)
		{
			$maxHeight = $maxWidth;
		}
		else
		{
			$maxHeight = max(1, intval($maxHeight));
		}

		$newWidth = $this->height * $maxWidth / $maxHeight;
		$newHeight = $this->width * $maxHeight / $maxWidth;

		$x = 0;
		$y = 0;

		if ($newWidth > $this->width)
		{
			$y = (($this->height - $newHeight) / 2);
			$newWidth = $this->width;
		}
		else
		{
			$x = (($this->width - $newWidth) / 2);
			$newHeight = $this->height;
		}

		$this->crop($maxWidth, $maxHeight, $x, $y, $newWidth, $newHeight);

		return $this;
	}

	/**
	 * Resizes an image to a specific length of the shortest side, maintaining aspect ratio
	 *
	 * @param      $length
	 * @param bool $upsize
	 *
	 * @return $this
	 */
	public function resizeShortEdge($length, $upsize = false)
	{
		$length = max(1, intval($length));

		$ratio = $this->width / $this->height;
		if ($ratio > 1) // landscape
		{
			if ($this->height < $length && !$upsize)
			{
				return $this;
			}

			$width = ceil($length * $ratio);
			$height = $length;
		}
		else
		{
			if ($this->width < $length && !$upsize)
			{
				return $this;
			}

			$width = $length;
			$height = max(1, ceil($length / $ratio));
		}

		$this->resizeTo($width, $height);

		return $this;
	}

	/**
	 * Resizes an image to a specific height, maintaining aspect ratio
	 *
	 * @param      $height
	 * @param bool $upsize
	 *
	 * @return $this
	 */
	public function resizeHeight($height, $upsize = false)
	{
		$height = max(1, intval($height));

		if ($height > $this->height && !$upsize)
		{
			return $this;
		}

		$width = ceil($this->width * ($height / $this->height));

		$this->resizeTo($width, $height);

		return $this;
	}

	public function transformByExif($orientation)
	{
		$transforms = [
			2 => 'flip-h',
			3 => 180,
			4 => 'flip-v',
			5 => 'transpose',
			6 => 90,
			7 => 'transverse',
			8 => 270
		];

		if (isset($transforms[$orientation]))
		{
			$transform = $transforms[$orientation];
			switch ($transform)
			{
				case 'flip-h':
					$this->flip(self::FLIP_HORIZONTAL);
					break;

				case 'flip-v':
					$this->flip(self::FLIP_VERTICAL);
					break;

				case 'transpose':
					$this->rotate(90);
					$this->flip(self::FLIP_HORIZONTAL);
					break;

				case 'transverse':
					$this->rotate(90);
					$this->flip(self::FLIP_VERTICAL);
					break;

				default:
					if (is_int($transform))
					{
						$this->rotate($transform);
					}
					else
					{
						throw new \InvalidArgumentException('Invalid transform: ' . $transform);
					}
			}
		}
	}

	public function unsharpMask($radius = 2, $sigma = 0.5, $amount = 0.7, $threshold = 0)
	{
		return $this->_unsharpMask($radius, $sigma, $amount, $threshold);
	}

	protected function _unsharpMask($radius, $sigma, $amount, $threshold)
	{
		return $this;
	}

	/**
	 * @param string $file
	 *
	 * @return AbstractDriver
	 */
	public function imageFromFile($file)
	{
		if (!file_exists($file) || !is_readable($file))
		{
			throw new \InvalidArgumentException("File '$file' is invalid");
		}

		if (!filesize($file))
		{
			throw new \InvalidArgumentException("File '$file' is an empty file");
		}

		$imageInfo = @getimagesize($file);
		if (!$imageInfo)
		{
			throw new \InvalidArgumentException("File '$file' is not a valid image");
		}

		$type = $imageInfo[2];

		switch ($type)
		{
			case IMAGETYPE_GIF:
			case IMAGETYPE_JPEG:
			case IMAGETYPE_PNG:
				break;

			default:
				throw new \InvalidArgumentException("File '$file' is not a valid image type");
		}

		$this->type = $type;
		$this->width = $imageInfo[0];
		$this->height = $imageInfo[1];

		return $this->_imageFromFile($file, $type);
	}

	public function createImage($width, $height)
	{
		$this->width = $width;
		$this->height = $height;

		return $this->_createImage($width, $height);
	}

	public function getWidth()
	{
		return $this->width;
	}

	public function getHeight()
	{
		return $this->height;
	}

	public function getType()
	{
		return $this->type;
	}

	public function getOrientation()
	{
		$w = $this->getWidth();
		$h = $this->getHeight();

		if ($w == $h)
		{
			return self::ORIENTATION_SQUARE;
		}
		else if ($w > $h)
		{
			return self::ORIENTATION_LANDSCAPE;
		}
		else
		{
			return self::ORIENTATION_PORTRAIT;
		}
	}

	public function getDataUri($tempFile)
	{
		$this->save($tempFile);

		switch ($this->type)
		{
			case IMAGETYPE_GIF:
				$mimeType = 'image/gif';
				break;

			case IMAGETYPE_PNG:
				$mimeType = 'image/png';
				break;

			case IMAGETYPE_JPEG:
			default:
				$mimeType = 'image/jpeg';
				break;
		}

		$data = file_get_contents($tempFile);
		return 'data:' . $mimeType . ';base64,' . base64_encode($data);
	}

	public function isSquare()
	{
		return ($this->getWidth() == $this->getHeight());
	}
}