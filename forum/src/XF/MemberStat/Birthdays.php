<?php

namespace XF\MemberStat;

class Birthdays
{
	public static function getBirthdayUsers(\XF\Entity\MemberStat $memberStat, \XF\Finder\User $finder)
	{
		$finder
			->isBirthday()
			->isRecentlyActive(365)
			->isValidUser();

		$users = $finder->fetch($memberStat->user_limit * 3);

		$results = $users->pluck(function(\XF\Entity\User $user)
		{
			return [$user->user_id, \XF::language()->numberFormat($user->Profile->getAge())];
		});

		return $results;
	}
}