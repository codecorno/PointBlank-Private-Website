<?php

namespace XF\Job;

class ProfilePost extends AbstractRebuildJob
{
	protected function getNextIds($start, $batch)
	{
		$db = $this->app->db();

		return $db->fetchAllColumn($db->limit(
			"
				SELECT profile_post_id
				FROM xf_profile_post
				WHERE profile_post_id > ?
				ORDER BY profile_post_id
			", $batch
		), $start);
	}

	protected function rebuildById($id)
	{
		/** @var \XF\Entity\ProfilePost $profilePost */
		$profilePost = $this->app->em()->find('XF:ProfilePost', $id);
		if (!$profilePost)
		{
			return;
		}

		$profilePost->rebuildCounters();
		$profilePost->saveIfChanged();
	}

	protected function getStatusType()
	{
		return \XF::phrase('profile_posts');
	}
}