<?php

namespace XF\Repository;

use XF\Mvc\Entity\Finder;
use XF\Mvc\Entity\Repository;

class FindNew extends Repository
{
	public function getAvailableCachedFindNewRecord(\XF\Entity\FindNew $baseRecord, $cacheLength)
	{
		$finder = $this->finder('XF:FindNew');
		$finder
			->where([
				'content_type' => $baseRecord->content_type,
				'user_id' => $baseRecord->user_id,
				'filter_hash' => $baseRecord->getFilterHash()
			])
			->where('cache_date', '>=', \XF::$time - $cacheLength)
			->order('cache_date', 'DESC');

		return $finder->fetchOne();
	}

	public function pruneFindNewResults($cutOff = null)
	{
		if ($cutOff === null)
		{
			$cutOff = \XF::$time - 86400;
		}

		return $this->db()->delete('xf_find_new', 'cache_date < ?', $cutOff);
	}

	/**
	 * @return \XF\FindNew\AbstractHandler[]
	 */
	public function getFindNewHandlers()
	{
		$handlers = [];

		foreach (\XF::app()->getContentTypeField('find_new_handler_class') AS $contentType => $handlerClass)
		{
			if (class_exists($handlerClass))
			{
				$handlerClass = \XF::extendClass($handlerClass);
				$handlers[$contentType] = new $handlerClass($contentType);
			}
		}

		return $handlers;
	}

	/**
	 * @param string $type
	 * @param bool $throw
	 *
	 * @return \XF\FindNew\AbstractHandler|null
	 */
	public function getFindNewHandler($type, $throw = false)
	{
		$handlerClass = \XF::app()->getContentTypeFieldValue($type, 'find_new_handler_class');
		if (!$handlerClass)
		{
			if ($throw)
			{
				throw new \InvalidArgumentException("No find new handler for '$type'");
			}
			return null;
		}

		if (!class_exists($handlerClass))
		{
			if ($throw)
			{
				throw new \InvalidArgumentException("Find new handler for '$type' does not exist: $handlerClass");
			}
			return null;
		}

		$handlerClass = \XF::extendClass($handlerClass);
		return new $handlerClass($type);
	}
}