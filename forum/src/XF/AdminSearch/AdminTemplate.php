<?php

namespace XF\AdminSearch;

use XF\Mvc\Entity\AbstractCollection;
use XF\Mvc\Entity\Entity;
use XF\Template\Templater;

class AdminTemplate extends PublicTemplate
{
	protected function getSearchTemplateType()
	{
		return 'admin';
	}

	public function isSearchable()
	{
		/** @var \XF\Repository\Style $styleRepo */
		$styleRepo = $this->app->repository('XF:Style');
		if (!$styleRepo->getMasterStyle()->canEdit())
		{
			return false;
		}

		return parent::isSearchable();
	}
}