<?php

namespace XF\Sitemap;

class Thread extends AbstractHandler
{
	public function getRecords($start)
	{
		$app = $this->app;
		$user = \XF::visitor();

		$ids = $this->getIds('xf_thread', 'thread_id', $start);

		$threadFinder = $app->finder('XF:Thread');
		$threads = $threadFinder
			->where('thread_id', $ids)
			->with(['Forum', 'Forum.Node', 'Forum.Node.Permissions|' . $user->permission_combination_id])
			->order('thread_id')
			->fetch();

		return $threads;
	}

	public function getEntry($record)
	{
		$url = $this->app->router('public')->buildLink('canonical:threads', $record);
		return Entry::create($url, [
			'lastmod' => $record->last_post_date
		]);
	}

	public function isIncluded($record)
	{
		/** @var $record \XF\Entity\Thread */
		if ($record->discussion_type == 'redirect' || !$record->isVisible())
		{
			return false;
		}
		return $record->canView();
	}
}