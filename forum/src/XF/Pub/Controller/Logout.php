<?php

namespace XF\Pub\Controller;

use XF\Mvc\ParameterBag;
use XF\Mvc\Reply\AbstractReply;

class Logout extends AbstractController
{
	protected function preDispatchController($action, ParameterBag $params)
	{
		\XF\Pub\App::$allowPageCache = false;
	}

	public function actionIndex()
	{
		$this->assertValidCsrfToken($this->filter('t', 'str'));

		/** @var \XF\ControllerPlugin\Login $loginPlugin */
		$loginPlugin = $this->plugin('XF:Login');
		$loginPlugin->logoutVisitor();

		return $this->redirect($this->buildLink('index'));
	}

	public function updateSessionActivity($action, ParameterBag $params, AbstractReply &$reply) {}

	public function assertNotRejected($action) {}
	public function assertNotDisabled($action) {}
	public function assertViewingPermissions($action) {}
	public function assertCorrectVersion($action) {}
	public function assertBoardActive($action) {}
	public function assertTfaRequirement($action) {}
	public function assertPolicyAcceptance($action) {}
}