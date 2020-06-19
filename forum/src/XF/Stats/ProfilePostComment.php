<?php

namespace XF\Stats;

class ProfilePostComment extends AbstractHandler
{
	public function getStatsTypes()
	{
		return [
			'profile_post_comment' => \XF::phrase('profile_post_comments'),
			'profile_post_comment_reaction' => \XF::phrase('profile_post_comment_reactions')
		];
	}

	public function getData($start, $end)
	{
		$db = $this->db();

		$profilePostComments = $db->fetchPairs(
			$this->getBasicDataQuery('xf_profile_post_comment', 'comment_date', 'message_state = ?'),
			[$start, $end, 'visible']
		);

		$profilePostCommentReactions = $db->fetchPairs(
			$this->getBasicDataQuery('xf_reaction_content', 'reaction_date', 'content_type = ? AND is_counted = ?'),
			[$start, $end, 'profile_post_comment', 1]
		);

		return [
			'profile_post_comment' => $profilePostComments,
			'profile_post_comment_reaction' => $profilePostCommentReactions
		];
	}
}