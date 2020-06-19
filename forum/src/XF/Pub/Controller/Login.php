<?php

namespace XF\Pub\Controller;

use XF\ControllerPlugin\LoginTfaResult;
use XF\Mvc\ParameterBag;
use XF\Mvc\Reply\AbstractReply;

class Login extends AbstractController
{
	protected function preDispatchController($action, ParameterBag $params)
	{
		\XF\Pub\App::$allowPageCache = false;
	}

	public function actionIndex()
	{
		if (\XF::visitor()->user_id)
		{
			if ($this->filter('check', 'bool'))
			{
				return $this->redirect($this->getDynamicRedirectIfNot($this->buildLink('login')), '');
			}

			return $this->message(\XF::phrase('you_already_logged_in', ['link' => $this->buildLink('forums')]));
		}

		$providers = $this->repository('XF:ConnectedAccount')->getUsableProviders(false);
		$viewParams = [
			'redirect' => $this->getDynamicRedirect(),
			'providers' => $providers
		];
		return $this->view('XF:Login\Form', 'login', $viewParams);
	}

	public function actionLogin()
	{
		if (\XF::visitor()->user_id)
		{
			if ($this->filter('check', 'bool'))
			{
				return $this->redirect($this->getDynamicRedirectIfNot($this->buildLink('login')));
			}

			return $this->message(\XF::phrase('you_already_logged_in', ['link' => $this->buildLink('forums')]));
		}

		$redirect = $this->getDynamicRedirectIfNot($this->buildLink('login'));

		if (!$this->isPost())
		{
			$providers = $this->repository('XF:ConnectedAccount')->getUsableProviders(false);
			$viewParams = [
				'redirect' => $redirect,
				'providers' => $providers
			];
			return $this->view('XF:Login\Form', 'login', $viewParams);
		}

		$this->assertPostOnly();

		$input = $this->filter([
			'login' => 'str',
			'password' => 'str',
			'remember' => 'bool'
		]);

		$ip = $this->request->getIp();

		/** @var \XF\Service\User\Login $loginService */
		$loginService = $this->service('XF:User\Login', $input['login'], $ip);
		if ($loginService->isLoginLimited($limitType))
		{
			if ($limitType == 'captcha')
			{
				if (!$this->captchaIsValid(true))
				{
					$viewParams = [
						'captcha' => true,
						'login' => $input['login'],
						'error' => \XF::phrase('did_not_complete_the_captcha_verification_properly'),
						'redirect' => $redirect
					];
					return $this->view('XF:Login\Form', 'login', $viewParams);
				}
			}
			else
			{
				return $this->error(\XF::phrase('your_account_has_temporarily_been_locked_due_to_failed_login_attempts'));
			}
		}

		$user = $loginService->validate($input['password'], $error);
		if (!$user)
		{
			$loginLimited = $loginService->isLoginLimited($limitType);
			$viewParams = [
				'captcha' => ($loginLimited && $limitType == 'captcha'),
				'login' => $input['login'],
				'error' => $error,
				'redirect' => $redirect
			];
			return $this->view('XF:Login\Form', 'login', $viewParams);
		}



		/** @var \XF\ControllerPlugin\Login $loginPlugin */
		$loginPlugin = $this->plugin('XF:Login');
		$loginPlugin->triggerIfTfaConfirmationRequired(
			$user,
			$this->buildLink('login/two-step', null, [
				'_xfRedirect' => $redirect,
				'remember' => $input['remember'] ? 1 : null
			])
		);
		$loginPlugin->completeLogin($user, $input['remember']);

		// TODO: POST handling?

		return $this->redirect($redirect, '');
	}

	public function actionTwoStep()
	{
		/** @var \XF\ControllerPlugin\Login $loginPlugin */
		$loginPlugin = $this->plugin('XF:Login');

		$input = $this->filter([
			'remember' => 'bool'
		]);

		$redirect = $this->getDynamicRedirectIfNot($this->buildLink('login'));

		$result = $loginPlugin->runTfaCheck($redirect);
		switch ($result->getResult())
		{
			case LoginTfaResult::RESULT_ERROR:
				return $this->error($result->getError());

			case LoginTfaResult::RESULT_FORM:
				$viewParams = $result->getFormParams();
				$viewParams['remember'] = $input['remember'];
				$viewParams['trustChecked'] = ($input['remember'] || $this->request->getCookie('user'));
				return $this->view('XF:Login\TwoStep', 'login_two_step', $viewParams);

			case LoginTfaResult::RESULT_SKIPPED:
				return $this->redirect($result->getRedirect(), '');

			case LoginTfaResult::RESULT_SUCCESS:
				$loginPlugin->completeLogin($result->getUser(), $input['remember']);
				return $this->redirect($result->getRedirect(), '');

			default:
				return $this->error(\XF::phrase('requested_page_not_found'));
		}
	}

	public function actionPasswordConfirm()
	{
		return $this->plugin('XF:Login')->actionPasswordConfirm();
	}

	public function actionKeepAlive()
	{
		return $this->plugin('XF:Login')->actionKeepAlive();
	}

	public function checkCsrfIfNeeded($action, ParameterBag $params)
	{
		switch (strtolower($action))
		{
			case 'login':
			case 'keepalive':
				return;
		}

		parent::checkCsrfIfNeeded($action, $params);
	}

	public function updateSessionActivity($action, ParameterBag $params, AbstractReply &$reply) {}

	public function assertViewingPermissions($action) {}
	public function assertCorrectVersion($action) {}
	public function assertBoardActive($action) {}
	public function assertTfaRequirement($action) {}
	public function assertPolicyAcceptance($action) {}
}