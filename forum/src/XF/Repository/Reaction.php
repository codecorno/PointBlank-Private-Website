<?php

namespace XF\Repository;

use XF\Mvc\Entity\Finder;
use XF\Mvc\Entity\Repository;

class Reaction extends Repository
{
	/**
	 * @return Finder
	 */
	public function findReactionsForList($activeOnly = false)
	{
		$finder = $this->finder('XF:Reaction')
			->order('display_order');

		if ($activeOnly)
		{
			$finder->where('active', 1);
		}

		return $finder;
	}

	public function getReactionScores()
	{
		return [
			1 => \XF::phrase('reaction_score.positive'),
			0 => \XF::phrase('reaction_score.neutral'),
			-1 => \XF::phrase('reaction_score.negative')
		];
	}

	/**
	 * @param string $contentType
	 * @param int $contentId
	 * @param int $userId
	 *
	 * @return \XF\Entity\ReactionContent|null
	 */
	public function getReactionByContentAndReactionUser($contentType, $contentId, $userId)
	{
		return $this->finder('XF:ReactionContent')->where([
			'content_type' => $contentType,
			'content_id' => $contentId,
			'reaction_user_id' => $userId
		])->fetchOne();
	}

	/**
	 * @param string $contentType
	 * @param int $contentId
	 *
	 * @return Finder
	 */
	public function findContentReactions($contentType, $contentId, $reactionId = null)
	{
		$finder = $this->finder('XF:ReactionContent')
			->with([
				'Reaction',
				'ReactionUser',
				'ReactionUser.Profile',
				'ReactionUser.Option'
			], true)
			->where([
				'content_type' => $contentType,
				'content_id' => $contentId,
				'Reaction.active' => true
			])
			->setDefaultOrder('reaction_date', 'DESC');

		if ($reactionId)
		{
			$finder->where('reaction_id', $reactionId);
		}

		return $finder;
	}

	/**
	 * @param $reactionUserId
	 *
	 * @return Finder
	 */
	public function findReactionsByReactionUserId($reactionUserId)
	{
		if ($reactionUserId instanceof \XF\Entity\User)
		{
			$reactionUserId = $reactionUserId->user_id;
		}

		return $this->finder('XF:ReactionContent')
			->where('reaction_user_id', $reactionUserId)
			->setDefaultOrder('reaction_date');
	}

	public function getContentTabSummary($contentType, $contentId)
	{
		$reactionHandler = $this->getReactionHandler($contentType, true);

		$entity = $reactionHandler->getContent($contentId);
		if (!$entity)
		{
			throw new \InvalidArgumentException("No entity found for '$contentType' with ID $contentId");
		}

		$countsField = $reactionHandler->getCountsFieldName();

		$counts = $entity->$countsField;
		$counts = [0 => array_sum($counts)] + $counts;
		return $counts;
	}

	public function reactToContent($reactionId, $contentType, $contentId, \XF\Entity\User $reactUser, $publish = true, $isLike = false)
	{
		$insert = false;

		$existingReaction = $this->getReactionByContentAndReactionUser($contentType, $contentId, $reactUser->user_id);
		if ($existingReaction && $existingReaction->reaction_id == $reactionId)
		{
			$existingReaction->setOption('is_like_only', $isLike);
			$existingReaction->delete();
			return null;
		}
		else if ($existingReaction && $existingReaction->reaction_id != $reactionId)
		{
			$existingReaction->setOption('is_like_only', $isLike);
			$existingReaction->delete();
			$insert = true;
		}
		else if (!$existingReaction)
		{
			$insert = true;
		}

		if ($insert)
		{
			try
			{
				$reaction = $this->insertReaction(
					$reactionId, $contentType, $contentId, $reactUser, $publish, $isLike
				);
				return $reaction;
			}
			catch (\XF\Db\DuplicateKeyException $e)
			{
				// race condition so we should just re-look up the reaction and return that
				return $this->getReactionByContentAndReactionUser($contentType, $contentId, $reactUser->user_id);
			}
		}
		else
		{
			return null;
		}
	}

	public function insertReaction($reactionId, $contentType, $contentId, \XF\Entity\User $reactUser, $publish = true, $isLike = false)
	{
		if (!$reactUser->user_id)
		{
			throw new \InvalidArgumentException("Guests cannot react to content");
		}

		$reactionHandler = $this->getReactionHandler($contentType, true, $isLike);

		$entity = $reactionHandler->getContent($contentId);
		if (!$entity)
		{
			throw new \InvalidArgumentException("No entity found for '$contentType' with ID $contentId");
		}

		/** @var \XF\Entity\ReactionContent $reaction */
		$reaction = $this->em->create('XF:ReactionContent');
		$reaction->setOption('is_like_only', $isLike);
		$reaction->reaction_id = $reactionId;
		$reaction->content_type = $contentType;
		$reaction->content_id = $contentId;
		$reaction->reaction_user_id = $reactUser->user_id;
		$reaction->content_user_id = $reactionHandler->getContentUserId($entity);
		if ($isLike)
		{
			$reaction->is_counted = $reactionHandler->likesCounted($entity);
		}
		else
		{
			$reaction->is_counted = $reactionHandler->reactionsCounted($entity);
		}
		$reaction->save();

		if ($publish)
		{
			if ($reaction->Owner && $reaction->ReactionUser)
			{
				if ($isLike)
				{
					$reactionHandler->sendLikeAlert($reaction->Owner, $reaction->ReactionUser, $contentId, $entity);
				}
				else
				{
					$reactionHandler->sendReactionAlert($reaction->Owner, $reaction->ReactionUser, $contentId, $entity, $reactionId);
				}
			}
			if ($reaction->ReactionUser)
			{
				if ($isLike)
				{
					$reactionHandler->publishLikeNewsFeed($reaction->ReactionUser, $contentId, $entity);
				}
				else
				{
					$reactionHandler->publishReactionNewsFeed($reaction->ReactionUser, $contentId, $entity, $reactionId);
				}
			}
		}

		return $reaction;
	}

	public function rebuildContentReactionCache($contentType, $contentId, $isLike = false, $throw = true)
	{
		$reactionHandler = $this->getReactionHandler($contentType, $throw, $isLike);
		if (!$reactionHandler)
		{
			if ($throw)
			{
				throw new \InvalidArgumentException("No reaction handler found for '$contentType'");
			}
			return false;
		}

		$entity = $reactionHandler->getContent($contentId);
		if (!$entity)
		{
			if ($throw)
			{
				throw new \InvalidArgumentException("No entity found for '$contentType' with ID $contentId");
			}
			return false;
		}

		$counts = $this->db()->fetchPairs("
			SELECT reacted.reaction_id, COUNT(*) AS counts
			FROM xf_reaction_content AS reacted
			INNER JOIN xf_reaction AS reaction ON (reacted.reaction_id = reaction.reaction_id)
			WHERE content_type = ? AND content_id = ? AND reaction.active = 1
			GROUP BY reaction_id
			ORDER BY counts DESC
		", [$contentType, $contentId]);

		if ($counts)
		{
			$latest = $this->db()->fetchAll("
				SELECT user.user_id, user.username, reacted.reaction_id
				FROM xf_reaction_content AS reacted
				INNER JOIN xf_user AS user ON (reacted.reaction_user_id = user.user_id)
				INNER JOIN xf_reaction AS reaction ON (reacted.reaction_id = reaction.reaction_id)
				WHERE reacted.content_type = ? AND reacted.content_id = ? AND reaction.active = 1
				ORDER BY reacted.reaction_date DESC
				LIMIT 5
			", [$contentType, $contentId]);
		}
		else
		{
			$latest = [];
		}

		if ($isLike)
		{
			$reactionHandler->updateContentLikes($entity, $counts, $latest);
		}
		else
		{
			$reactionHandler->updateContentReactions($entity, $counts, $latest);
		}

		return true;
	}

	/**
	 * @return \XF\Reaction\AbstractHandler[]
	 */
	public function getReactionHandlers($isLike = false)
	{
		$handlers = [];

		$field = $isLike ? 'like_handler_class' : 'reaction_handler_class';
		foreach (\XF::app()->getContentTypeField($field) AS $contentType => $handlerClass)
		{
			if (class_exists($handlerClass))
			{
				$handlerClass = \XF::extendClass($handlerClass);
				$handlers[$contentType] = new $handlerClass($contentType);
			}
		}

		return $handlers;
	}

	/**
	 * @param string $type
	 * @param bool $throw
	 *
	 * @return \XF\Reaction\AbstractHandler|\XF\Like\AbstractHandler|null
	 */
	public function getReactionHandler($type, $throw = false, $isLike = false)
	{
		$field = $isLike ? 'like_handler_class' : 'reaction_handler_class';
		$handlerClass = \XF::app()->getContentTypeFieldValue($type, $field);
		if (!$handlerClass)
		{
			if ($throw)
			{
				throw new \InvalidArgumentException("No reaction handler for '$type'");
			}
			return null;
		}

		if (!class_exists($handlerClass))
		{
			if ($throw)
			{
				throw new \InvalidArgumentException("Reaction handler for '$type' does not exist: $handlerClass");
			}
			return null;
		}

		$handlerClass = \XF::extendClass($handlerClass);
		return new $handlerClass($type);
	}

	/**
	 * @param \XF\Entity\ReactionContent[] $reactions
	 */
	public function addContentToReactions($reactions, $isLike = false)
	{
		$contentMap = [];
		foreach ($reactions AS $key => $reaction)
		{
			$contentType = $reaction->content_type;
			if (!isset($contentMap[$contentType]))
			{
				$contentMap[$contentType] = [];
			}
			$contentMap[$contentType][$key] = $reaction->content_id;
		}

		foreach ($contentMap AS $contentType => $contentIds)
		{
			$handler = $this->getReactionHandler($contentType, false, $isLike);
			if (!$handler)
			{
				continue;
			}

			$data = $handler->getContent($contentIds);

			foreach ($contentIds AS $reactionContentId => $contentId)
			{
				$content = isset($data[$contentId]) ? $data[$contentId] : null;
				$reactions[$reactionContentId]->setContent($content);
			}
		}
	}

	public function recalculateReactionIsCounted($contentType, $contentIds, $updateReactionScore = true, $isLike = false)
	{
		$reactionHandler = $this->getReactionHandler($contentType, true, $isLike);

		if (!is_array($contentIds))
		{
			$contentIds = [$contentIds];
		}
		if (!$contentIds)
		{
			return;
		}

		$entities = $reactionHandler->getContent($contentIds);
		$enableIds = [];
		$disableIds = [];

		foreach ($entities AS $id => $entity)
		{
			if ($isLike)
			{
				$isCounted = $reactionHandler->likesCounted($entity);
			}
			else
			{
				$isCounted = $reactionHandler->reactionsCounted($entity);
			}

			if ($isCounted)
			{
				$enableIds[] = $id;
			}
			else
			{
				$disableIds[] = $id;
			}
		}

		if ($enableIds)
		{
			$this->fastUpdateReactionIsCounted($contentType, $enableIds, true, $updateReactionScore);
		}
		if ($disableIds)
		{
			$this->fastUpdateReactionIsCounted($contentType, $disableIds, false, $updateReactionScore);
		}
	}

	public function fastUpdateReactionIsCounted($contentType, $contentIds, $newValue, $updateReactionScore = true)
	{
		if (!is_array($contentIds))
		{
			$contentIds = [$contentIds];
		}
		if (!$contentIds)
		{
			return;
		}

		$newDbValue = $newValue ? 1 : 0;
		$oldDbValue = $newValue ? 0 : 1;

		$db = $this->db();
		if ($updateReactionScore)
		{
			$updates = $db->fetchPairs("
				SELECT content_user_id, content_id
				FROM xf_reaction_content
				WHERE content_type = ?
					AND content_id IN (" . $db->quote($contentIds) . ")
					AND is_counted = ?
			", [$contentType, $oldDbValue]);
			if ($updates)
			{
				$db->beginTransaction();

				$db->update('xf_reaction_content',
					['is_counted' => $newDbValue],
					'content_type = ?
						AND content_id IN (' . $db->quote($contentIds) . ')
						AND is_counted = ?',
					[$contentType, $oldDbValue]
				);

				unset($updates[0]);

				$tally = [];
				foreach ($updates AS $userId => $contentId)
				{
					$existingReaction = $this->getReactionByContentAndReactionUser($contentType, $contentId, $userId);
					$tally[$userId] = isset($tally[$userId]) ? $tally[$userId] + $existingReaction->reaction_score : 1;
				}

				$operator = $newDbValue ? '+' : '-';
				foreach ($tally AS $userId => $totalChange)
				{
					$db->query("
						UPDATE xf_user
						SET reaction_score = reaction_score {$operator} ?
						WHERE user_id = ?
					", [$totalChange, $userId]);
				}

				$db->commit();
			}
		}
		else
		{
			$db->update('xf_reaction_content',
				['is_counted' => $newDbValue],
				'content_type = ?
					AND content_id IN (' . $db->quote($contentIds) . ')
					AND is_counted = ?',
				[$contentType, $oldDbValue]
			);
		}
	}

	public function fastDeleteReactions($contentType, $contentIds, $updateReactionCount = true)
	{
		if (!is_array($contentIds))
		{
			$contentIds = [$contentIds];
		}
		if (!$contentIds)
		{
			return;
		}

		$db = $this->db();

		if ($updateReactionCount)
		{
			$updates = $db->fetchPairs("
				SELECT content_user_id, content_id
				FROM xf_reaction_content
				WHERE content_type = ?
					AND content_id IN (" . $db->quote($contentIds) . ")
					AND is_counted = 1
			", $contentType);
		}
		else
		{
			$updates = [];
		}

		$db->beginTransaction();
		if ($updates)
		{
			unset($updates[0]);

			$tally = [];
			foreach ($updates AS $userId => $contentId)
			{
				$existingReaction = $this->getReactionByContentAndReactionUser($contentType, $contentId, $userId);
				$tally[$userId] = isset($tally[$userId]) ? $tally[$userId] + $existingReaction->reaction_score : 1;
			}

			foreach ($tally AS $userId => $totalChange)
			{
				$db->query("
					UPDATE xf_user
					SET reaction_score = reaction_score - ?
					WHERE user_id = ?
				", [$totalChange, $userId]);
			}
		}

		$db->delete('xf_reaction_content',
			'content_type = ? AND content_id IN (' . $db->quote($contentIds) . ')',
			$contentType
		);

		$db->commit();
	}

	public function getUserReactionScore($userId)
	{
		if ($userId instanceof \XF\Entity\User)
		{
			$userId = $userId->user_id;
		}

		return intval($this->db()->fetchOne("
			SELECT SUM(reaction.reaction_score)
			FROM xf_reaction_content AS content
			INNER JOIN xf_reaction AS reaction ON
				(content.reaction_id = reaction.reaction_id)
			WHERE content.content_user_id = ?
				AND content.is_counted = 1
		", $userId));
	}

	/**
	 * @param $userId
	 *
	 * @return Finder
	 */
	public function findUserReactions($userId)
	{
		if ($userId instanceof \XF\Entity\User)
		{
			$userId = $userId->user_id;
		}

		$finder = $this->finder('XF:ReactionContent')
			->with('ReactionUser')
			->where('content_user_id', $userId)
			->where('is_counted', 1)
			->setDefaultOrder('reaction_date', 'DESC');

		return $finder;
	}

	public function getUserReactionsTabSummary($userId)
	{
		if ($userId instanceof \XF\Entity\User)
		{
			$userId = $userId->user_id;
		}

		return $this->db()->fetchPairs('
			SELECT content.reaction_id, COUNT(*)
			FROM xf_reaction_content AS content
			FORCE INDEX (content_user_id_reaction_date)
			INNER JOIN xf_reaction AS reaction ON
				(content.reaction_id = reaction.reaction_id)
			WHERE content.content_user_id = ?
				AND reaction.active = 1
				AND content.is_counted = 1
			GROUP BY content.reaction_id
			ORDER BY reaction.display_order 
		', $userId);
	}

	public function getReactionCacheData()
	{
		$reactions = $this->finder('XF:Reaction')
			->order(['display_order', 'reaction_id'])
			->fetch();

		$cache = [];

		foreach ($reactions AS $reactionId => $reaction)
		{
			$reaction = $reaction->toArray();

			$cache[$reactionId] = $reaction;

			if (!$reaction['sprite_mode'] || !$reaction['sprite_params'])
			{
				unset($cache[$reactionId]['sprite_params']);
			}

			unset($cache[$reactionId]['sprite_mode'], $cache[$reactionId]['reaction_text']);
		}

		return $cache;
	}

	public function rebuildReactionCache()
	{
		$cache = $this->getReactionCacheData();
		\XF::registry()->set('reactions', $cache);
		return $cache;
	}

	public function getReactionSpriteCacheData()
	{
		$reactions = $this->finder('XF:Reaction')
			->order(['display_order', 'reaction_id'])
			->fetch();

		$cache = [];
		$defaultReactionHeight = 32;

		foreach ($reactions AS $reactionId => $reaction)
		{
			if ($reaction->sprite_mode && !empty($reaction->sprite_params))
			{
				$w = (int)$reaction->sprite_params['w'];
				$h = (int)$reaction->sprite_params['h'];
				$x = (int)$reaction->sprite_params['x'];
				$y = (int)$reaction->sprite_params['y'];
				$imageUrlHtml = htmlspecialchars($reaction->image_url);

				// Out of the box reactions display at 32x32 for the max size. We then generally assume
				// small and medium are 16x16 and 21x21, so we need to ensure that we always scale other
				// reaction sizes to that.
				$adjustScalingFactor = $defaultReactionHeight / $h;

				$cache[$reactionId]['sprite_css'] = sprintf(
					'width: %1$dpx; height: %2$dpx; background: url(\'%3$s\') no-repeat %4$dpx %5$dpx;',
					$w,
					$h,
					$imageUrlHtml,
					$x,
					$y
				);

				$cache[$reactionId]['small_sprite_css'] = sprintf(
					'width: %1$dpx; height: %2$dpx; background: url(\'%3$s\') no-repeat %4$dpx %5$dpx;',
					($w / 2) * $adjustScalingFactor,
					($h / 2) * $adjustScalingFactor,
					$imageUrlHtml,
					($x / 2) * $adjustScalingFactor,
					($y / 2) * $adjustScalingFactor
				);

				$cache[$reactionId]['medium_sprite_css'] = sprintf(
					'width: %1$dpx; height: %2$dpx; background: url(\'%3$s\') no-repeat %4$dpx %5$dpx;',
					($w * .65625) * $adjustScalingFactor,
					($h * .65625) * $adjustScalingFactor,
					$imageUrlHtml,
					($x * .65625) * $adjustScalingFactor,
					($y * .65625) * $adjustScalingFactor
				);

				if (!empty($reaction->sprite_params['bs']))
				{
					$bs = ' background-size: ' . htmlspecialchars($reaction->sprite_params['bs']);;

					$cache[$reactionId]['sprite_css'] .= $bs;
					$cache[$reactionId]['small_sprite_css'] .= $bs;
					$cache[$reactionId]['medium_sprite_css'] .= $bs;
				}
			}
		}

		return $cache;
	}

	public function rebuildReactionSpriteCache()
	{
		$cache = $this->getReactionSpriteCacheData();
		\XF::registry()->set('reactionSprites', $cache);
		$this->repository('XF:Style')->updateAllStylesLastModifiedDateLater();
		return $cache;
	}
}