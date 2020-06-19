<?php

namespace XF\Job;

class User extends AbstractRebuildJob
{
	protected function getNextIds($start, $batch)
	{
		$db = $this->app->db();

		return $db->fetchAllColumn($db->limit(
			"
				SELECT user_id
				FROM xf_user
				WHERE user_id > ?
				ORDER BY user_id
			", $batch
		), $start);
	}

	protected function rebuildById($id)
	{
		/** @var \XF\Entity\User $user */
		$user = $this->app->em()->find('XF:User', $id, ['Profile']);
		if (!$user)
		{
			return;
		}

		$db = $this->app->db();

		$db->beginTransaction();

		$user->alerts_unread = $this->app->finder('XF:UserAlert')
			->where('alerted_user_id', $user->user_id)
			->where('view_date', 0)
			->total();

		$user->conversations_unread = $this->app->finder('XF:ConversationUser')
			->with('Master', true)
			->with('Recipient', true)
			->where('owner_user_id', $user->user_id)
			->where('is_unread', 1)
			->total();

		$user->save(true, false);

		$user->rebuildUserGroupRelations(false);
		$user->rebuildPermissionCombination();
		$user->rebuildDisplayStyleGroup();

		$user->Profile->rebuildUserFieldValuesCache();

		$db->commit();
	}

	protected function getStatusType()
	{
		return \XF::phrase('users');
	}
}