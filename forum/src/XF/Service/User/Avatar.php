<?php

namespace XF\Service\User;

use XF\Entity\User;

class Avatar extends \XF\Service\AbstractService
{
	/**
	 * @var User
	 */
	protected $user;

	protected $logIp = true;
	protected $logChange = true;

	protected $fileName;

	protected $width;

	protected $height;

	protected $cropX;
	protected $cropY;

	protected $type;

	protected $error = null;

	protected $allowedTypes = [IMAGETYPE_GIF, IMAGETYPE_JPEG, IMAGETYPE_PNG];

	protected $sizeMap;

	protected $throwErrors = true;

	const HIGH_DPI_THRESHOLD = 384;

	public function __construct(\XF\App $app, User $user)
	{
		parent::__construct($app);
		$this->setUser($user);

		$this->sizeMap = $this->app->container('avatarSizeMap');
	}

	protected function setUser(User $user)
	{
		if (!$user->user_id)
		{
			return $this->throwException(new \LogicException("User must be saved"));
		}

		$this->user = $user;
	}

	public function logIp($logIp)
	{
		$this->logIp = $logIp;
	}

	public function logChange($logChange)
	{
		$this->logChange = $logChange;
	}

	public function getError()
	{
		return $this->error;
	}

	public function silentRunning($runSilent)
	{
		$this->throwErrors = !$runSilent;
	}

	public function setImage($fileName)
	{
		if (!$this->validateImageAsAvatar($fileName, $error))
		{
			$this->error = $error;
			$this->fileName = null;
			return false;
		}

		$this->fileName = $fileName;
		return true;
	}

	public function setImageFromUpload(\XF\Http\Upload $upload)
	{
		$upload->requireImage();

		if (!$upload->isValid($errors))
		{
			$this->error = reset($errors);
			return false;
		}

		return $this->setImage($upload->getTempFile());
	}

	public function setImageFromExisting()
	{
		$path = $this->user->getAbstractedCustomAvatarPath('o');
		if (!$this->app->fs()->has($path))
		{
			return $this->throwException(new \InvalidArgumentException("User does not have an 'o' avatar ($path)"));
		}

		$tempFile = \XF\Util\File::copyAbstractedPathToTempFile($path);
		return $this->setImage($tempFile);
	}

	/**
	 * Sets the cropping values. These coordinates must be scaled to the medium size avatar (default 96px)!
	 * Using null will automatically crop at the middle.
	 *
	 * @param int|null $x
	 * @param int|null $y
	 */
	public function setCrop($x, $y)
	{
		if ($x === null || $y === null)
		{
			$this->cropX = $x;
			$this->cropY = $y;
		}
		else
		{
			$this->cropX = intval($x);
			$this->cropY = intval($y);
		}
	}

	public function getCrop()
	{
		return [$this->cropX, $this->cropY];
	}

	public function validateImageAsAvatar($fileName, &$error = null)
	{
		$error = null;

		if (!file_exists($fileName))
		{
			return $this->throwException(new \InvalidArgumentException("Invalid file '$fileName' passed to avatar service"));
		}
		if (!is_readable($fileName))
		{
			return $this->throwException(new \InvalidArgumentException("'$fileName' passed to avatar service is not readable"));
		}

		$imageInfo = filesize($fileName) ? @getimagesize($fileName) : false;
		if (!$imageInfo)
		{
			$error = \XF::phrase('provided_file_is_not_valid_image');
			return false;
		}

		$type = $imageInfo[2];
		if (!in_array($type, $this->allowedTypes))
		{
			$error = \XF::phrase('provided_file_is_not_valid_image');
			return false;
		}

		$width = $imageInfo[0];
		$height = $imageInfo[1];

		if (!$this->app->imageManager()->canResize($width, $height))
		{
			$error = \XF::phrase('uploaded_image_is_too_big');
			return false;
		}

		$this->width = $width;
		$this->height = $height;
		$this->type = $type;

		return true;
	}

	public function updateAvatar()
	{
		if (!$this->fileName)
		{
			return $this->throwException(new \LogicException("No source file for avatar set"));
		}
		if (!$this->user->exists())
		{
			return $this->throwException(new \LogicException("User does not exist, cannot update avatar"));
		}

		$imageManager = $this->app->imageManager();

		$outputFiles = [];
		$baseFile = $this->fileName;

		$origSize = $this->sizeMap['o'];
		$shortSide = min($this->width, $this->height);

		if ($shortSide > $origSize)
		{
			$image = $imageManager->imageFromFile($this->fileName);
			if (!$image)
			{
				return false;
			}

			$image->resizeShortEdge($origSize);

			$newTempFile = \XF\Util\File::getTempFile();
			if ($newTempFile && $image->save($newTempFile, null, 95))
			{
				$outputFiles['o'] = $newTempFile;
				$baseFile = $newTempFile;
				$width = $image->getWidth();
				$height = $image->getHeight();
			}
			else
			{
				return $this->throwException(new \RuntimeException("Failed to save image to temporary file; check internal_data/data permissions"));
			}

			unset($image);
		}
		else
		{
			$outputFiles['o'] = $this->fileName;
			$width = $this->width;
			$height = $this->height;
		}

		$crop = [
			'm' => [0, 0]
		];

		foreach ($this->sizeMap AS $code => $size)
		{
			if (isset($outputFiles[$code]))
			{
				continue;
			}

			$image = $imageManager->imageFromFile($baseFile);
			if (!$image)
			{
				continue;
			}

			$crop[$code] = $this->resizeAvatarImage($image, $size);

			$newTempFile = \XF\Util\File::getTempFile();
			if ($newTempFile && $image->save($newTempFile))
			{
				$outputFiles[$code] = $newTempFile;
			}
			unset($image);
		}

		if (count($outputFiles) != count($this->sizeMap))
		{
			return $this->throwException(new \RuntimeException("Failed to save image to temporary file; image may be corrupt or check internal_data/data permissions"));
		}

		foreach ($outputFiles AS $code => $file)
		{
			$dataFile = $this->user->getAbstractedCustomAvatarPath($code);
			\XF\Util\File::copyFileToAbstractedPath($file, $dataFile);
		}

		$user = $this->user;
		$user->bulkSet([
			'avatar_date' => \XF::$time,
			'avatar_width' => $width,
			'avatar_height' => $height,
			'avatar_highdpi' => ($width >= self::HIGH_DPI_THRESHOLD && $height >= self::HIGH_DPI_THRESHOLD),
			'gravatar' => ''
		]);

		$profile = $user->getRelationOrDefault('Profile');
		$profile->bulkSet([
			'avatar_crop_x' => $crop['m'][0],
			'avatar_crop_y' => $crop['m'][1]
		]);
		$user->addCascadedSave($profile);

		if ($this->logChange == false)
		{
			$user->getBehavior('XF:ChangeLoggable')->setOption('enabled', false);
		}

		$user->save();

		if ($this->logIp)
		{
			$ip = ($this->logIp === true ? $this->app->request()->getIp() : $this->logIp);
			$this->writeIpLog('update', $ip);
		}

		return true;
	}

	protected function resizeAvatarImage(\XF\Image\AbstractDriver $image, $size)
	{
		$sizeMap = $this->sizeMap;

		$cropX = $this->cropX;
		$cropY = $this->cropY;
		$cropScaleRef = $sizeMap['m'];
		if ($cropX === null || $cropY === null)
		{
			$cropScale = $sizeMap['o'] / $cropScaleRef;
			$width = $image->getWidth();
			$height = $image->getHeight();

			$cropX = floor(
				(($width - $sizeMap['o']) / 2) / $cropScale
			);
			$cropY = floor(
				(($height - $sizeMap['o']) / 2) / $cropScale
			);
		}

		$cropX = max($cropX, 0);
		$cropY = max($cropY, 0);

		$image->resizeShortEdge($size, true);

		$cropScale = $size / $cropScaleRef;
		$thisCropX = floor($cropScale * $cropX);
		$thisCropY = floor($cropScale * $cropY);

		$widthOverage = $image->getWidth() - $size;
		if ($widthOverage)
		{
			$thisCropX = min($thisCropX, $widthOverage);
		}

		$heightOverage = $image->getHeight() - $size;
		if ($heightOverage)
		{
			$thisCropY = min($thisCropY, $heightOverage);
		}

		$image->crop($size, $size, $thisCropX, $thisCropY);

		return [$thisCropX, $thisCropY];
	}

	public function createOSizeAvatarFromL()
	{
		$user = $this->user;

		$l = $user->getAbstractedCustomAvatarPath('l');
		$o = $user->getAbstractedCustomAvatarPath('o');
		$fs = $this->app->fs();

		if (!$fs->has($l) || $fs->has($o))
		{
			return true;
		}

		$fs->copy($l, $o);

		$imageManager = $this->app->imageManager();
		$lSize = $this->sizeMap['l'];

		// temp file has original L image content
		$tempFile = \XF\Util\File::copyAbstractedPathToTempFile($l);

		$success = false;

		try
		{
			$image = $imageManager->imageFromFile($tempFile);
			if ($image)
			{
				$this->resizeAvatarImage($image, $lSize);
				$image->save($tempFile);
				// temp file has new L image content
				$success = true;
			}
			else
			{
				// have to remove the avatar
				$success = false;
			}
		}
		catch (\Exception $e)
		{
			\XF::logException($e, false, "Failed to update avatar for user {$user->user_id}: ");
		}

		if ($success)
		{
			\XF\Util\File::copyFileToAbstractedPath($tempFile, $l);
		}

		return true;
	}

	public function setGravatar($gravatar, $verify = true)
	{
		$user = $this->user;

		if ($gravatar !== '' && $verify)
		{
			$validator = $this->app->validator('Gravatar');
			if (!$validator->isValid($gravatar, $errorKey))
			{
				$this->error = $validator->getPrintableErrorValue($errorKey);
				return false;
			}
		}

		$user->bulkSet([
			'gravatar' => $gravatar
		]);

		if (!$user->preSave())
		{
			$errors = $user->getErrors();
			$this->error = reset($errors);
			return false;
		}

		$user->save();

		if ($this->logIp)
		{
			$ip = ($this->logIp === true ? $this->app->request()->getIp() : $this->logIp);
			$this->writeIpLog('set_gravatar', $ip);
		}

		return true;
	}

	public function removeGravatar()
	{
		$this->user->gravatar = '';
		$this->user->saveIfChanged($changed);

		if ($changed && $this->logIp)
		{
			$ip = ($this->logIp === true ? $this->app->request()->getIp() : $this->logIp);
			$this->writeIpLog('remove_gravatar', $ip);
		}

		return true;
	}

	public function deleteAvatar()
	{
		$this->deleteAvatarFiles();

		$user = $this->user;
		$user->bulkSet([
			'avatar_date' => 0,
			'avatar_width' => 0,
			'avatar_height' => 0,
			'avatar_highdpi' => false,
			'gravatar' => ''
		]);

		$profile = $user->getRelationOrDefault('Profile');
		$profile->bulkSet([
			'avatar_crop_x' => 0,
			'avatar_crop_y' => 0
		]);
		$user->addCascadedSave($profile);

		$user->save();

		if ($this->logIp)
		{
			$ip = ($this->logIp === true ? $this->app->request()->getIp() : $this->logIp);
			$this->writeIpLog('delete', $ip);
		}

		return true;
	}

	public function deleteAvatarForUserDelete()
	{
		$this->deleteAvatarFiles();

		return true;
	}

	protected function deleteAvatarFiles()
	{
		if ($this->user->avatar_date)
		{
			foreach ($this->sizeMap AS $code => $size)
			{
				\XF\Util\File::deleteFromAbstractedPath($this->user->getAbstractedCustomAvatarPath($code));
			}
		}
	}

	protected function writeIpLog($action, $ip)
	{
		$user = $this->user;

		/** @var \XF\Repository\Ip $ipRepo */
		$ipRepo = $this->repository('XF:Ip');
		$ipRepo->logIp(\XF::visitor()->user_id, $ip, 'user', $user->user_id, 'avatar_' . $action);
	}

	/**
	 * @param \Exception $error
	 *
	 * @return bool
	 * @throws \Exception
	 */
	protected function throwException(\Exception $error)
	{
		if ($this->throwErrors)
		{
			throw $error;
		}
		else
		{
			return false;
		}
	}
}