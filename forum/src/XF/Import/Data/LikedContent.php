<?php

namespace XF\Import\Data;

class LikedContent extends AbstractEmulatedData
{
	protected $columnMap = [
		'like_id' => 'reaction_content_id',
		'like_user_id' => 'reaction_user_id',
		'like_date' => 'reaction_date'
	];

	public function getImportType()
	{
		return 'liked_content';
	}

	public function getEntityShortName()
	{
		return 'XF:LikedContent';
	}

	protected function write($oldId)
	{
		// traditional "likes" are always reaction ID 1
		$this->ee->set('reaction_id', 1);

		return parent::write($oldId);
	}

	protected function postSave($oldId, $newId)
	{
		/** @var \XF\Entity\Reaction $reaction */
		$reaction = $this->em()->find('XF:Reaction', 1); // like
		$reactionScore = $reaction->reaction_score;

		if ($this->is_counted && $this->content_user_id)
		{
			$this->db()->query("
				UPDATE xf_user
				SET reaction_score = reaction_score + ?
				WHERE user_id = ?
			", [$reactionScore, $this->content_user_id]);
		}

		$this->app()->repository('XF:LikedContent')->rebuildContentLikeCache(
			$this->content_type, $this->content_id, false
		);
	}
}