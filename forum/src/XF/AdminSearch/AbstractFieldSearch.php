<?php

namespace XF\AdminSearch;

use XF\Mvc\Entity\Entity;
use XF\Mvc\Router;

abstract class AbstractFieldSearch extends AbstractHandler
{
	abstract protected function getFinderName();
	abstract protected function getContentIdName();
	abstract protected function getRouteName();

	/**
	 * @var array Fields to be searched for $text. The first field here is assumed to be the title field.
	 */
	protected $searchFields = ['title'];

	public function search($text, $limit, array $previousMatchIds = [])
	{
		$finder = $this->app->finder($this->getFinderName());

		$conditions = [];
		$escapedLike = $finder->escapeLike($text, '%?%');

		foreach ($this->searchFields AS $searchField)
		{
			$conditions[] = [$searchField, 'like', $escapedLike];
		}

		$conditions = $this->getConditions($conditions, $text, $escapedLike);

		if ($previousMatchIds)
		{
			$conditions[] = [$this->getContentIdName(), $previousMatchIds];
		}

		$finder
			->whereOr($conditions)
			->order($this->searchFields[0])
			->limit($limit);

		return $finder->fetch();
	}

	protected function getConditions(array $conditions, $text, $escapedLike)
	{
		return $conditions;
	}

	public function getTemplateData(Entity $record)
	{
		/** @var \XF\Mvc\Router $router */
		$router = $this->app->container('router.admin');

		return $this->getTemplateParams($router, $record, [
			'link' => $router->buildLink($this->getRouteName(), $record),
			'title' => $record->{$this->searchFields[0]}
		]);
	}

	/**
	 * @param Router $router
	 * @param Entity $record
	 * @param array  $templateParams
	 *
	 * @return array
	 */
	protected function getTemplateParams(Router $router, Entity $record, array $templateParams)
	{
		return $templateParams;
	}
}