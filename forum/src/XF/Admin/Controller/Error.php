<?php

namespace XF\Admin\Controller;

use XF\Mvc\ParameterBag;

class Error extends AbstractController
{
	public function actionDispatchError(ParameterBag $params)
	{
		// if we got here and we're not logged in, we basically just need to force the login screen
		if (!\XF::visitor()->is_admin)
		{
			return $this->view('XF:Login\Form', 'login_form');
		}

		return $this->plugin('XF:Error')->actionDispatchError($params);
	}

	public function actionException(ParameterBag $params)
	{
		return $this->plugin('XF:Error')->actionException($params->get('exception', false));
	}

	public function actionAddOnUpgrade(ParameterBag $params)
	{
		return $this->plugin('XF:Error')->actionAddOnUpgrade();
	}

	public function checkCsrfIfNeeded($action, ParameterBag $params) {}
	public function assertAdmin() {}
	public function assertCorrectVersion($action) {}
}