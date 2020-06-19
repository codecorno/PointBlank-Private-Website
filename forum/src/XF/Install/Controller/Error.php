<?php

namespace XF\Install\Controller;

use XF\Mvc\ParameterBag;

class Error extends AbstractController
{
	public function actionDispatchError(ParameterBag $params)
	{
		return $this->notFound();
	}

	public function actionException(ParameterBag $params)
	{
		$exception = $params->get('exception', false);

		$reply = $this->view('XF:Error\Server', '', [
			'exception' => $exception
		]);
		$reply->setResponseCode(500);
		return $reply;
	}

	public function assertIpNotBanned() {}
	public function assertNotBanned() {}
	public function assertViewingPermissions($action) {}
	public function assertCorrectVersion($action) {}
	public function assertBoardActive($action) {}
	public function assertTfaRequirement($action) {}
}