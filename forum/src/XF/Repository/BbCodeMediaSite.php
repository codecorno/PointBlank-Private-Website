<?php

namespace XF\Repository;

use XF\Mvc\Entity\Finder;
use XF\Mvc\Entity\Repository;

class BbCodeMediaSite extends Repository
{
	/**
	 * @return Finder
	 */
	public function findBbCodeMediaSitesForList()
	{
		return $this->finder('XF:BbCodeMediaSite')->order(['media_site_id']);
	}

	/**
	 * @return Finder
	 */
	public function findActiveMediaSites()
	{
		return $this->finder('XF:BbCodeMediaSite')
			->where('active', 1)
			->whereAddOnActive()
			->order('media_site_id');
	}

	public function getBbCodeMediaSiteCacheData()
	{
		$sites = $this->findActiveMediaSites()->fetch();

		$cache = [];

		foreach ($sites AS $siteId => $site)
		{
			$cache[$siteId] = [
				'site_title' => $site->site_title,
				'site_url' => $site->site_url,
				'supported' => $site->supported,
				'oembed_enabled' => $site->oembed_enabled,
				'oembed_url_scheme' => $site->oembed_url_scheme
			];

			if ($site->embed_html_callback_class && $site->embed_html_callback_method)
			{
				$cache[$siteId]['callback'] = [$site->embed_html_callback_class, $site->embed_html_callback_method];
			}
		}

		return $cache;
	}

	public function rebuildBbCodeMediaSiteCache()
	{
		$cache = $this->getBbCodeMediaSiteCacheData();
		\XF::registry()->set('bbCodeMedia', $cache);
		return $cache;
	}

	public function urlMatchesMediaSiteList($url, $mediaSites)
	{
		foreach ($mediaSites AS $site)
		{
			/** @var \XF\Entity\BbCodeMediaSite $site */
			$mediaId = $site->getMediaIdFromUrl($url);
			if ($mediaId)
			{
				return [
					'site' => $site,
					'media_site_id' => $site->media_site_id,
					'media_id' => $mediaId
				];
			}
		}

		return null;
	}
}