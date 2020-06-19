<?php

namespace XF\Repository;

use XF\Mvc\Entity\Finder;
use XF\Mvc\Entity\Repository;

class NewsFeed extends Repository
{
	/**
	 * @param bool $applyPrivacyChecks
	 *
	 * @return \XF\Finder\NewsFeed
	 */
	public function findNewsFeed($applyPrivacyChecks = true)
	{
		/** @var \XF\Finder\NewsFeed $finder */
		$finder = $this->finder('XF:NewsFeed');

		if ($applyPrivacyChecks)
		{
			$finder->applyPrivacyChecks();
		}

		$finder->order('event_date', 'DESC')
			->indexHint('FORCE', 'event_date');

		return $finder;
	}

	/**
	 * @param \XF\Entity\User $user
	 *
	 * @return \XF\Finder\NewsFeed
	 */
	public function findMembersActivity(\XF\Entity\User $user)
	{
		return $this->finder('XF:NewsFeed')
			->byUser($user)
			->order('event_date', 'DESC');
	}

	public function publish($contentType, $contentId, $action, $userId, $username, array $extraData = [], $eventDate = null)
	{
		$newsFeed = $this->em->create('XF:NewsFeed');
		$newsFeed->content_type = $contentType;
		$newsFeed->content_id = $contentId;
		$newsFeed->action = $action;
		$newsFeed->user_id = $userId;
		$newsFeed->username = $username;
		$newsFeed->extra_data = $extraData;
		if ($eventDate)
		{
			$newsFeed->event_date = $eventDate;
		}
		$newsFeed->save();

		return $newsFeed;
	}

	public function unpublish($contentType, $contentId, $userId = null, $action = null)
	{
		$finder = $this->finder('XF:NewsFeed')
			->where('content_type', $contentType)
			->where('content_id', $contentId);

		if ($userId !== null)
		{
			$finder->where('user_id', $userId);
		}
		if ($action !== null)
		{
			$finder->where('action', $action);
		}

		$entries = $finder->fetch();

		$this->db()->beginTransaction();

		/** @var \XF\Entity\NewsFeed $entry */
		foreach ($entries AS $entry)
		{
			$entry->delete(false, false);
		}

		$this->db()->commit();

		return $entries;
	}

	/**
	 * @param string $type
	 * @param bool $throw
	 *
	 * @return \XF\NewsFeed\AbstractHandler|null
	 */
	public function getNewsFeedHandler($type, $throw = false)
	{
		$handlerClass = \XF::app()->getContentTypeFieldValue($type, 'news_feed_handler_class');
		if (!$handlerClass)
		{
			if ($throw)
			{
				throw new \InvalidArgumentException("No news feed handler for '$type'");
			}
			return null;
		}

		if (!class_exists($handlerClass))
		{
			if ($throw)
			{
				throw new \InvalidArgumentException("News feed handler for '$type' does not exist: $handlerClass");
			}
			return null;
		}

		$handlerClass = \XF::extendClass($handlerClass);
		return new $handlerClass($type);
	}

	/**
	 * @param \XF\Entity\NewsFeed[] $newsFeedItems
	 */
	public function addContentToNewsFeedItems($newsFeedItems)
	{
		$contentMap = [];
		foreach ($newsFeedItems AS $key => $newsFeed)
		{
			$contentType = $newsFeed->content_type;
			if (!isset($contentMap[$contentType]))
			{
				$contentMap[$contentType] = [];
			}
			$contentMap[$contentType][$key] = $newsFeed->content_id;
		}

		foreach ($contentMap AS $contentType => $contentIds)
		{
			$handler = $this->getNewsFeedHandler($contentType);
			if (!$handler)
			{
				continue;
			}

			$data = $handler->getContent($contentIds);

			foreach ($contentIds AS $newsFeedId => $contentId)
			{
				$content = isset($data[$contentId]) ? $data[$contentId] : null;
				$newsFeedItems[$newsFeedId]->setContent($content);
			}
		}
	}

	public function cleanUpNewsFeedItems($dateCut = null)
	{
		if ($dateCut === null)
		{
			// TODO: make this an option
			$dateCut = time() - 90 * 86400;
		}
		$this->db()->delete('xf_news_feed', 'event_date < ?', $dateCut);
	}
}