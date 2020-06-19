<?php

namespace XF\Repository;

use XF\Entity\UnfurlResult;
use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Repository;

class Unfurl extends Repository
{
	public function logPendingUnfurl($url)
	{
		if (!$url || !preg_match('#^https?://#i', $url))
		{
			throw new \InvalidArgumentException('Invalid URL');
		}

		$affected = $this->db()->insert('xf_unfurl_result', [
			'url' => $url,
			'url_hash' => md5($url),
			'pending' => 1
		], false, '
			pending = VALUES(pending)
		');
		if ($affected == 1)
		{
			$id = $this->db()->lastInsertId();
			return $this->em->find('XF:UnfurlResult', $id);
		}
		else
		{
			return $this->getUnfurlResultByUrl($url);
		}
	}

	public function logError(UnfurlResult $result)
	{
		$result->last_request_date = \XF::$time;
		$result->pending = false;
		$result->error_count = $result->error_count + 1;
		$result->save();
	}

	/**
	 * @param string $url
	 *
	 * @return null|Entity|UnfurlResult
	 */
	public function getUnfurlResultByUrl($url)
	{
		return $this->finder('XF:UnfurlResult')
			->where('url_hash', md5($url))
			->fetchOne();
	}

	public function addUnfurlsToContent($content, $skipRecrawl, $metadataKey = 'embed_metadata', $getterKey = 'Unfurls')
	{
		if (!$content)
		{
			return;
		}

		$unfurlIds = [];
		foreach ($content AS $item)
		{
			$metadata = $item->{$metadataKey};
			if (isset($metadata['unfurls']))
			{
				$unfurlIds = array_merge($unfurlIds, $metadata['unfurls']);
			}
		}

		$unfurls = [];
		if ($unfurlIds)
		{
			$unfurls = $this->finder('XF:UnfurlResult')
				->whereIds(array_unique($unfurlIds))
				->fetch();
		}

		if (!$unfurls || !$unfurls->count())
		{
			return;
		}

		if (!$skipRecrawl)
		{
			$this->recrawlUnfurlsIfNecessary($unfurls);
		}

		foreach ($content AS $item)
		{
			$metadata = $item->{$metadataKey};
			if (isset($metadata['unfurls']))
			{
				$contentUnfurls = [];
				foreach ($metadata['unfurls'] AS $id)
				{
					if (!isset($unfurls[$id]))
					{
						continue;
					}
					$contentUnfurls[$unfurls[$id]->url_hash] = $unfurls[$id];
				}

				$item->{"set$getterKey"}($contentUnfurls);
			}
		}
	}

	/**
	 * @param \XF\Mvc\Entity\ArrayCollection|UnfurlResult[] $unfurls
	 */
	public function recrawlUnfurlsIfNecessary(\XF\Mvc\Entity\ArrayCollection $unfurls)
	{
		foreach ($unfurls AS $result)
		{
			if (!$result->pending && $result->requiresRecrawl())
			{
				$result->pending = true;
				$result->save();
			}
		}
	}
}