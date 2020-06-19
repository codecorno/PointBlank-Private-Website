<?php

namespace XF\AdminSearch;

use XF\Mvc\Entity\Entity;
use XF\Mvc\Router;

class Notice extends AbstractFieldSearch
{
	public function getDisplayOrder()
	{
		return 45;
	}

	protected function getFinderName()
	{
		return 'XF:Notice';
	}

	protected function getContentIdName()
	{
		return 'notice_id';
	}

	protected function getRouteName()
	{
		return 'notices/edit';
	}

	public function isSearchable()
	{
		return \XF::visitor()->hasAdminPermission('notice');
	}
}