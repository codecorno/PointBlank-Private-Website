<?php

namespace XF\Import\Data;

class UserBan extends AbstractEmulatedData
{
	public function getImportType()
	{
		return 'user_ban';
	}

	public function getEntityShortName()
	{
		return 'XF:UserBan';
	}

	protected function postSave($oldId, $newId)
	{
		/** @var \XF\Entity\User $user */
		$user = $this->em()->find('XF:User', $this->user_id);
		if ($user)
		{
			$user->is_banned = true;
			$user->getBehavior('XF:ChangeLoggable')->setOption('enabled', false);
			$user->saveIfChanged($saved, false, false);
		}
	}
}