<?php

namespace XF\AdminSearch;

use XF\Mvc\Entity\AbstractCollection;
use XF\Mvc\Entity\Entity;
use XF\Template\Templater;

class User extends AbstractHandler
{
	public function getDisplayOrder()
	{
		return 30;
	}

	public function search($text, $limit, array $previousMatchIds = [])
	{
		$finder = $this->app->finder('XF:User');

		$conditions = [
			['username', 'like', $finder->escapeLike($text, '%?%')],
			['email', 'like', $finder->escapeLike($text, '%?%')]
		];
		if ($previousMatchIds)
		{
			$conditions[] = ['user_id', $previousMatchIds];
		}

		$finder
			->whereOr($conditions)
			->order('username')
			->limit($limit);

		return $finder->fetch();
	}

	public function getTemplateData(Entity $record)
	{
		/** @var \XF\Mvc\Router $router */
		$router = $this->app->container('router.admin');

		return [
			'link' => $router->buildLink('users/edit', $record),
			'title' => $record->username,
			'extra' => $record->email
		];
	}

	public function isSearchable()
	{
		return \XF::visitor()->hasAdminPermission('user');
	}
}