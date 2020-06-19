<?php

namespace XF\Sitemap;

class User extends AbstractHandler
{
	public function getRecords($start)
	{
		$app = $this->app;

		$ids = $this->getIds('xf_user', 'user_id', $start);

		$userFinder = $app->finder('XF:User');
		$users = $userFinder
			->where('user_id', $ids)
			->with(['Profile', 'Privacy'])
			->order('user_id')
			->fetch();

		return $users;
	}

	public function getEntry($record)
	{
		/** @var \XF\Entity\User $record */
		$url = $this->app->router('public')->buildLink('canonical:members', $record);
		$entry = Entry::create($url, [
			'priority' => 0.3
		]);
		if ($record->avatar_date || $record->gravatar)
		{
			$avatar = \XF::canonicalizeUrl($record->getAvatarUrl('o', null, true));
			$entry->set('image', $avatar);
		}
		return $entry;
	}

	public function isIncluded($record)
	{
		/** @var $record \XF\Entity\User */
		return $record->canViewFullProfile();
	}
}