<?php

namespace XF\Entity;

use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Structure;

/**
 * COLUMNS
 * @property int|null reaction_content_id
 * @property int reaction_id
 * @property string content_type
 * @property int content_id
 * @property int reaction_user_id
 * @property int reaction_date
 * @property int content_user_id
 * @property bool is_counted
 *
 * GETTERS
 * @property Entity|null Content
 *
 * RELATIONS
 * @property \XF\Entity\Reaction Reaction
 * @property \XF\Entity\User ReactionUser
 * @property \XF\Entity\User Owner
 */
class ReactionContent extends Entity
{
	public function canView(&$error = null)
	{
		$handler = $this->getHandler();
		$content = $this->Content;

		if ($handler && $content)
		{
			return $handler->canViewContent($content, $error);
		}
		else
		{
			return false;
		}
	}

	public function isReactionActive()
	{
		return !empty($this->app()['reactions'][$this->reaction_id]['active']);
	}

	public function getHandler()
	{
		$handler = $this->getReactionRepo()->getReactionHandler(
			$this->content_type, false, $this->getOption('is_like_only')
		);
		if (!$handler)
		{
			$handler = $this->getReactionRepo()->getReactionHandler(
				$this->content_type, false, true
			);
		}
		return $handler;
	}

	/**
	 * @return Entity|null
	 */
	public function getContent()
	{
		$handler = $this->getHandler();
		return $handler ? $handler->getContent($this->content_id) : null;
	}

	public function setContent(Entity $content = null)
	{
		$this->_getterCache['Content'] = $content;
	}

	public function render()
	{
		$handler = $this->getHandler();
		return $handler ? $handler->render($this->getCompatibleEntity()) : '';
	}

	public function isRenderable()
	{
		$handler = $this->getHandler();
		return $handler ? $handler->isRenderable($this->getCompatibleEntity()) : false;
	}

	/**
	 * @return ReactionContent|LikedContent|Entity
	 */
	protected function getCompatibleEntity()
	{
		if ($this->getHandler() instanceof \XF\Like\AbstractHandler)
		{
			$entity = $this->em()->create('XF:LikedContent');
			$entity->bulkSet($this->toArray(), ['forceSet' => true]);
		}
		else
		{
			$entity = $this;
		}

		return $entity;
	}

	protected function _postSave()
	{
		$reactionScore = $this->getApplicableReactionScore();

		if ($this->isInsert())
		{
			if ($this->is_counted)
			{
				$this->adjustUserReactionScore($this->content_user_id, $reactionScore);
			}
		}
		else
		{
			if ($this->isChanged('content_user_id'))
			{
				if ($this->getExistingValue('is_counted'))
				{
					$this->adjustUserReactionScore($this->getExistingValue('content_user_id'), $reactionScore);
				}
				if ($this->is_counted)
				{
					$this->adjustUserReactionScore($this->content_user_id, $reactionScore);
				}
			}
			else if ($this->isChanged('is_counted'))
			{
				// either now counted (increment) or no longer counted (decrement)
				$this->adjustUserReactionScore($this->content_user_id, $this->is_counted ? $reactionScore : -$reactionScore);
			}
		}

		if ($this->isChanged(['content_type', 'content_id', 'content_user_id', 'like_date', 'like_user_id']))
		{
			$this->rebuildContentReactionCache();
		}
	}

	protected function _postDelete()
	{
		if ($this->is_counted)
		{
			$reactionScore = $this->getApplicableReactionScore();
			$this->adjustUserReactionScore($this->content_user_id, -$reactionScore);
		}
		$this->rebuildContentReactionCache();

		$handler = $this->getHandler();
		if ($handler)
		{
			if ($this->getOption('is_like_only'))
			{
				$handler->removeLikeAlert($this->getCompatibleEntity());
				$handler->unpublishLikeNewsFeed($this->getCompatibleEntity());
			}
			else
			{
				$handler->removeReactionAlert($this);
				$handler->unpublishReactionNewsFeed($this);
			}
		}
	}

	protected function getApplicableReactionScore()
	{
		$forceReactionScore = $this->getOption('force_reaction_score');
		if (is_int($forceReactionScore))
		{
			return $forceReactionScore;
		}

		$reaction = $this->Reaction;
		return $reaction ? $reaction->reaction_score : 0;
	}

	protected function adjustUserReactionScore($userId, $score)
	{
		if (!$userId || !$score)
		{
			return;
		}

		$this->db()->query("
			UPDATE xf_user
			SET reaction_score = reaction_score + ?
			WHERE user_id = ?
		", [$score, $userId]);
	}

	protected function rebuildContentReactionCache()
	{
		$repo = $this->getReactionRepo();
		$repo->rebuildContentReactionCache($this->content_type, $this->content_id, $this->getOption('is_like_only'));
	}

	public static function getStructure(Structure $structure)
	{
		$structure->table = 'xf_reaction_content';
		$structure->shortName = 'XF:ReactionContent';
		$structure->primaryKey = 'reaction_content_id';
		$structure->columns = [
			'reaction_content_id' => ['type' => self::UINT, 'autoIncrement' => true, 'nullable' => true],
			'reaction_id' => ['type' => self::UINT, 'required' => true],
			'content_type' => ['type' => self::STR, 'maxLength' => 25, 'required' => true],
			'content_id' => ['type' => self::UINT, 'required' => true],
			'reaction_user_id' => ['type' => self::UINT, 'required' => true],
			'reaction_date' => ['type' => self::UINT, 'default' => \XF::$time],
			'content_user_id' => ['type' => self::UINT, 'required' => true],
			'is_counted' => ['type' => self::BOOL, 'default' => true],
		];
		$structure->getters = [
			'Content' => true
		];
		$structure->relations = [
			'Reaction' => [
				'entity' => 'XF:Reaction',
				'type' => self::TO_ONE,
				'conditions' => 'reaction_id',
				'primary' => true
			],
			'ReactionUser' => [
				'entity' => 'XF:User',
				'type' => self::TO_ONE,
				'conditions' => [['user_id', '=', '$reaction_user_id']],
				'primary' => true
			],
			'Owner' => [
				'entity' => 'XF:User',
				'type' => self::TO_ONE,
				'conditions' => [['user_id', '=', '$content_user_id']],
				'primary' => true
			]
		];
		$structure->options = [
			'is_like_only' => false,
			'force_reaction_score' => null
		];
		$structure->defaultWith[] = 'Reaction';

		return $structure;
	}

	/**
	 * @return \XF\Repository\Reaction
	 */
	protected function getReactionRepo()
	{
		return $this->repository('XF:Reaction');
	}
}