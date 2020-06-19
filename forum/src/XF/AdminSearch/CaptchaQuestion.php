<?php

namespace XF\AdminSearch;

use XF\Mvc\Entity\Entity;
use XF\Mvc\Router;

class CaptchaQuestion extends AbstractFieldSearch
{
	protected $searchFields = ['question'];

	public function getDisplayOrder()
	{
		return 45;
	}

	protected function getFinderName()
	{
		return 'XF:CaptchaQuestion';
	}

	protected function getContentIdName()
	{
		return 'captcha_question_id';
	}

	protected function getRouteName()
	{
		return 'captcha-questions/edit';
	}

	public function isSearchable()
	{
		return \XF::visitor()->hasAdminPermission('option');
	}
}