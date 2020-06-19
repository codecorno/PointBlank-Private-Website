<?php

namespace XF\Repository;

use XF\Mvc\Entity\Finder;
use XF\Mvc\Entity\Repository;

class ImageProxy extends Repository
{
	/**
	 * @return Finder
	 */
	public function findImageProxyLogsForList()
	{
		return $this->finder('XF:ImageProxy')->setDefaultOrder('last_request_date', 'DESC');
	}

	/**
	 * @param string $url
	 *
	 * @return null|\XF\Entity\ImageProxy
	 */
	public function getImageByUrl($url)
	{
		$url = $this->cleanUrlForFetch($url);
		$hash = md5($url);

		return $this->finder('XF:ImageProxy')->where('url_hash', $hash)->fetchOne();
	}

	public function getTotalActiveFetches($activeLength = 60)
	{
		return $this->db()->fetchOne("
			SELECT COUNT(*)
			FROM xf_image_proxy
			WHERE is_processing >= ?
		", time() - $activeLength);
	}

	public function logImageView(\XF\Entity\ImageProxy $image)
	{
		$this->db()->query("
			UPDATE xf_image_proxy SET
				views = views + 1,
				last_request_date = ?
			WHERE image_id = ?
		", [\XF::$time, $image->image_id]);
	}

	public function logImageReferrer(\XF\Entity\ImageProxy $image, $referrer)
	{
		if (!preg_match('#^https?://#i', $referrer))
		{
			return false;
		}

		try
		{
			$this->db()->insert('xf_image_proxy_referrer', [
				'image_id' => $image->image_id,
				'referrer_hash' => md5($referrer),
				'referrer_url' => $referrer,
				'hits' => 1,
				'first_date' => \XF::$time,
				'last_date' => \XF::$time
			], false, 'hits = hits + 1, last_date = VALUES(last_date)');
		}
		catch (\XF\Db\DeadlockException $e)
		{
			// ignore deadlocks here -- we're likely triggering a race condition within MySQL
		}

		return true;
	}

	public function cleanUrlForFetch($url)
	{
		$url = preg_replace('/#.*$/s', '', $url);
		if (preg_match_all('/[^A-Za-z0-9._~:\/?#\[\]@!$&\'()*+,;=%-]/', $url, $matches))
		{
			foreach ($matches[0] AS $match)
			{
				$url = str_replace($match[0], '%' . strtoupper(dechex(ord($match[0]))), $url);
			}
		}
		$url = preg_replace('/%(?![a-fA-F0-9]{2})/', '%25', $url);

		return $url;
	}

	/**
	 * @return \XF\Entity\ImageProxy
	 */
	public function getPlaceholderImage()
	{
		// TODO: ability to customize path
		$path = \XF::getRootDirectory() . '/styles/default/xenforo/missing-image.png';

		/** @var \XF\Entity\ImageProxy $image */
		$image = $this->em->create('XF:ImageProxy');
		$image->setAsPlaceholder($path, 'image/png', 'missing-image.png');

		return $image;
	}

	/**
	 * Prunes images from the file system cache that have expired
	 *
	 * @param integer|null $pruneDate
	 */
	public function pruneImageCache($pruneDate = null)
	{
		if ($pruneDate === null)
		{
			if (!$this->options()->imageCacheTTL)
			{
				return;
			}

			$pruneDate = \XF::$time - (86400 * $this->options()->imageCacheTTL);
		}

		/** @var \XF\Entity\ImageProxy[] $images */
		$images = $this->finder('XF:ImageProxy')
			->where('fetch_date', '<', $pruneDate)
			->where('pruned', 0)
			->where('is_processing', 0)
			->fetch(2000);
		foreach ($images AS $image)
		{
			$image->prune();
		}
	}

	/**
	 * Prunes unused image proxy log entries.
	 *
	 * @param null|int $pruneDate
	 *
	 * @return int
	 */
	public function pruneImageProxyLogs($pruneDate = null)
	{
		if ($pruneDate === null)
		{
			$options = $this->options();

			if (!$options->imageLinkProxyLogLength)
			{
				return 0;
			}
			if (!$options->imageCacheTTL)
			{
				// we're keeping images forever - can't prune
				return 0;
			}

			$maxTtl = max($options->imageLinkProxyLogLength, $options->imageCacheTTL);
			$pruneDate = \XF::$time - (86400 * $maxTtl);
		}

		// we can only remove logs where we've pruned the image
		return $this->db()->delete('xf_image_proxy',
			'pruned = 1 AND last_request_date < ?', $pruneDate
		);
	}

	public function pruneImageReferrerLogs($pruneDate = null)
	{
		if ($pruneDate === null)
		{
			$options = $this->options();

			if (empty($options->imageLinkProxyReferrer['length']))
			{
				// we're keeping referrer data forever
				return 0;
			}

			$pruneDate = \XF::$time - (86400 * $options->imageLinkProxyReferrer['length']);
		}

		return $this->db()->delete('xf_image_proxy_referrer',
			'last_date < ?', $pruneDate
		);
	}
}