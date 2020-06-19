<?php

namespace XF\Pub\Controller;

use XF\Mvc\ParameterBag;

class EmailStop extends AbstractController
{
	public function assertIpNotBanned() {}
	public function assertViewingPermissions($action) {}
	public function assertPolicyAcceptance($action) {}

	public function actionIndex(ParameterBag $params)
	{
		if ($this->isPost())
		{
			$confirmKey = $this->filter('c', 'str');
			$emailStopper = $this->assertValidatedStopService($params->user_id, $confirmKey);

			$stopAction = $this->filter('stop', 'str');
			$emailStopper->stop($stopAction);

			return $this->message(\XF::phrase('your_email_notification_selections_have_been_updated'));
		}
		else
		{
			return $this->displayConfirmation($params);
		}
	}

	protected function displayConfirmation(ParameterBag $params, array $actions = [])
	{
		$confirmKey = $this->filter('c', 'str');
		$emailStopper = $this->assertValidatedStopService($params->user_id, $confirmKey);

		$actionOptions = $emailStopper->getActionOptions($actions);
		$defaultAction = $actionOptions ? key($actionOptions) : null;

		$viewParams = [
			'user' => $emailStopper->getUser(),
			'confirmKey' => $emailStopper->getConfirmKey(),
			'actions' => $actionOptions,
			'defaultAction' => $defaultAction
		];
		return $this->view('XF:EmailStop\Confirm', 'email_stop_confirm', $viewParams);
	}

	/**
	 * @param integer $userId
	 * @param string $confirmKey
	 *
	 * @return \XF\Service\User\EmailStop
	 * @throws \XF\Mvc\Reply\Exception
	 */
	protected function assertValidatedStopService($userId, $confirmKey)
	{
		$user = $this->app->find('XF:User', $userId);
		if (!$user)
		{
			throw $this->exception(
				$this->error(\XF::phrase('this_link_is_not_usable_by_you'), 403)
			);
		}

		if ($confirmKey !== $user->email_confirm_key)
		{
			throw $this->exception(
				$this->error(\XF::phrase('this_link_could_not_be_verified'), 403)
			);
		}

		/** @var \XF\Service\User\EmailStop $emailStopper */
		$emailStopper = $this->service('XF:User\EmailStop', $user);
		return $emailStopper;
	}

	public function actionAll(ParameterBag $params)
	{
		return $this->displayConfirmation($params);
	}

	public function actionMailingList(ParameterBag $params)
	{
		return $this->displayConfirmation($params, ['list']);
	}

	public function actionConversation(ParameterBag $params)
	{
		return $this->displayConfirmation($params, ['conversations']);
	}

	public function actionContent(ParameterBag $params)
	{
		$type = $this->filter('t', 'str');
		$id = $this->filter('id', 'str');
		if ($id)
		{
			$type .= ":$id";
		}

		return $this->displayConfirmation($params, [$type]);
	}

	public static function getActivityDetails(array $activities)
	{
		return \XF::phrase('managing_account_details');
	}
}