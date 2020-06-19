<?php

namespace XF\Stats;

class ProfilePost extends AbstractHandler
{
	public function getStatsTypes()
	{
		return [
			'profile_post' => \XF::phrase('profile_posts'),
			'profile_post_reaction' => \XF::phrase('profile_post_reactions')
		];
	}

	public function getData($start, $end)
	{
		$db = $this->db();

		$profilePosts = $db->fetchPairs(
			$this->getBasicDataQuery('xf_profile_post', 'post_date', 'message_state = ?'),
			[$start, $end, 'visible']
		);

		$profilePostReactions = $db->fetchPairs(
			$this->getBasicDataQuery('xf_reaction_content', 'reaction_date', 'content_type = ? AND is_counted = ?'),
			[$start, $end, 'profile_post', 1]
		);

		return [
			'profile_post' => $profilePosts,
			'profile_post_reaction' => $profilePostReactions
		];
	}
}