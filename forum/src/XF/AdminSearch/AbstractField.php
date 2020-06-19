<?php

namespace XF\AdminSearch;

use XF\Mvc\Entity\Entity;
use XF\Mvc\Router;

abstract class AbstractField extends AbstractPhrased
{
	protected function getContentIdName()
	{
		return 'field_id';
	}

	protected function getRouteName()
	{
		return 'custom-user-fields/edit';
	}

	public function getDisplayOrder()
	{
		return 50;
	}

	protected function getTemplateParams(Router $router, Entity $record, array $templateParams)
	{
		return $templateParams + ['extra' => $record->field_id];
	}
}