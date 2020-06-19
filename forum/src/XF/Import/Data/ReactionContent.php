<?php

namespace XF\Import\Data;

class ReactionContent extends AbstractEmulatedData
{
	public function getImportType()
	{
		return 'reaction_content';
	}

	public function getEntityShortName()
	{
		return 'XF:ReactionContent';
	}

	public function setReactionId($reactionId)
	{
		$this->set('reaction_id', $reactionId);
	}

	protected function preSave($oldId)
	{
		if (!$this->get('reaction_id'))
		{
			throw new \LogicException("Must set a reaction ID");
		}
	}

	protected function postSave($oldId, $newId)
	{
		/** @var \XF\Entity\Reaction $reaction */
		$reaction = $this->em()->find('XF:Reaction', $this->get('reaction_id'));
		if ($reaction)
		{
			$reactionScore = $reaction->reaction_score;

			if ($this->is_counted && $this->content_user_id)
			{
				$this->db()->query("
					UPDATE xf_user
					SET reaction_score = reaction_score + ?
					WHERE user_id = ?
				", [$reactionScore, $this->content_user_id]);
			}

			$this->app()->repository('XF:Reaction')->rebuildContentReactionCache(
				$this->content_type, $this->content_id, false
			);
		}
	}
}