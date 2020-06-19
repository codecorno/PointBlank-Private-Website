<?php

namespace XF\AdminSearch;

use XF\Mvc\Entity\Entity;
use XF\Mvc\Router;

class Feed extends AbstractFieldSearch
{
	protected $searchFields = ['title', 'url'];

	public function getDisplayOrder()
	{
		return 45;
	}

	protected function getFinderName()
	{
		return 'XF:Feed';
	}

	protected function getContentIdName()
	{
		return 'feed_id';
	}

	protected function getRouteName()
	{
		return 'feeds/edit';
	}

	protected function getTemplateParams(Router $router, Entity $record, array $templateParams)
	{
		return $templateParams + ['extra' => $record->url];
	}

	public function isSearchable()
	{
		return \XF::visitor()->hasAdminPermission('thread');
	}
}