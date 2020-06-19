<?php

namespace XF\Pub\Controller;

use XF\ConnectedAccount\Provider\AbstractProvider;
use XF\ConnectedAccount\ProviderData\AbstractProviderData;
use XF\Mvc\ParameterBag;

class Register extends AbstractController
{
	protected function preDispatchController($action, ParameterBag $params)
	{
		\XF\Pub\App::$allowPageCache = false;
	}

	public function actionIndex()
	{
		if (\XF::visitor()->user_id)
		{
			return $this->redirect($this->getDynamicRedirectIfNot($this->buildLink('register')), '');
		}

		$this->assertRegistrationActive();

		$fields = [];
		if ($login = $this->filter('login', 'str'))
		{
			if ($this->app->validator('Email')->isValid($login))
			{
				$fields['email'] = $login;
			}
			else
			{
				$fields['username'] = $login;
			}
		}

		/** @var \XF\Service\User\RegisterForm $regForm */
		$regForm = $this->service('XF:User\RegisterForm');
		$regForm->saveStateToSession($this->session());

		$viewParams = [
			'fields' => $fields,
			'regForm' => $regForm,
			'providers' => $this->repository('XF:ConnectedAccount')->getUsableProviders(true)
		];
		return $this->view('XF:Register\Form', 'register_form', $viewParams);
	}

	public function actionConnectedAccount(ParameterBag $params)
	{
		$provider = $this->assertProviderExists($params->provider_id);
		$handler = $provider->getHandler();

		if (!$provider->isUsable())
		{
			throw $this->exception(
				$this->error(\XF::phrase('this_connected_account_provider_is_not_currently_available'))
			);
		}

		$redirect = $this->getDynamicRedirect();
		$visitor = \XF::visitor();

		if ($visitor->user_id && $provider->isAssociated($visitor))
		{
			return $this->redirect($redirect);
		}

		$storageState = $handler->getStorageState($provider, $visitor);

		if ($this->filter('setup', 'bool'))
		{
			$storageState->clearToken();
			return $handler->handleAuthorization($this, $provider, $redirect);
		}

		$session = $this->session();
		$connectedAccountRequest = $session->get('connectedAccountRequest');

		if (!is_array($connectedAccountRequest) || !isset($connectedAccountRequest['provider']))
		{
			if ($visitor->user_id)
			{
				// user may have just logged in while in the middle of a request
				// so just redirect to the index without showing an error.
				return $this->redirect($this->buildLink('index'));
			}
			else
			{
				return $this->error(\XF::phrase('there_is_no_valid_connected_account_request_available'));
			}
		}

		if ($connectedAccountRequest['provider'] !== $provider->provider_id)
		{
			$session->remove('connectedAccountRequest');
			$session->save();
			return $this->error(\XF::phrase('there_is_no_valid_connected_account_request_available'));
		}

		if (!$storageState->getProviderToken() || empty($connectedAccountRequest['tokenStored']))
		{
			return $this->error(\XF::phrase('error_occurred_while_connecting_with_x', ['provider' => $provider->title]));
		}

		$redirect = $connectedAccountRequest['returnUrl'];

		$providerData = $handler->getProviderData($storageState);

		// If we find this provider account is associated with a local account, we'll log into it.
		$connectedRepo = $this->getConnectedAccountRepo();
		$userConnected = $connectedRepo->getUserConnectedAccountFromProviderData($providerData);
		if ($userConnected && $userConnected->User)
		{
			if ($visitor->user_id)
			{
				return $this->error(\XF::phrase('this_account_is_already_associated_with_another_member'));
			}

			// otherwise, just log into that account
			$userConnected->extra_data = $providerData->extra_data;
			$userConnected->save();

			$associatedUser = $userConnected->User;

			/** @var \XF\ControllerPlugin\Login $loginPlugin */
			$loginPlugin = $this->plugin('XF:Login');
			$loginPlugin->triggerIfTfaConfirmationRequired(
				$associatedUser,
				$this->buildLink('login/two-step', null, [
					'_xfRedirect' => $redirect,
					'remember' => 1
				])
			);
			$loginPlugin->completeLogin($associatedUser, true);

			return $this->redirect($redirect, '');
		}

		// We know the account isn't associated, but if its email matches someone else, we can't continue.
		// (If it matches our current account, we just disregard it.)
		if ($providerData->email)
		{
			$emailUser = $this->em()->findOne('XF:User', ['email' => $providerData->email]);
			if ($emailUser && $emailUser->user_id != $visitor->user_id)
			{
				return $this->error(\XF::phrase('this_accounts_email_is_already_associated_with_another_member'));
			}
		}

		$viewParams = [
			'provider' => $provider,
			'providerData' => $providerData,
			'redirect' => $redirect
		];

		if ($visitor->user_id)
		{
			return $this->getConnectedAssociateResponse($viewParams);
		}
		else
		{
			return $this->getConnectedRegisterResponse($viewParams);
		}
	}

	protected function getConnectedAssociateResponse(array $viewParams)
	{
		$this->assertBoardActive(null);

		$visitor = \XF::visitor();

		/** @var \XF\Entity\UserAuth $auth */
		$auth = $visitor->Auth;
		if (!$auth || !$auth->getAuthenticationHandler()->hasPassword())
		{
			/** @var \XF\Service\User\PasswordReset $passwordConfirmation */
			$passwordConfirmation = $this->service('XF:User\PasswordReset', $visitor);
			$passwordConfirmation->triggerConfirmation();

			$passwordEmailed = true;
		}
		else
		{
			$passwordEmailed = false;
		}

		$viewParams['passwordEmailed'] = $passwordEmailed;

		return $this->view('XF:Account\ConnectedAssociate', 'account_connected_associate', $viewParams);
	}

	protected function getConnectedRegisterResponse(array $viewParams)
	{
		$this->assertBoardActive(null);

		return $this->view('XF:Register\ConnectedAccount', 'register_connected_account', $viewParams);
	}

	public function actionConnectedAccountAssociate(ParameterBag $params)
	{
		$this->assertPostOnly();

		$redirect = $this->getDynamicRedirect(null, false);

		$visitor = \XF::visitor();
		if (!$visitor->user_id)
		{
			return $this->redirect($redirect);
		}

		$provider = $this->assertProviderExists($params->provider_id);
		$handler = $provider->getHandler();

		if (!$provider->isUsable())
		{
			throw $this->exception(
				$this->error(\XF::phrase('this_connected_account_provider_is_not_currently_available'))
			);
		}

		$storageState = $handler->getStorageState($provider, $visitor);
		$providerData = $handler->getProviderData($storageState);

		if (!$storageState->getProviderToken())
		{
			return $this->error(\XF::phrase('error_occurred_while_connecting_with_x', ['provider' => $provider->title]));
		}

		if (!$visitor->user_id)
		{
			return $this->error(\XF::phrase('to_associate_existing_account_first_log_in'));
		}

		$userConnected = $this->getConnectedAccountRepo()->getUserConnectedAccountFromProviderData($providerData);
		if ($userConnected && $userConnected->user_id != $visitor->user_id)
		{
			return $this->error(\XF::phrase('this_account_is_already_associated_with_another_member'));
		}

		/** @var \XF\Service\User\Login $loginService */
		$loginService = $this->service('XF:User\Login', $visitor->username, $this->request->getIp());
		if ($loginService->isLoginLimited())
		{
			return $this->error(\XF::phrase('your_account_has_temporarily_been_locked_due_to_failed_login_attempts'));
		}

		$password = $this->filter('password', 'str');
		$user = $loginService->validate($password, $error);
		if (!$user)
		{
			return $this->error(\XF::phrase('your_existing_password_is_not_correct'));
		}

		$this->getConnectedAccountRepo()->associateConnectedAccountWithUser($visitor, $providerData);

		return $this->redirect($redirect);
	}

	public function actionConnectedAccountRegister(ParameterBag $params)
	{
		$this->assertRegistrationActive();
		$this->assertPostOnly();

		$redirect = $this->getDynamicRedirect(null, false);

		$visitor = \XF::visitor();
		if ($visitor->user_id)
		{
			return $this->redirect($redirect);
		}

		$provider = $this->assertProviderExists($params->provider_id);
		$handler = $provider->getHandler();

		if (!$provider->isUsable())
		{
			throw $this->exception(
				$this->error(\XF::phrase('this_connected_account_provider_is_not_currently_available'))
			);
		}

		$storageState = $handler->getStorageState($provider, $visitor);
		$providerData = $handler->getProviderData($storageState);

		if (!$storageState->getProviderToken())
		{
			return $this->error(\XF::phrase('error_occurred_while_connecting_with_x', ['provider' => $provider->title]));
		}

		$userConnected = $this->getConnectedAccountRepo()->getUserConnectedAccountFromProviderData($providerData);
		if ($userConnected && $userConnected->User)
		{
			return $this->error(\XF::phrase('this_account_is_already_associated_with_another_member'));
		}

		$input = $this->getConnectedRegistrationInput($providerData);
		$registration = $this->setupConnectedRegistration($input, $providerData);
		$registration->checkForSpam();

		if (!$registration->validate($errors))
		{
			return $this->error($errors);
		}

		$user = $registration->save();

		$this->getConnectedAccountRepo()->associateConnectedAccountWithUser($user, $providerData);

		$this->finalizeRegistration($user);

		return $this->redirect($this->buildLink('register/complete'));
	}

	protected function getConnectedRegistrationInput(AbstractProviderData $providerData)
	{
		$input = $this->filter([
			'username' => 'str',
			'email' => 'str',
			'timezone' => 'str',
			'location' => 'str',
			'dob_day' => 'uint',
			'dob_month' => 'uint',
			'dob_year' => 'uint',
			'custom_fields' => 'array',
			'email_choice' => 'bool'
		]);

		$filterer = $this->app->inputFilterer();

		if ($providerData->email)
		{
			$input['email'] = $filterer->cleanString($providerData->email);
		}
		if ($providerData->location)
		{
			$input['location'] = $filterer->cleanString($providerData->location);
		}
		if ($providerData->dob)
		{
			$dob = $providerData->dob;
			$input['dob_day'] = $dob['dob_day'];
			$input['dob_month'] = $dob['dob_month'];
			$input['dob_year'] = $dob['dob_year'];
		}

		return $input;
	}

	protected function setupConnectedRegistration(array $input, AbstractProviderData $providerData)
	{
		/** @var \XF\Service\User\Registration $registration */
		$registration = $this->service('XF:User\Registration');
		$registration->setFromInput($input);
		$registration->setNoPassword();

		if ($providerData->email)
		{
			$registration->skipEmailConfirmation();
		}

		$avatarUrl = $providerData->avatar_url;
		if ($avatarUrl)
		{
			$registration->setAvatarUrl($avatarUrl);
		}

		return $registration;
	}

	public function actionRegister()
	{
		$this->assertPostOnly();
		$this->assertRegistrationActive();

		/** @var \XF\Service\User\RegisterForm $regForm */
		$regForm = $this->service('XF:User\RegisterForm', $this->session());
		if (!$regForm->isValidRegistrationAttempt($this->request(), $error))
		{
			// they failed something that a legit user shouldn't fail, redirect so the key is different
			$regForm->clearStateFromSession($this->session());
			return $this->redirect($this->buildLink('register'));
		}

		$privacyPolicyUrl = $this->app->container('privacyPolicyUrl');
		$tosUrl = $this->app->container('tosUrl');

		if (($privacyPolicyUrl || $tosUrl) && !$this->filter('accept', 'bool'))
		{
			if ($privacyPolicyUrl && $tosUrl)
			{
				return $this->error(\XF::phrase('please_read_and_accept_our_terms_and_privacy_policy_before_continuing'));
			}
			else if ($tosUrl)
			{
				return $this->error(\XF::phrase('please_read_and_accept_our_terms_and_rules_before_continuing'));
			}
			else
			{
				return $this->error(\XF::phrase('please_read_and_accept_our_privacy_policy_before_continuing'));
			}
		}

		if (!$this->captchaIsValid())
		{
			return $this->error(\XF::phrase('did_not_complete_the_captcha_verification_properly'));
		}

		$input = $this->getRegistrationInput($regForm);
		$registration = $this->setupRegistration($input);
		$registration->checkForSpam();

		if (!$registration->validate($errors))
		{
			return $this->error($errors);
		}

		$user = $registration->save();
		$this->finalizeRegistration($user);

		return $this->redirect($this->buildLink('register/complete'));
	}

	protected function getRegistrationInput(\XF\Service\User\RegisterForm $regForm)
	{
		$input = $regForm->getHashedInputValues($this->request);
		$input += $this->request->filter([
			'location' => 'str',
			'dob_day' => 'uint',
			'dob_month' => 'uint',
			'dob_year' => 'uint',
			'custom_fields' => 'array'
		]);

		if ($this->options()->registrationSetup['requireEmailChoice'])
		{
			$input['email_choice'] = $this->request->filter('email_choice', 'bool');
		}

		return $input;
	}

	protected function setupRegistration(array $input)
	{
		/** @var \XF\Service\User\Registration $registration */
		$registration = $this->service('XF:User\Registration');
		$registration->setFromInput($input);

		return $registration;
	}

	protected function finalizeRegistration(\XF\Entity\User $user)
	{
		$this->session()->changeUser($user);
		\XF::setVisitor($user);

		/** @var \XF\ControllerPlugin\Login $loginPlugin */
		$loginPlugin = $this->plugin('XF:Login');
		$loginPlugin->createVisitorRememberKey();
	}

	public function actionComplete()
	{
		$visitor = \XF::visitor();
		if (!$visitor->user_id || $visitor->register_date < \XF::$time - 3600)
		{
			return $this->redirect($this->buildLink('index'));
		}

		$viewParams = [
			'redirect' => $this->filter('redirect', 'str')
		];
		return $this->view('XF:Register\Complete', 'register_complete', $viewParams);
	}

	protected function assertRegistrationActive()
	{
		if (!$this->options()->registrationSetup['enabled'])
		{
			throw $this->exception(
				$this->error(\XF::phrase('new_registrations_currently_not_being_accepted'))
			);
		}

		// prevent discouraged IP addresses from registering
		if ($this->options()->preventDiscouragedRegistration && $this->isDiscouraged())
		{
			throw $this->exception(
				$this->error(\XF::phrase('new_registrations_currently_not_being_accepted'))
			);
		}
	}

	/**
	 * @return \XF\Repository\User
	 */
	protected function getUserRepo()
	{
		return $this->repository('XF:User');
	}

	/**
	 * @param string $id
	 * @param array|string|null $with
	 * @param null|string $phraseKey
	 *
	 * @return \XF\Entity\ConnectedAccountProvider
	 */
	protected function assertProviderExists($id, $with = null, $phraseKey = null)
	{
		return $this->assertRecordExists('XF:ConnectedAccountProvider', $id, $with, $phraseKey);
	}

	/**
	 * @return \XF\Repository\ConnectedAccount
	 */
	protected function getConnectedAccountRepo()
	{
		return $this->repository('XF:ConnectedAccount');
	}

	public function assertViewingPermissions($action) {}
	public function assertTfaRequirement($action) {}

	public function assertBoardActive($action)
	{
		switch (strtolower($action))
		{
			case 'connectedaccount':
				break;

			default:
				parent::assertBoardActive($action);
		}
	}

	public static function getActivityDetails(array $activities)
	{
		return \XF::phrase('registering');
	}
}