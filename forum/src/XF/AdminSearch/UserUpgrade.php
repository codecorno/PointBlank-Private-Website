<?php

namespace XF\AdminSearch;

use XF\Mvc\Entity\Entity;
use XF\Mvc\Router;

class UserUpgrade extends AbstractFieldSearch
{
	protected $searchFields = ['title', 'description'];

	public function getDisplayOrder()
	{
		return 45;
	}

	protected function getFinderName()
	{
		return 'XF:UserUpgrade';
	}

	protected function getContentIdName()
	{
		return 'user_upgrade_id';
	}

	protected function getRouteName()
	{
		return 'user-upgrades/edit';
	}

	protected function getTemplateParams(Router $router, Entity $record, array $templateParams)
	{
		return $templateParams + ['extra' => $record->cost_phrase];
	}

	public function isSearchable()
	{
		return \XF::visitor()->hasAdminPermission('userUpgrade');
	}
}