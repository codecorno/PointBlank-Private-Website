<?php

namespace XF\Admin\Controller;

use XF\Mvc\FormAction;
use XF\Mvc\ParameterBag;

class Category extends AbstractNode
{
	protected function getNodeTypeId()
	{
		return 'Category';
	}

	protected function getDataParamName()
	{
		return 'category';
	}

	protected function getTemplatePrefix()
	{
		return 'category';
	}

	protected function getViewClassPrefix()
	{
		return 'XF:Category';
	}
}