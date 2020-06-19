<?php

namespace XF\Sitemap;

class Tag extends AbstractHandler
{
	public function getRecords($start)
	{
		$app = $this->app;

		$tagIds = $this->getIds('xf_tag', 'tag_id', $start);

		$tagFinder = $app->finder('XF:Tag');
		$tags = $tagFinder
			->where('tag_id', $tagIds)
			->order('tag_id')
			->fetch();

		return $tags;
	}

	public function getEntry($record)
	{
		/** @var \XF\Entity\Node $record */
		$url = $this->app->router('public')->buildLink('canonical:tags', $record);
		return Entry::create($url, [
			'lastmod' => $record->last_use_date
		]);
	}

	public function basePermissionCheck()
	{
		if (parent::basePermissionCheck())
		{
			return $this->app->options()->enableTagging;
		}
		else
		{
			return false;
		}
	}

	public function isIncluded($record)
	{
		return ($record->use_count > 0);
	}
}