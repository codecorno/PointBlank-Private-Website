<?php

namespace XF\Import\DataHelper;

class Avatar extends AbstractHelper
{
	public function copyFinalAvatarFile($sourceFile, $size, \XF\Entity\User $user)
	{
		$targetPath = $user->getAbstractedCustomAvatarPath($size);
		return \XF\Util\File::copyFileToAbstractedPath($sourceFile, $targetPath);
	}

	public function copyFinalAvatarFiles(array $sourceFileMap, \XF\Entity\User $user)
	{
		$success = true;
		foreach ($sourceFileMap AS $size => $sourceFile)
		{
			if (!$this->copyFinalAvatarFile($sourceFile, $size, $user))
			{
				$success = false;
				break;
			}
		}

		return $success;
	}

	public function setAvatarFromFile($sourceFile, \XF\Entity\User $user)
	{
		/** @var \XF\Service\User\Avatar $avatarService */
		$avatarService = $this->dataManager->app()->service('XF:User\Avatar', $user);
		$avatarService->logIp(false);
		$avatarService->logChange(false);
		$avatarService->silentRunning(true);

		if ($avatarService->setImage($sourceFile))
		{
			$avatarService->updateAvatar();
			return true;
		}

		return false;
	}
}