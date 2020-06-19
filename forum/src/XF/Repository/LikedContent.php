<?php

namespace XF\Repository;

use XF\Mvc\Entity\Finder;
use XF\Mvc\Entity\Repository;

class LikedContent extends Repository
{
	/**
	 * @param string $contentType
	 * @param int $contentId
	 * @param int $userId
	 *
	 * @return \XF\Entity\LikedContent|null
	 */
	public function getLikeByContentAndLiker($contentType, $contentId, $userId)
	{
		return $this->finder('XF:LikedContent')->where([
			'content_type' => $contentType,
			'content_id' => $contentId,
			'like_user_id' => $userId
		])->fetchOne();
	}

	/**
	 * @param string $contentType
	 * @param int $contentId
	 *
	 * @return Finder
	 */
	public function findContentLikes($contentType, $contentId)
	{
		return $this->finder('XF:LikedContent')
			->where([
				'content_type' => $contentType,
				'content_id' => $contentId
			])->setDefaultOrder('like_date', 'DESC');
	}

	/**
	 * @param $likeUserId
	 *
	 * @return Finder
	 */
	public function findLikesByLikeUserId($likeUserId)
	{
		if ($likeUserId instanceof \XF\Entity\User)
		{
			$likeUserId = $likeUserId->user_id;
		}

		return $this->finder('XF:LikedContent')
			->where('like_user_id', $likeUserId)
			->setDefaultOrder('like_date');
	}

	public function toggleLike($contentType, $contentId, \XF\Entity\User $likeUser, $publish = true)
	{
		$reactionRepo = $this->getReactionRepo();

		return $reactionRepo->reactToContent(1, $contentType, $contentId, $likeUser, $publish, true);
	}

	public function insertLike($contentType, $contentId, \XF\Entity\User $likeUser, $publish = true)
	{
		$reactionRepo = $this->getReactionRepo();

		return $reactionRepo->insertReaction(1, $contentType, $contentId, $likeUser, $publish, true);
	}

	/**
	 * @return \XF\Like\AbstractHandler[]
	 */
	public function getLikeHandlers()
	{
		return $this->getReactionRepo()->getReactionHandlers(true);
	}

	/**
	 * @param string $type
	 * @param bool $throw
	 *
	 * @return \XF\Like\AbstractHandler|null
	 */
	public function getLikeHandler($type, $throw = false)
	{
		return $this->getReactionRepo()->getReactionHandler($type, $throw, true);
	}

	/**
	 * @param \XF\Mvc\Entity\ArrayCollection|\XF\Entity\LikedContent[] $likes
	 */
	public function addContentToLikes($likes)
	{
		$this->getReactionRepo()->addContentToReactions($likes);
	}

	public function rebuildContentLikeCache($contentType, $contentId, $throw = true)
	{
		return $this->getReactionRepo()->rebuildContentReactionCache($contentType, $contentId, true, $throw);
	}

	public function recalculateLikeIsCounted($contentType, $contentIds, $updateLikeCount = true)
	{
		$this->getReactionRepo()->recalculateReactionIsCounted($contentType, $contentIds, $updateLikeCount, true);
	}

	public function fastUpdateLikeIsCounted($contentType, $contentIds, $newValue, $updateLikeCount = true)
	{
		$this->getReactionRepo()->fastUpdateReactionIsCounted($contentType, $contentIds, $newValue, $updateLikeCount);
	}

	public function fastDeleteLikes($contentType, $contentIds, $updateLikeCount = true)
	{
		$this->getReactionRepo()->fastDeleteReactions($contentType, $contentIds, $updateLikeCount);
	}

	public function getUserLikeCount($userId)
	{
		return $this->getReactionRepo()->getUserReactionScore($userId);
	}

	/**
	 * @param $userId
	 *
	 * @return Finder
	 */
	public function findUserLikes($userId)
	{
		$reactionFinder = $this->getReactionRepo()->findUserReactions($userId);

		$reactionFinder->where('reaction_id', 1);

		return $reactionFinder;
	}

	/**
	 * @return Repository|Reaction
	 */
	protected function getReactionRepo()
	{
		return $this->repository('XF:Reaction');
	}
}