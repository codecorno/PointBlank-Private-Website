<?php

namespace XF\Stats;

class Post extends AbstractHandler
{
	public function getStatsTypes()
	{
		return [
			'post' => \XF::phrase('posts'),
			'post_reaction' => \XF::phrase('post_reactions')
		];
	}

	public function getData($start, $end)
	{
		$db = $this->db();

		$posts = $db->fetchPairs(
			$this->getBasicDataQuery('xf_post', 'post_date', 'message_state = ?'),
			[$start, $end, 'visible']
		);

		$postReactions = $db->fetchPairs(
			$this->getBasicDataQuery('xf_reaction_content', 'reaction_date', 'content_type = ? AND is_counted = ?'),
			[$start, $end, 'post', 1]
		);

		return [
			'post' => $posts,
			'post_reaction' => $postReactions
		];
	}
}