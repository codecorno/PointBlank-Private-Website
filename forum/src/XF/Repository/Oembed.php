<?php

namespace XF\Repository;

use XF\Mvc\Entity\Finder;
use XF\Mvc\Entity\Repository;

class Oembed extends Repository
{
	/**
	 * @return Finder
	 */
	public function findOembedLogsForList()
	{
		return $this->finder('XF:Oembed')
			->with('BbCodeMediaSite')
			->setDefaultOrder('last_request_date', 'DESC');
	}

	/**
	 * @param $mediaSiteId
	 * @param $mediaId
	 *
	 * @return null|\XF\Entity\Oembed
	 */
	public function getOembed($mediaSiteId, $mediaId)
	{
		return $this->finder('XF:Oembed')
			->with('BbCodeMediaSite')
			->where('media_site_id', $mediaSiteId)
			->where('media_id', $mediaId)
			->fetchOne();
	}

	/**
	 * @param \XF\Entity\BbCodeMediaSite|string $mediaSiteId Use the BbCodeMediaSite entity to avoid a query,
	 *                                                       otherwise use media_site_id
	 * @param string $mediaId
	 *
	 * @return string
	 */
	public function getOembedUrl($mediaSiteId, $mediaId)
	{
		if ($this->isSiteEntity($mediaSiteId))
		{
			$site = $mediaSiteId;
		}
		else
		{
			$site = $this->getMediaSite($mediaSiteId);
		}

		$url = $site->oembed_api_endpoint
		. (strpos($site->oembed_api_endpoint, '?') === false ? '?' : '&')
		. 'format=json&'
		. 'url=' . urlencode(str_replace('{$id}', $mediaId, $site->oembed_url_scheme));

		return $url;
	}

	public function getTotalActiveFetches($activeLength = 60)
	{
		return $this->db()->fetchOne("
			SELECT COUNT(*)
			FROM xf_oembed
			WHERE is_processing >= ?
		", time() - $activeLength);
	}

	public function getOembedFailure()
	{
		/** @var \XF\Entity\Oembed $oEmbed */
		$oEmbed = $this->em->create('XF:Oembed');
		$oEmbed->setAsFailure();

		return $oEmbed;
	}

	public function getOembedFailureResponse($mediaSiteId, $mediaId, $error)
	{
		return json_encode([
			'provider' => $mediaSiteId,
			'id' => $mediaId,
			'xf-oembed-error' => $error
		]);
	}

	public function logOembedRequest(\XF\Entity\Oembed $oEmbed)
	{
		$this->db()->query("
			UPDATE xf_oembed SET
				views = views + 1,
				last_request_date = ?
			WHERE oembed_id = ?
		", [\XF::$time, $oEmbed->oembed_id]);
	}

	public function logOembedReferrer(\XF\Entity\Oembed $oEmbed, $referrer)
	{
		if (!preg_match('#^https?://#i', $referrer))
		{
			return false;
		}

		$this->db()->insert('xf_oembed_referrer', [
			'oembed_id' => $oEmbed->oembed_id,
			'referrer_hash' => md5($referrer),
			'referrer_url' => $referrer,
			'hits' => 1,
			'first_date' => \XF::$time,
			'last_date' => \XF::$time
		], false, 'hits = hits + 1, last_date = VALUES(last_date)');

		return true;
	}

	protected function getMediaSite($mediaSiteId)
	{
		return $this->finder('XF:BbCodeMediaSite')
			->where('media_site_id', $mediaSiteId)
			->where('oembed_api_endpoint', '<>', '')
			->fetchOne();
	}

	public function findOembedMediaSitesForList()
	{
		return $this->finder('XF:BbCodeMediaSite')
			->where('oembed_enabled', 1)
			->order('site_title');
	}

	/**
	 * Prunes expired oembed data from the file system
	 *
	 * @param integer|null $pruneDate
	 */
	public function pruneOembedCache($pruneDate = null)
	{
		if ($pruneDate === null)
		{
			if (!$this->options()->oEmbedCacheTTL)
			{
				return;
			}

			$pruneDate = \XF::$time - (86400 * $this->options()->oEmbedCacheTTL);
		}

		/** @var \XF\Entity\Oembed[] $images */
		$oEmbeds = $this->finder('XF:Oembed')
			->where('fetch_date', '<', $pruneDate)
			->where('pruned', 0)
			->where('is_processing', 0)
			->fetch(2000);
		foreach ($oEmbeds AS $oEmbed)
		{
			$oEmbed->prune();
		}
	}

	/**
	 * Prunes unused oEmbed log entries.
	 *
	 * @param null|int $pruneDate
	 *
	 * @return int
	 */
	public function pruneOembedLogs($pruneDate = null)
	{
		if ($pruneDate === null)
		{
			$options = $this->options();

			if (!$options->oEmbedLogLength)
			{
				return 0;
			}
			if (!$options->oEmbedCacheTTL)
			{
				// don't prune if we are not expiring oEmbed data
				return 0;
			}

			$maxTtl = max($options->oEmbedLogLength, $options->oEmbedCacheTTL);
			$pruneDate = \XF::$time - (86400 * $maxTtl);
		}

		// we can only remove logs where we've pruned the data
		return $this->db()->delete('xf_oembed',
			'pruned = 1 AND last_request_date < ?', $pruneDate
		);
	}

	public function pruneOembedReferrerLogs($pruneDate = null)
	{
		if ($pruneDate === null)
		{
			$options = $this->options();

			if (empty($options->oEmbedRequestReferrer['length']))
			{
				// we're keeping referrer data forever
				return 0;
			}

			$pruneDate = \XF::$time - (86400 * $options->oEmbedRequestReferrer['length']);
		}

		return $this->db()->delete('xf_oembed_referrer',
			'last_date < ?', $pruneDate
		);
	}

	protected function isSiteEntity($thing)
	{
		return $thing instanceof \XF\Entity\BbCodeMediaSite;
	}
}