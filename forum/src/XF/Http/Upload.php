<?php

namespace XF\Http;

class Upload
{
	protected $tempFile;
	protected $fileName;
	protected $extension;
	protected $fileSize;
	protected $uploadError;

	protected $extraErrors = [];

	protected $isImage = false;
	protected $imageWidth = 0;
	protected $imageHeight = 0;
	protected $imageType = null;
	protected $exif = null;

	protected $imageContentUnsafe = false;

	protected $isVideo = false;
	protected $videoType = null;

	protected $allowedExtensions = null;
	protected $maxFileSize = null;
	protected $maxVideoSize = null;
	protected $maxWidth = null;
	protected $maxHeight = null;
	protected $imageRequired = false;
	protected $requireValidVideo = true;

	protected $transformed = false;

	public function __construct($tempFile, $fileName, $uploadError = 0)
	{
		if ($tempFile && (!file_exists($tempFile) || !is_readable($tempFile)))
		{
			throw new \InvalidArgumentException("Temporary file '$tempFile' can not be read or found");
		}

		$this->setFileName($fileName);

		$this->uploadError = $uploadError;

		$this->tempFile = $tempFile;
		$this->fileSize = $tempFile ? filesize($tempFile) : 0;
		$this->analyze();
	}

	public function setFileName($fileName)
	{
		$this->fileName = $fileName;
		$this->extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
	}

	public function getFileName()
	{
		return $this->fileName;
	}

	public function getExtension()
	{
		return $this->extension;
	}

	public function getFileSize()
	{
		return $this->fileSize;
	}

	public function getTempFile()
	{
		return $this->tempFile;
	}

	public function getUploadError()
	{
		return $this->uploadError;
	}

	public function requireImage()
	{
		$this->imageRequired = true;
		$this->allowedExtensions = array_keys($this->getImageExtensionMap());

		return $this;
	}

	public function allowInvalidVideo()
	{
		// this may be used when transcoding videos that we may not match
		$this->requireValidVideo = false;

		return $this;
	}

	public function setMaxImageDimensions($maxWidth, $maxHeight)
	{
		$this->maxWidth = $maxWidth;
		$this->maxHeight = $maxHeight;

		return $this;
	}

	public function setMaxFileSize($maxSize)
	{
		$this->maxFileSize = $maxSize;

		return $this;
	}

	public function setMaxVideoSize($maxVideoSize)
	{
		$this->maxVideoSize = $maxVideoSize;
	}

	public function setAllowedExtensions(array $extensions = null)
	{
		if (is_array($extensions))
		{
			$extensions = array_map('strtolower', $extensions);
		}

		$this->allowedExtensions = $extensions;

		return $this;
	}

	public function applyConstraints(array $constraints)
	{
		if (isset($constraints['extensions']) && is_array($constraints['extensions']))
		{
			$this->setAllowedExtensions($constraints['extensions']);
		}

		if (isset($constraints['size']))
		{
			$this->setMaxFileSize($constraints['size']);
		}

		if (isset($constraints['video_size']))
		{
			$this->setMaxVideoSize($constraints['video_size']);
		}

		if (isset($constraints['width']) || isset($constraints['height']))
		{
			$this->setMaxImageDimensions(
				isset($constraints['width']) ? $constraints['width'] : null,
				isset($constraints['height']) ? $constraints['height'] : null
			);
		}

		return $this;
	}

	protected function analyze()
	{
		if (!$this->tempFile || !$this->fileSize)
		{
			return;
		}

		$this->analyzeVideo();
		$this->analyzeImage();

		$fp = @fopen($this->tempFile, 'rb');
		if ($fp)
		{
			$previous = '';
			while (!@feof($fp))
			{
				$content = fread($fp, 256000);
				$test = $previous . $content;
				$exists = (
					strpos($test, '<?php') !== false
					|| preg_match('/<script\s+language\s*=\s*(php|"php"|\'php\')\s*>/i', $test)
				);
				if ($exists)
				{
					$this->imageContentUnsafe = true;
					break;
				}

				$previous = $content;
			}

			@fclose($fp);
		}
	}

	protected function analyzeImage()
	{
		$this->isImage = false;

		$map = $this->getImageExtensionMap();
		if (!isset($map[$this->extension]))
		{
			// require image extension to even try anything
			return;
		}

		$imageInfo = @getimagesize($this->tempFile);
		if (!$imageInfo)
		{
			return;
		}

		$imageType = $imageInfo[2];
		switch ($imageType)
		{
			case IMAGETYPE_GIF:
			case IMAGETYPE_JPEG:
			case IMAGETYPE_PNG:
				break;

			default:
				return;
		}

		if ($imageType != $map[$this->extension])
		{
			foreach ($map AS $newExtension => $extensionType)
			{
				if ($imageType == $extensionType)
				{
					$this->setFileName(pathinfo($this->fileName, PATHINFO_FILENAME) . ".$newExtension");
					break;
				}
			}
		}

		$this->isImage = true;
		$this->imageType = $imageType;
		$this->imageWidth = $imageInfo[0];
		$this->imageHeight = $imageInfo[1];

		if ($imageType == IMAGETYPE_JPEG && function_exists('exif_read_data'))
		{
			@ini_set('exif.encode_unicode', 'UTF-8');
			$exif = @exif_read_data($this->tempFile, null, true);
			$this->exif = $exif ?: [];
		}
		else
		{
			$this->exif = [];
		}
	}

	protected function analyzeVideo()
	{
		$this->isVideo = false;

		if (!$this->hasVideoExtension())
		{
			return;
		}

		$fp = @fopen($this->tempFile, 'rb');
		if (!$fp)
		{
			return;
		}

		$preamble = fread($fp, 1024);
		fclose($fp);

		$first4 = substr($preamble, 0, 4);
		$first8 = substr($preamble, 0, 8);

		$videoType = null;

		$mp4Ftypes = [
			'avc1', 'iso2', 'iso6', 'isom', 'mmp4', 'msnv',
			'ndas', 'ndsc', 'ndsh', 'ndsm', 'ndsp', 'ndss', 'ndxc', 'ndxh',
			'ndxm', 'ndxp', 'ndxs', 'XAVC'
			// mp41/2 handled via regex
		];
		$mp4FtypesRegex = implode('|', $mp4Ftypes);

		// Signatures adapted from: https://www.garykessler.net/library/file_sigs.html
		if (preg_match('#^(....)ftyp(M4V |qt  |mp4[0-9]|' . $mp4FtypesRegex . ')#', $preamble, $match))
		{
			switch ($match[2])
			{
				case 'M4V ':
					$videoType = 'm4v';
					break;

				case 'qt  ':
					$videoType = 'mov';
					break;

				default:
					$videoType = 'mp4';
			}
		}
		else if (preg_match('#^....(moov)#', $preamble))
		{
			$videoType = 'mov';
		}
		else if ($first8 === "OggS\x00\x02\x00\x00")
		{
			$videoType = 'ogv';
		}
		else if ($first4 === "\x1A\x45\xDF\xA3") // MKV, which WebM is a subset of
		{
			$videoType = 'webm';
		}
		else if (preg_match('#^RIFF....CDXA#', $preamble))
		{
			$videoType = 'mpg';
		}
		else if ($first4 === "\x00\x00\x01\xBA" || $first4 === "\x00\x00\x01\xB3")
		{
			$videoType = 'mpg';
		}

		if (!$videoType)
		{
			return;
		}

		if ($this->extension !== $videoType)
		{
			$this->setFileName(pathinfo($this->fileName, PATHINFO_FILENAME) . ".$videoType");
		}

		$this->isVideo = true;
		$this->videoType = $videoType;
	}

	public function transformImage()
	{
		if ($this->transformed)
		{
			return $this;
		}
		$this->transformed = true;

		if (!$this->isImage || $this->imageContentUnsafe)
		{
			// do nothing, just let it error
			return $this;
		}

		$orientation = 0;
		if ($this->exif && !empty($this->exif['IFD0']['Orientation']) && $this->exif['IFD0']['Orientation'] > 1)
		{
			$orientation = $this->exif['IFD0']['Orientation'];
		}
		$transformRequired = ($orientation > 1);

		$maxWidth = $this->maxWidth;
		$maxHeight = $this->maxHeight;

		if ($orientation >= 5 && $orientation <= 8)
		{
			// after rotation the X and Y coords will be reversed,
			// so flip the limits to reflect the "after" value
			$maxHeight = $this->maxWidth;
			$maxWidth = $this->maxHeight;
		}

		$resizeRequired = (
			($maxWidth && $this->imageWidth > $maxWidth)
			|| ($maxHeight && $this->imageHeight > $maxHeight)
		);

		if ($resizeRequired || $transformRequired)
		{
			$imageManager = \XF::app()->imageManager();
			if ($imageManager->canResize($this->imageWidth, $this->imageHeight))
			{
				$image = $imageManager->imageFromFile($this->tempFile);
				if ($image)
				{
					if ($resizeRequired)
					{
						$image->resize($maxWidth ?: $maxHeight, $maxHeight ?: null);
					}
					if ($transformRequired)
					{
						$image->transformByExif($orientation);
					}

					if ($image->save($this->tempFile))
					{
						$this->imageWidth = $image->getWidth();
						$this->imageHeight = $image->getHeight();
						clearstatcache();
						$this->fileSize = filesize($this->tempFile);
					}
				}
				else
				{
					// treat as non-image
					$this->isImage = false;
				}
			}
		}

		return $this;
	}

	public function isImage()
	{
		return $this->isImage && $this->hasImageExtension();
	}

	public function isVideo()
	{
		return $this->isVideo && $this->hasVideoExtension();
	}

	public function getVideoType()
	{
		return $this->videoType;
	}

	public function getImageType()
	{
		return $this->imageType;
	}

	public function getImageWidth()
	{
		return $this->imageWidth;
	}

	public function getImageHeight()
	{
		return $this->imageHeight;
	}

	public function isValid(&$errors = [])
	{
		$this->transformImage();

		$errors = [];

		if ($this->uploadError)
		{
			$errors['server'] = $this->getServerUploadError();
			return false;
		}

		if (!$this->tempFile)
		{
			$errors['server'] = \XF::phrase('uploaded_file_failed_not_found');
			return false;
		}

		if (!$this->fileSize)
		{
			$errors['fileSize'] = \XF::phrase('uploaded_file_empty_please_try_a_different_file');
			return false;
		}

		$isImage = $this->isImage();
		$isVideo = $this->isVideo();

		if ($this->imageRequired && !$isImage)
		{
			$errors['image'] = \XF::phrase('uploaded_file_must_be_valid_image');
		}

		if ($this->allowedExtensions && !in_array($this->extension, $this->allowedExtensions))
		{
			$errors['extension'] = \XF::phrase('uploaded_file_does_not_have_an_allowed_extension');
		}
		else if (!$this->isImage && $this->hasImageExtension())
		{
			$errors['extension'] = \XF::phrase('the_uploaded_file_was_not_an_image_as_expected');
		}
		else if (!$this->isVideo && $this->hasVideoExtension() && $this->requireValidVideo)
		{
			$errors['extension'] = \XF::phrase('the_uploaded_file_was_not_a_video_as_expected');
		}

		if ($isVideo)
		{
			if ($this->maxVideoSize && $this->fileSize > $this->maxVideoSize)
			{
				$errors['fileSize'] = \XF::phrase('uploaded_file_is_too_large');
			}
		}
		else
		{
			if ($this->maxFileSize && $this->fileSize > $this->maxFileSize)
			{
				$errors['fileSize'] = \XF::phrase('uploaded_file_is_too_large');
			}
		}

		if ($isImage)
		{
			if ($this->imageContentUnsafe)
			{
				$errors['content'] = \XF::phrase('uploaded_image_contains_invalid_content');
			}

			if (
				($this->maxWidth && $this->imageWidth > $this->maxWidth)
				|| ($this->maxHeight && $this->imageHeight > $this->maxHeight)
			)
			{
				$errors['dimensions'] = \XF::phrase('uploaded_image_is_too_big');
			}
		}

		$errors = array_merge($this->extraErrors, $errors);

		return count($errors) == 0;
	}

	public function logError($key, $error)
	{
		$this->extraErrors[$key] = $error;
	}

	public function getFileWrapper()
	{
		if (!$this->tempFile)
		{
			throw new \LogicException("Cannot get file wrapper for invalid upload (no temp file)");
		}

		$wrapper = new \XF\FileWrapper($this->tempFile, $this->fileName);
		if (is_array($this->exif))
		{
			$wrapper->setExif($this->exif);
		}

		return $wrapper;
	}

	protected function hasVideoExtension()
	{
		$map = \XF::app()->inlineVideoTypes;
		return isset($map[$this->extension]);
	}

	protected function hasImageExtension()
	{
		$map = $this->getImageExtensionMap();
		return isset($map[$this->extension]);
	}

	protected function getImageExtensionMap()
	{
		return [
			'gif' => IMAGETYPE_GIF,
			'jpg' => IMAGETYPE_JPEG,
			'jpeg' => IMAGETYPE_JPEG,
			'jpe' => IMAGETYPE_JPEG,
			'png' => IMAGETYPE_PNG
		];
	}

	protected function getServerUploadError()
	{
		switch ($this->uploadError)
		{
			case UPLOAD_ERR_INI_SIZE:
				return \XF::phrase('uploaded_file_is_too_large_for_server_to_process');

			case UPLOAD_ERR_FORM_SIZE:
				return \XF::phrase('uploaded_file_is_too_large');

			case UPLOAD_ERR_PARTIAL:
				return \XF::phrase('uploaded_file_failed_partial_upload');

			case UPLOAD_ERR_NO_FILE:
				return \XF::phrase('uploaded_file_failed_not_found');

			case UPLOAD_ERR_NO_TMP_DIR:
				return \XF::phrase('uploaded_file_failed_tmp_dir');

			case UPLOAD_ERR_CANT_WRITE:
				return \XF::phrase('uploaded_file_failed_cant_write');

			case UPLOAD_ERR_EXTENSION:
				return \XF::phrase('uploaded_file_failed_extension_stopped_upload');

			default:
				return null;
		}
	}
}