<?php

namespace XF\Pub\Controller;

use XF\Mvc\Entity\ArrayCollection;
use XF\Mvc\ParameterBag;

class WhatsNew extends AbstractController
{
	public function actionIndex()
	{
		$this->assertCanonicalUrl($this->buildLink('whats-new'));

		$viewParams = [];
		return $this->view('XF:WhatsNew\Overview', 'whats_new', $viewParams);
	}

	public function actionLatestActivity()
	{
		if (!$this->options()->enableNewsFeed)
		{
			throw $this->exception($this->error(\XF::phrase('news_feed_disabled'), $this->app->config('serviceUnavailableCode')));
		}

		$this->assertCanonicalUrl($this->buildLink('whats-new/latest-activity'));

		$newsFeedRepo = $this->repository('XF:NewsFeed');
		$maxItems = $this->options()->newsFeedMaxItems;

		$beforeId = $this->filter('before_id', 'uint');

		$newsFeedFinder = $newsFeedRepo->findNewsFeed()
			->beforeFeedId($beforeId);

		$items = $newsFeedFinder->fetch($maxItems * 2);
		$newsFeedRepo->addContentToNewsFeedItems($items);
		$items = $items->filterViewable();

		/** @var ArrayCollection $items */
		$items = $items->slice(0, $maxItems);

		$lastItem = $items->last();
		$oldestItemId = $lastItem ? $lastItem->news_feed_id : 0;

		$viewParams = [
			'newsFeedItems' => $items,
			'oldestItemId' => $oldestItemId,
			'beforeId' => $beforeId
		];
		return $this->view('XF:WhatsNew\LatestActivity', 'latest_activity', $viewParams);
	}

	public function actionNewsFeed()
	{
		if (!$this->options()->enableNewsFeed)
		{
			throw $this->exception($this->error(\XF::phrase('news_feed_disabled'), $this->app->config('serviceUnavailableCode')));
		}

		$visitor = \XF::visitor();
		if (!$visitor->user_id)
		{
			return $this->redirect(
				$this->buildLink('whats-new/latest-activity')
			);
		}

		$newsFeed = [];
		$oldestItemId = 0;
		$beforeId = 0;

		if ($visitor->Profile->following)
		{
			$maxItems = $this->options()->newsFeedMaxItems;

			/** @var \XF\Repository\NewsFeed $newsFeedRepo */
			$newsFeedRepo = $this->repository('XF:NewsFeed');

			$beforeId = $this->filter('before_id', 'uint');

			$newsFeedFinder = $newsFeedRepo->findNewsFeed();
			$newsFeedFinder
				->beforeFeedId($beforeId)
				->forUser($visitor);

			$newsFeed = $newsFeedFinder->fetch($maxItems * 2);
			$newsFeedRepo->addContentToNewsFeedItems($newsFeed);
			$newsFeed = $newsFeed->filterViewable();
			$newsFeed = $newsFeed->slice(0, $maxItems);

			if ($newsFeed->count())
			{
				$oldestItemId = min(array_keys($newsFeed->toArray()));
			}
		}

		$viewParams = [
			'newsFeedItems' => $newsFeed,
			'oldestItemId' => $oldestItemId,
			'beforeId' => $beforeId
		];
		return $this->view('XF:WhatsNew\NewsFeed', 'news_feed', $viewParams);
	}

	public function actionPosts()
	{
		return $this->redirectPermanently(
			$this->buildLink('whats-new/posts')
		);
	}

	public function actionProfilePosts()
	{
		return $this->redirectPermanently(
			$this->buildLink('whats-new/profile-posts')
		);
	}

	public static function getActivityDetails(array $activities)
	{
		return \XF::phrase('viewing_latest_content');
	}
}