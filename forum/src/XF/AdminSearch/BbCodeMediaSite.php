<?php

namespace XF\AdminSearch;

use XF\Mvc\Entity\Entity;
use XF\Mvc\Router;

class BbCodeMediaSite extends AbstractFieldSearch
{
	protected $searchFields = ['site_title', 'site_url', 'media_site_id'];

	public function getDisplayOrder()
	{
		return 45;
	}

	protected function getFinderName()
	{
		return 'XF:BbCodeMediaSite';
	}

	protected function getContentIdName()
	{
		return 'media_site_id';
	}

	protected function getRouteName()
	{
		return 'bb-code-media-sites/edit';
	}

	protected function getTemplateParams(Router $router, Entity $record, array $templateParams)
	{
		return $templateParams + ['extra' => $record->site_url];
	}

	public function isSearchable()
	{
		return \XF::visitor()->hasAdminPermission('bbCodeSmilie');
	}
}