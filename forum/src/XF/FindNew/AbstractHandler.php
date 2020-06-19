<?php

namespace XF\FindNew;

use XF\Entity\FindNew;

abstract class AbstractHandler
{
	protected $contentType;

	abstract public function getRoute();
	abstract public function getPageReply(
		\XF\Mvc\Controller $controller, FindNew $findNew, array $results, $page, $perPage
	);
	abstract public function getFiltersFromInput(\XF\Http\Request $request);
	abstract public function getDefaultFilters();
	abstract public function getResultIds(array $filters, $maxResults);
	abstract public function getPageResultsEntities(array $ids);
	abstract public function getResultsPerPage();

	public function __construct($contentType)
	{
		$this->contentType = $contentType;
	}

	public function isAvailable()
	{
		return true;
	}

	/**
	 * @param array $ids
	 *
	 * @return \XF\Mvc\Entity\ArrayCollection
	 */
	public function getPageResults(array $ids)
	{
		$results = $this->getPageResultsEntities($ids);
		$results = $this->filterResults($results);
		return $results->sortByList($ids);
	}

	protected function filterResults(\XF\Mvc\Entity\ArrayCollection $results)
	{
		return $results->filterViewable();
	}

	public function getContentType()
	{
		return $this->contentType;
	}
}