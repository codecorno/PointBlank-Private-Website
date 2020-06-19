<?php

namespace XF\Repository;

use XF\Mvc\Entity\Finder;
use XF\Mvc\Entity\Repository;

class SitemapLog extends Repository
{
	/**
	 * @return Finder
	 */
	public function findSitemapLogsForList()
	{
		return $this->finder('XF:SitemapLog')->setDefaultOrder('sitemap_id', 'DESC');
	}

	/**
	 * @return \XF\Entity\SitemapLog
	 */
	public function getActiveSitemap()
	{
		return $this->finder('XF:SitemapLog')->where('is_active', 1)->order('sitemap_id', 'desc')->fetchOne();
	}

	public function getAbstractedSitemapFileName($fileSet, $fileNumber, $compressed = false, $temp = false)
	{
		$fileSet = intval($fileSet);
		$fileNumber = intval($fileNumber);

		$tempPrefix = $temp ? 'temp-' : '';
		if ($temp)
		{
			$compressed = false;
		}
		$compressedSuffixed = $compressed ? '.gz' : '';

		return "internal-data://sitemaps/{$tempPrefix}sitemap-{$fileSet}-{$fileNumber}.xml{$compressedSuffixed}";
	}

	public function logPendingBuild($id, $entryCount, $fileCount, $compressed)
	{
		$sitemapLog = $this->em->find('XF:SitemapLog', $id);
		if (!$sitemapLog)
		{
			$sitemapLog = $this->em->create('XF:SitemapLog');
			$sitemapLog->sitemap_id = $id;
		}

		$sitemapLog->is_active = 0;
		$sitemapLog->is_compressed = $compressed ? 1 : 0;
		$sitemapLog->file_count = $fileCount;
		$sitemapLog->entry_count = $entryCount;
		$sitemapLog->complete_date = 0;

		$sitemapLog->save();

		return $sitemapLog;
	}

	public function logCompletedBuild($id, $entryCount, $fileCount, $compressed)
	{
		$sitemapLog = $this->em->find('XF:SitemapLog', $id);
		if (!$sitemapLog)
		{
			$sitemapLog = $this->em->create('XF:SitemapLog');
			$sitemapLog->sitemap_id = $id;
		}

		$sitemapLog->is_active = 1;
		$sitemapLog->is_compressed = $compressed ? 1 : 0;
		$sitemapLog->file_count = $fileCount;
		$sitemapLog->entry_count = $entryCount;
		$sitemapLog->complete_date = time();

		$sitemapLog->save();

		return $sitemapLog;
	}

	public function deactivateOldSitemaps($skipId = null)
	{
		$finder = $this->finder('XF:SitemapLog')->where('is_active', 1);
		if ($skipId)
		{
			$finder->where('sitemap_id', '<>', $skipId);
		}

		/** @var \XF\Entity\SitemapLog $sitemap */
		foreach ($finder->fetch() AS $sitemap)
		{
			$sitemap->is_active = 0;
			$sitemap->save();
		}
	}

	public function deleteOldSitemapLogs($cutOff = null)
	{
		$cutOff = $cutOff ?: \XF::$time - 86400 * 60;
		return $this->db()->delete('xf_sitemap', 'sitemap_id < ? AND is_active = 0', $cutOff);
	}

	/**
	 * @return \XF\Sitemap\AbstractHandler[]
	 */
	public function getSitemapHandlers()
	{
		$handlers = [];

		foreach (\XF::app()->getContentTypeField('sitemap_handler_class') AS $contentType => $handlerClass)
		{
			if (class_exists($handlerClass))
			{
				$handlerClass = \XF::extendClass($handlerClass);
				$handlers[$contentType] = new $handlerClass($contentType, $this->app());
			}
		}

		return $handlers;
	}

	/**
	 * @param string $type
	 * @param bool $throw
	 *
	 * @return \XF\Sitemap\AbstractHandler|null
	 */
	public function getSitemapHandler($type, $throw = false)
	{
		$handlerClass = $this->app()->getContentTypeFieldValue($type, 'sitemap_handler_class');
		if (!$handlerClass)
		{
			if ($throw)
			{
				throw new \InvalidArgumentException("No sitemap handler for '$type'");
			}
			return null;
		}

		if (!class_exists($handlerClass))
		{
			if ($throw)
			{
				throw new \InvalidArgumentException("Sitemap handler for '$type' does not exist: $handlerClass");
			}
			return null;
		}

		$handlerClass = \XF::extendClass($handlerClass);
		return new $handlerClass($type, $this->app());
	}

	public function getSitemapContentTypes($includeExcluded = false)
	{
		$types = [];
		$excluded = $this->options()->sitemapExclude;

		foreach (\XF::app()->getContentTypeField('sitemap_handler_class') AS $type => $class)
		{
			if (empty($excluded[$type]) || $includeExcluded)
			{
				if (!class_exists($class))
				{
					continue;
				}

				$types[$type] = $class;
			}
		}

		return $types;
	}
}