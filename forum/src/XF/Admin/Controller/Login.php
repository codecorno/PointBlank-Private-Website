<?php

namespace XF\Admin\Controller;

use XF\ControllerPlugin\LoginTfaResult;
use XF\Mvc\ParameterBag;

class Login extends AbstractController
{
	public function actionForm()
	{
		return $this->view('XF:Login\Form', 'login_form');
	}

	public function actionLogin()
	{
		$this->assertPostOnly();

		if (\XF::visitor()->user_id)
		{
			return $this->redirect($this->getDynamicRedirectIfNot('login'));
		}

		$input = $this->filter([
			'login' => 'str',
			'password' => 'str'
		]);

		$ip = $this->request->getIp();

		/** @var \XF\Service\User\Login $loginService */
		$loginService = $this->service('XF:User\Login', $input['login'], $ip);
		if ($loginService->isLoginLimited($limitType))
		{
			return $this->error(\XF::phrase('your_account_has_temporarily_been_locked_due_to_failed_login_attempts'));
		}

		$user = $loginService->validate($input['password'], $error);
		if (!$user)
		{
			return $this->error($error);
		}

		if (!$user->is_admin)
		{
			return $this->error(\XF::phrase('your_account_does_not_have_admin_privileges'));
		}

		$redirect = $this->getDynamicRedirectIfNot($this->buildLink('login'));

		/** @var \XF\ControllerPlugin\Login $loginPlugin */
		$loginPlugin = $this->plugin('XF:Login');
		$loginPlugin->triggerIfTfaConfirmationRequired(
			$user,
			$this->buildLink('login/two-step', null, [
				'_xfRedirect' => $redirect
			])
		);

		if (empty($user->Option->use_tfa)
			&& \XF::config('enableTfa')
			&& ($this->options()->adminRequireTfa || $user->hasPermission('general', 'requireTfa'))
		)
		{
			return $this->error(\XF::phrase('you_must_enable_two_step_access_control_panel', [
				'link' => $this->app->router('public')->buildLink('account/two-step')
			]));
		}

		$this->completeLogin($user);

		// TODO: POST handling?

		return $this->redirect($redirect, '');
	}

	public function actionTwoStep()
	{
		/** @var \XF\ControllerPlugin\Login $loginPlugin */
		$loginPlugin = $this->plugin('XF:Login');
		$redirect = $this->getDynamicRedirectIfNot($this->buildLink('login'));

		$result = $loginPlugin->runTfaCheck($redirect);
		switch ($result->getResult())
		{
			case LoginTfaResult::RESULT_ERROR:
				return $this->error($result->getError());

			case LoginTfaResult::RESULT_FORM:
				$viewParams = $result->getFormParams();
				$viewParams['trustChecked'] = $this->request->getCookie('user');
				return $this->view('XF:Login\TwoStep', 'login_two_step', $viewParams);

			case LoginTfaResult::RESULT_SKIPPED:
				return $this->redirect($result->getRedirect(), '');

			case LoginTfaResult::RESULT_SUCCESS:
				$this->completeLogin($result->getUser());
				return $this->redirect($result->getRedirect(), '');

			default:
				return $this->error(\XF::phrase('requested_page_not_found'));
		}
	}

	protected function completeLogin(\XF\Entity\User $user)
	{
		$this->session()->changeUser($user);
		\XF::setVisitor($user);

		$ip = $this->request->getIp();

		$this->repository('XF:Ip')->logIp(
			$user->user_id, $ip,
			'user', $user->user_id, 'login_admin'
		);

		if (!$user->Admin && $user->is_admin)
		{
			$admin = $this->em()->create('XF:Admin');
			$admin->user_id = $user->user_id;
			$admin->last_login = \XF::$time;
			$admin->save();
		}
		else
		{
			$user->Admin->last_login = \XF::$time;
			$user->Admin->save();
		}

		$this->session()->passwordConfirm = \XF::$time;

		/** @var \XF\Session\Session $publicSession */
		$publicSession = $this->app['session.public'];
		if (!$publicSession['userId'])
		{
			$publicSession->changeUser($user);
			$publicSession->save();
			$publicSession->applyToResponse($this->app->response());
		}

		// this is just a sanity check -- faster to run here than on every page if internal_data is remote
		if (!\XF\Util\File::installLockExists())
		{
			\XF\Util\File::writeInstallLock();
		}
	}

	public function actionLogout()
	{
		$this->assertValidCsrfToken($this->filter('t', 'str'));

		$this->session()->logoutUser();

		return $this->redirect($this->buildLink('index'));
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
	
	public function assertAdmin() {}
	public function assertCorrectVersion($action) {}
}