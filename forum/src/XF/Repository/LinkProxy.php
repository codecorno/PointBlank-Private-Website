<?php

namespace XF\Repository;

use XF\Mvc\Entity\Finder;
use XF\Mvc\Entity\Repository;

class LinkProxy extends Repository
{
	/**
	 * @return Finder
	 */
	public function findLinkProxyLogsForList()
	{
		return $this->finder('XF:LinkProxy')->setDefaultOrder('last_request_date', 'DESC');
	}

	/**
	 * @param string $url
	 *
	 * @return null|\XF\Entity\LinkProxy
	 */
	public function getLinkByUrl($url)
	{
		$hash = md5($url);

		return $this->finder('XF:LinkProxy')->where('url_hash', $hash)->fetchOne();
	}

	/**
	 * @param string $url
	 *
	 * @return null|\XF\Entity\LinkProxy
	 */
	public function logLinkVisit($url)
	{
		if (!$url || !preg_match('#^https?://#i', $url))
		{
			throw new \InvalidArgumentException('Invalid URL');
		}

		$affected = $this->db()->insert('xf_link_proxy', [
			'url' => $url,
			'url_hash' => md5($url),
			'first_request_date' => \XF::$time,
			'last_request_date' => \XF::$time,
			'hits' => 1
		], false, 'last_request_date = VALUES(last_request_date), hits = hits + 1');
		if ($affected == 1)
		{
			$id = $this->db()->lastInsertId();
			return $this->em->find('XF:LinkProxy', $id);
		}
		else
		{
			return $this->getLinkByUrl($url);
		}
	}

	public function logLinkReferrer(\XF\Entity\LinkProxy $link, $referrer)
	{
		if (!preg_match('#^https?://#i', $referrer))
		{
			return false;
		}

		$this->db()->insert('xf_link_proxy_referrer', [
			'link_id' => $link->link_id,
			'referrer_hash' => md5($referrer),
			'referrer_url' => $referrer,
			'hits' => 1,
			'first_date' => \XF::$time,
			'last_date' => \XF::$time
		], false, 'hits = hits + 1, last_date = VALUES(last_date)');

		return true;
	}

	/**
	 * Prunes unused link proxy log entries.
	 *
	 * @param null|int $pruneDate
	 *
	 * @return int
	 */
	public function pruneLinkProxyLogs($pruneDate = null)
	{
		if ($pruneDate === null)
		{
			$options = $this->options();

			if (!$options->imageLinkProxyLogLength)
			{
				return 0;
			}

			$pruneDate = \XF::$time - (86400 * $options->imageLinkProxyLogLength);
		}

		return $this->db()->delete('xf_link_proxy', 'last_request_date < ?', $pruneDate);
	}

	public function pruneLinkReferrerLogs($pruneDate = null)
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

		return $this->db()->delete('xf_link_proxy_referrer',
			'last_date < ?', $pruneDate
		);
	}
}