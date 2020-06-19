<?php

namespace XF\Repository;

use XF\Mvc\Entity\Repository;

class UserChangeTemp extends Repository
{
	protected $validChangeRelations = ['Auth', 'Option', 'Profile', 'Privacy'];

	public function expireUserChangeByKey(\XF\Entity\User $user, $changeKey)
	{
		/** @var \XF\Entity\UserChangeTemp|null $change */
		$change = $this->em->findOne('XF:UserChangeTemp', ['user_id' => $user->user_id, 'change_key' => $changeKey]);
		if ($change)
		{
			/** @var \XF\Service\User\TempChange $changeService */
			$changeService = $this->app()->service('XF:User\TempChange');
			return $changeService->expireChange($change);
		}

		return false;
	}

	public function removeExpiredChanges()
	{
		$expired = $this->finder('XF:UserChangeTemp')
			->where('expiry_date', '<=', \XF::$time)
			->where('expiry_date', '!=', null)
			->order('expiry_date')
			->fetch(1000);

		/** @var \XF\Service\User\TempChange $changeService */
		$changeService = $this->app()->service('XF:User\TempChange');

		/** @var \XF\Entity\UserChangeTemp $change */
		foreach ($expired AS $change)
		{
			$changeService->expireChange($change);
		}
	}
}