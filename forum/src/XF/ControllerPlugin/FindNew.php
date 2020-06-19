<?php

namespace XF\ControllerPlugin;

class FindNew extends AbstractPlugin
{
	public function getRequestedFilters(\XF\FindNew\AbstractHandler $handler)
	{
		$filters = $handler->getFiltersFromInput($this->request);
		if (!$filters)
		{
			// Skip user or type defaults; the filters from the input are explicit.
			// This should be set when submitting the filter form or if you need to link into the system
			// and want no filters guaranteed.
			$skip = $this->filter('skip', 'bool');
			if ($skip)
			{
				$filters = [];
			}
			else
			{
				$filters = $this->getFallbackFilters($handler);
			}
		}

		return $filters;
	}

	public function getFallbackFilters(\XF\FindNew\AbstractHandler $handler)
	{
		$filters = null;

		$userId = \XF::visitor()->user_id;
		if ($userId)
		{
			$filters = $this->getFindNewDefaultRepo()->getUserDefaultFilters($userId, $handler->getContentType());
		}

		if (!is_array($filters))
		{
			$filters = $handler->getDefaultFilters();
		}

		return $filters;
	}

	public function runFindNewSearch(\XF\FindNew\AbstractHandler $handler, array $filters)
	{
		$findNew = $this->em()->create('XF:FindNew');
		$findNew->content_type = $handler->getContentType();
		$findNew->user_id = \XF::visitor()->user_id;
		$findNew->filters = $filters;

		$cacheLength = $findNew->user_id ? 5 : 45; // Increase cache from 5 seconds to 45 seconds for guests

		$cached = $this->getFindNewRepo()->getAvailableCachedFindNewRecord($findNew, $cacheLength);
		if ($cached)
		{
			return $cached;
		}

		$maxResults = $this->options()->maximumSearchResults;
		$findNew->results = $handler->getResultIds($filters, $maxResults);

		return $findNew;
	}

	public function findNewRequiresSaving(\XF\Entity\FindNew $findNew)
	{
		return (!$findNew->exists() && ($findNew->results || $findNew->filters));
	}

	public function saveDefaultFilters(\XF\FindNew\AbstractHandler $handler, array $filters)
	{
		$this->getFindNewDefaultRepo()->saveUserDefaultFilters(
			\XF::visitor()->user_id, $handler->getContentType(), $filters
		);
	}

	/**
	 * @param string $contentType;
	 *
	 * @return \XF\FindNew\AbstractHandler|null
	 */
	public function getFindNewHandler($contentType)
	{
		$handler = $this->getFindNewRepo()->getFindNewHandler($contentType);
		if ($handler && $handler->isAvailable())
		{
			return $handler;
		}
		else
		{
			return null;
		}
	}

	/**
	 * @param integer $findNewId
	 * @param string $expectedContentType
	 *
	 * @return \XF\Entity\FindNew|null
	 */
	public function getFindNewRecord($findNewId, $expectedContentType)
	{
		if (!$findNewId)
		{
			return null;
		}

		$findNew = $this->em()->find('XF:FindNew', $findNewId);

		if (
			$findNew
			&& $findNew->content_type === $expectedContentType
			&& $findNew->user_id == \XF::visitor()->user_id
		)
		{
			return $findNew;
		}
		else
		{
			return null;
		}
	}

	/**
	 * @return \XF\Repository\FindNew
	 */
	protected function getFindNewRepo()
	{
		return $this->repository('XF:FindNew');
	}

	/**
	 * @return \XF\Repository\FindNewDefault
	 */
	protected function getFindNewDefaultRepo()
	{
		return $this->repository('XF:FindNewDefault');
	}
}