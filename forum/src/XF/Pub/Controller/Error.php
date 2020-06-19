<?php

namespace XF\Pub\Controller;

use XF\Mvc\ParameterBag;

class Error extends AbstractController
{
	public function actionDispatchError(ParameterBag $params)
	{
		return $this->plugin('XF:Error')->actionDispatchError($params);
	}

	public function actionException(ParameterBag $params)
	{
		return $this->plugin('XF:Error')->actionException($params->get('exception', false));
	}

	public function actionAddOnUpgrade(ParameterBag $params)
	{
		return $this->plugin('XF:Error')->actionAddOnUpgrade($params);
	}

	public function checkCsrfIfNeeded($action, ParameterBag $params) {}
	public function assertIpNotBanned() {}
	public function assertNotBanned() {}
	public function assertViewingPermissions($action) {}
	public function assertCorrectVersion($action) {}
	public function assertBoardActive($action) {}
	public function assertTfaRequirement($action) {}
	public function assertPolicyAcceptance($action) {}
}