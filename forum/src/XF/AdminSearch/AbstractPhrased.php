<?php

namespace XF\AdminSearch;

use XF\Mvc\Entity\Entity;
use XF\Mvc\Router;

abstract class AbstractPhrased extends AbstractHandler
{
	abstract protected function getFinderName();
	abstract protected function getContentIdName();
	abstract protected function getRouteName();

	public function search($text, $limit, array $previousMatchIds = [])
	{
		$contentFinder = $this->app->finder($this->getFinderName());

		$conditions = [
			[$contentFinder->columnUtf8($this->getContentIdName()), 'like', $contentFinder->escapeLike($text, '%?%')]
		];
		if ($previousMatchIds)
		{
			$conditions[] = [$this->getContentIdName(), $previousMatchIds];
		}

		$contentFinder
			->whereOr($conditions)
			->order($contentFinder->caseInsensitive($this->getContentIdName()))
			->limit($limit);

		return $contentFinder->fetch();
	}

	public function getTemplateData(Entity $record)
	{
		/** @var \XF\Mvc\Router $router */
		$router = $this->app->container('router.admin');

		return $this->getTemplateParams($router, $record, [
			'link' => $router->buildLink($this->getRouteName(), $record),
			'title' => $record->title
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