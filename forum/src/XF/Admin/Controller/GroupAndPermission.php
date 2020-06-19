<?php

namespace XF\Admin\Controller;

use XF\Mvc\ParameterBag;
use XF\Mvc\FormAction;

class GroupAndPermission extends AbstractController
{
	public function actionIndex()
	{
		return $this->view('XF:GroupsPermissions', 'groups_permissions');
	}
}