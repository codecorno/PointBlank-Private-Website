<?php

namespace XF\Image;

class Gd extends AbstractDriver
{
	protected $image;

	protected function _imageFromFile($file, $type)
	{
		$this->image = null;

		$image = null;

		// approximately 5 bytes per pixel, times a 1.2 fudge factor, times 2 to support a second copy
		// (such as for rotation via EXIF data)
		$memoryBuffer = ($this->width * $this->height * 5) * 1.2 * 2;
		$availableMemory = \XF::getAvailableMemory();
		if ($availableMemory && $availableMemory < $memoryBuffer)
		{
			\XF::increaseMemoryLimit($memoryBuffer - $availableMemory);
		}

		switch ($type)
		{
			case IMAGETYPE_GIF:
				if (!function_exists('imagecreatefromgif'))
				{
					return false;
				}
				$image = @imagecreatefromgif($file);
				break;

			case IMAGETYPE_JPEG:
				if (!function_exists('imagecreatefromjpeg'))
				{
					return false;
				}
				@ini_set('gd.jpeg_ignore_warning', 1); // not default until PHP 7.1
				$image = @imagecreatefromjpeg($file);
				break;

			case IMAGETYPE_PNG:
				if (!function_exists('imagecreatefrompng'))
				{
					return false;
				}
				$image = @imagecreatefrompng($file);
				break;

			default:
				throw new \InvalidArgumentException("Unknown image type '$type'");
		}

		if (!$image)
		{
			return false;
		}

		$this->setImage($image);

		return true;
	}

	protected function _createImage($width, $height)
	{
		$this->image = null;

		$image = imagecreatetruecolor($width, $height);
		$this->preallocateBackground($image);

		$this->setImage($image);

		return $this;
	}

	public function resizeTo($width, $height)
	{
		$newImage = imagecreatetruecolor($width, $height);
		$this->preallocateBackground($newImage);

		imagecopyresampled(
			$newImage, $this->image,
			0, 0, 0, 0,
			$width, $height, $this->width, $this->height
		);
		$this->setImage($newImage);

		return $this;
	}

	public function crop($width, $height, $x = 0, $y = 0, $srcWidth = null, $srcHeight = null)
	{
		$newImage = imagecreatetruecolor($width, $height);
		$this->preallocateBackground($newImage);

		imagecopyresampled(
			$newImage, $this->image,
			0, 0, $x, $y,
			$width, $height,
			$srcWidth ?: $width, $srcHeight ?: $height
		);
		$this->setImage($newImage);

		return $this;
	}

	public function rotate($angle)
	{
		$newImage = imagerotate($this->image, $angle * -1, 0);
		$this->setImage($newImage);

		return $this;
	}

	public function flip($mode)
	{
		$srcX = 0;
		$srcY = 0;
		$srcWidth = $this->width;
		$srcHeight = $this->height;

		switch ($mode)
		{
			case self::FLIP_HORIZONTAL:
				$srcX = $this->width - 1;
				$srcWidth = -$this->width;
				break;

			case self::FLIP_VERTICAL:
				$srcY = $this->height - 1;
				$srcHeight = -$this->height;
				break;

			case self::FLIP_BOTH:
				$srcX = $this->width - 1;
				$srcWidth = -$this->width;
				$srcY = $this->height - 1;
				$srcHeight = -$this->height;
				break;

			default:
				throw new \InvalidArgumentException("Unknown flip mode");
		}

		$newImage = imagecreatetruecolor($this->width, $this->height);
		imagealphablending($newImage, false);
		imagesavealpha($newImage, true);
		imagecopyresampled(
			$newImage, $this->image,
			0, 0, $srcX, $srcY,
			$this->width, $this->height, $srcWidth, $srcHeight
		);

		$this->setImage($newImage);

		return $this;
	}

	public function setOpacity($opacity)
	{
		imagealphablending($this->image, false);
		imagesavealpha($this->image, true);

		$opacity = 1 - $opacity;
		imagefilter($this->image, IMG_FILTER_COLORIZE, 0,0,0,127 * $opacity);

		$this->setImage($this->image);

		return $this;
	}

	public function appendImageAt($x, $y, $toAppend)
	{
		imagecopy($this->image, $toAppend, $x, $y, 0, 0, imagesx($toAppend), imagesy($toAppend));

		$this->setImage($this->image);

		return $this;
	}

	/**
	 * Unsharp mask algorithm
	 *
	 * @copyright 2003-2007 Torstein Hønsi
	 * @author thoensi_at_netcom_dot_no
	 *
	 * @param $amount
	 * @param $radius
	 * @param $threshold
	 */
	protected function _unsharpMask($radius, $sigma, $amount, $threshold)
	{
		// Attempt to calibrate the parameters to Photoshop:
		$amount = min($amount, 500) * 0.016;
		$radius = abs(round(min(50, $radius) * 2)); // Only integers make sense.
		$threshold = min(255, $threshold);

		if (!$radius || !function_exists('imageconvolution'))
		{
			return $this;
		}

		$w = $this->width;
		$h = $this->height;

		$imageCanvas = imagecreatetruecolor($w, $h);
		$imageBlur = imagecreatetruecolor($w, $h);

		$image = $this->image;

		// Gaussian blur matrix
		$matrix = [
			[1, 2, 1],
			[2, 4, 2],
			[1, 2, 1]
		];
		imagecopy ($imageBlur, $image, 0, 0, 0, 0, $w, $h);
		imageconvolution($imageBlur, $matrix, 16, 0);

		if ($threshold > 0)
		{
			// Calculate the difference between the blurred pixels and the original and set the pixels
			for ($x = 0; $x < $w-1; $x++)
			{
				// each row
				for ($y = 0; $y < $h; $y++)
				{
					// each pixel
					$rgbOrig = imagecolorat($image, $x, $y);
					$rOrig = (($rgbOrig >> 16) & 0xFF);
					$gOrig = (($rgbOrig >> 8) & 0xFF);
					$bOrig = ($rgbOrig & 0xFF);

					$rgbBlur = imagecolorat($imageBlur, $x, $y);

					$rBlur = (($rgbBlur >> 16) & 0xFF);
					$gBlur = (($rgbBlur >> 8) & 0xFF);
					$bBlur = ($rgbBlur & 0xFF);

					// When the masked pixels differ less from the original than the threshold specifies, they are set to their original value.
					$rNew = (abs($rOrig - $rBlur) >= $threshold)
						? max(0, min(255, ($amount * ($rOrig - $rBlur)) + $rOrig))
						: $rOrig;

					$gNew = (abs($gOrig - $gBlur) >= $threshold)
						? max(0, min(255, ($amount * ($gOrig - $gBlur)) + $gOrig))
						: $gOrig;

					$bNew = (abs($bOrig - $bBlur) >= $threshold)
						? max(0, min(255, ($amount * ($bOrig - $bBlur)) + $bOrig))
						: $bOrig;

					if (($rOrig != $rNew) || ($gOrig != $gNew) || ($bOrig != $bNew))
					{
						$pixCol = imagecolorallocate($image, $rNew, $gNew, $bNew);
						imagesetpixel($image, $x, $y, $pixCol);
					}
				}
			}
		}
		else
		{
			for ($x = 0; $x < $w; $x++)
			{
				// each row
				for ($y = 0; $y < $h; $y++)
				{
					// each pixel
					$rgbOrig = imagecolorat($image, $x, $y);
					$rOrig = (($rgbOrig >> 16) & 0xFF);
					$gOrig = (($rgbOrig >> 8) & 0xFF);
					$bOrig = ($rgbOrig & 0xFF);

					$rgbBlur = imagecolorat($imageBlur, $x, $y);

					$rBlur = (($rgbBlur >> 16) & 0xFF);
					$gBlur = (($rgbBlur >> 8) & 0xFF);
					$bBlur = ($rgbBlur & 0xFF);

					$rNew = ($amount * ($rOrig - $rBlur)) + $rOrig;
					if ($rNew > 255)
					{
						$rNew = 255;
					}
					else if ($rNew < 0)
					{
						$rNew = 0;
					}
					$gNew = ($amount * ($gOrig - $gBlur)) + $gOrig;
					if($gNew > 255)
					{
						$gNew = 255;
					}
					else if ($gNew < 0)
					{
						$gNew = 0;
					}
					$bNew = ($amount * ($bOrig - $bBlur)) + $bOrig;
					if ($bNew > 255)
					{
						$bNew = 255;
					}
					else if ($bNew < 0)
					{
						$bNew = 0;
					}
					$rgbNew = ($rNew << 16) + ($gNew << 8) + $bNew;
					imagesetpixel($image, $x, $y, $rgbNew);
				}
			}
		}

		imagedestroy($imageCanvas);
		imagedestroy($imageBlur);

		$this->setImage($image);

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

		switch ($format)
		{
			case IMAGETYPE_GIF:
				return imagegif($this->image, $file);

			case IMAGETYPE_JPEG:
				return imagejpeg($this->image, $file, $quality);

			case IMAGETYPE_PNG:
				imagealphablending($this->image, false);
				imagesavealpha($this->image, true);
				return imagepng($this->image, $file, 9, PNG_ALL_FILTERS); // "quality" seems to be misleading, always force 9

			default:
				throw new \InvalidArgumentException('Invalid format given. Expects IMAGETYPE_XXX constant.');
		}
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

		switch ($format)
		{
			case IMAGETYPE_GIF:
				return imagegif($this->image);

			case IMAGETYPE_JPEG:
				return imagejpeg($this->image, null, $quality);

			case IMAGETYPE_PNG:
				imagealphablending($this->image, false);
				imagesavealpha($this->image, true);
				return imagepng($this->image, null, 9, PNG_ALL_FILTERS); // "quality" seems to be misleading, always force 9

			default:
				throw new \InvalidArgumentException('Invalid format given. Expects IMAGETYPE_XXX constant.');
		}
	}

	public function isValid()
	{
		return $this->image ? true : false;
	}

	public function getImage()
	{
		return $this->image;
	}

	protected function setImage($image)
	{
		$this->image = $image;
		$this->width = imagesx($image);
		$this->height = imagesy($image);
	}

	protected function preallocateBackground($image)
	{
		imagesavealpha($image, true);
		$color = imagecolorallocatealpha($image, 255, 255, 255, 127);
		imagecolortransparent($image, $color);
		imagefill($image, 0, 0, $color);
	}

	public function __destruct()
	{
		if ($this->image)
		{
			imagedestroy($this->image);
			$this->image = null;
		}
	}
}