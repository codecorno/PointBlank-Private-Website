<?php

namespace XF\AdminSearch;

use XF\Mvc\Entity\AbstractCollection;
use XF\Mvc\Entity\Entity;
use XF\Template\Templater;

class Node extends AbstractHandler
{
	public function getDisplayOrder()
	{
		return 45;
	}

	public function search($text, $limit, array $previousMatchIds = [])
	{
		$finder = $this->app->finder('XF:Node');

		$conditions = [
			['title', 'like', $finder->escapeLike($text, '%?%')]
		];
		if ($previousMatchIds)
		{
			$conditions[] = ['node_id', $previousMatchIds];
		}

		$finder
			->whereOr($conditions)
			->order('title')
			->limit($limit);

		return $finder->fetch();
	}

	public function getTemplateData(Entity $record)
	{
		/** @var \XF\Mvc\Router $router */
		$router = $this->app->container('router.admin');

		return [
			'link' => $router->buildLink('nodes/edit', $record),
			'title' => $record->title
		];
	}

	public function isSearchable()
	{
		return \XF::visitor()->hasAdminPermission('node');
	}
}