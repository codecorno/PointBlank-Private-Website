<?php

namespace XF\AdminSearch;

use XF\Mvc\Entity\Entity;
use XF\Mvc\Router;

class Navigation extends AbstractPhrased
{
	protected function getFinderName()
	{
		return 'XF:Navigation';
	}

	protected function getContentIdName()
	{
		return 'navigation_id';
	}

	protected function getRouteName()
	{
		return 'navigation/edit';
	}

	public function getDisplayOrder()
	{
		return 50;
	}

	public function getRelatedPhraseGroups()
	{
		return ['nav'];
	}

	protected function getTemplateParams(Router $router, Entity $record, array $templateParams)
	{
		return $templateParams + ['extra' => $record->navigation_id];
	}

	public function isSearchable()
	{
		return \XF::visitor()->hasAdminPermission('navigation');
	}
}