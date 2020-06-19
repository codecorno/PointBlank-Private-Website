<?php

namespace XF\ControllerPlugin;

class Login extends AbstractPlugin
{
	public function isTfaConfirmationRequired(\XF\Entity\User $user)
	{
		$trustKey = $this->getCurrentTrustKey();

		/** @var \XF\Repository\Tfa $tfaRepo */
		$tfaRepo = $this->repository('XF:Tfa');
		return $tfaRepo->isUserTfaConfirmationRequired($user, $trustKey);
	}

	public function getCurrentTrustKey()
	{
		return $this->request->getCookie('tfa_trust');
	}

	public function getTfaLoginUserId()
	{
		if (!$this->session->tfaLoginUserId || \XF::visitor()->user_id)
		{
			return null;
		}

		if (!$this->session->tfaLoginDate || $this->session->tfaLoginDate < time() - 900)
		{
			return null;
		}

		return $this->session->tfaLoginUserId;
	}

	/**
	 * @return null|\XF\Entity\User
	 */
	public function getTfaLoginUser()
	{
		$userId = $this->getTfaLoginUserId();
		if (!$userId)
		{
			return null;
		}

		return $this->em->find('XF:User', $userId, ['Option']);
	}

	public function setTfaSessionCheck(\XF\Entity\User $user)
	{
		$this->session->tfaLoginUserId = $user->user_id;
		$this->session->tfaLoginDate = time();
	}

	public function clearTfaSessionCheck()
	{
		unset($this->session->tfaLoginUserId);
		unset($this->session->tfaLoginDate);
	}

	public function triggerIfTfaConfirmationRequired(\XF\Entity\User $user, $callbackOrUrl)
	{
		if ($this->isTfaConfirmationRequired($user))
		{
			$this->setTfaSessionCheck($user);

			if ($callbackOrUrl instanceof \Closure)
			{
				$callbackOrUrl();
			}
			else
			{
				throw $this->exception($this->redirect($callbackOrUrl, ''));
			}
		}
	}

	/**
	 * @param string $redirect
	 * @param null|string $providerId
	 *
	 * @return LoginTfaResult
	 */
	public function runTfaCheck($redirect, $providerId = null)
	{
		if ($providerId === null)
		{
			$providerId = $this->request->filter('provider', 'str');
		}

		$user = $this->getTfaLoginUser();
		if (!$user)
		{
			$this->clearTfaSessionCheck();
			return LoginTfaResult::newSkipped($redirect);
		}

		/** @var \XF\Service\User\Tfa $tfaService */
		$tfaService = $this->service('XF:User\Tfa', $user);

		if (!$tfaService->isTfaAvailable())
		{
			$this->clearTfaSessionCheck();
			return LoginTfaResult::newSuccess($user, $redirect);
		}

		if (
			$this->request->isPost()
			&& $this->request->filter('confirm', 'bool')
			&& $tfaService->isProviderValid($providerId)
		)
		{
			if ($tfaService->hasTooManyTfaAttempts())
			{
				return LoginTfaResult::newError(
					\XF::phrase('your_account_has_temporarily_been_locked_due_to_failed_login_attempts')
				);
			}

			$verified = $tfaService->verify($this->request, $providerId);
			if (!$verified)
			{
				return LoginTfaResult::newError(\XF::phrase('two_step_verification_value_could_not_be_confirmed'));
			}
			else
			{
				$this->clearTfaSessionCheck();

				if ($this->filter('trust', 'bool'))
				{
					$this->setDeviceTrusted($user->user_id);
				}

				return LoginTfaResult::newSuccess($user, $redirect);
			}
		}

		$triggered = $tfaService->trigger($this->request, $providerId);

		return LoginTfaResult::newForm([
			'user' => $user,
			'providers' => $tfaService->getProviders(),
			'providerId' => $triggered['provider']->provider_id,
			'provider' => $triggered['provider'],
			'providerData' => $triggered['providerData'],
			'triggerData' => $triggered['triggerData'],
			'redirect' => $redirect
		]);
	}

	public function setDeviceTrusted($userId)
	{
		/** @var \XF\Repository\UserTfaTrusted $tfaTrustRepo */
		$tfaTrustRepo = $this->repository('XF:UserTfaTrusted');
		$key = $tfaTrustRepo->createTrustedKey($userId);

		$this->app->response()->setCookie('tfa_trust', $key, 45 * 86400, null, true);

		return $key;
	}

	public function completeLogin(\XF\Entity\User $user, $remember)
	{
		if ($user->user_id !== \XF::visitor()->user_id)
		{
			$this->session->changeUser($user);
			\XF::setVisitor($user);
		}

		$ip = $this->request->getIp();

		$this->repository('XF:SessionActivity')->clearUserActivity(0, $ip);

		$this->repository('XF:Ip')->logIp(
			$user->user_id, $ip,
			'user', $user->user_id, 'login'
		);

		if ($remember)
		{
			$this->createVisitorRememberKey();
		}
	}

	public function actionPasswordConfirm()
	{
		$redirect = $this->controller->getDynamicRedirectIfNot($this->buildLink('login'));
		$visitor = \XF::visitor();

		if (!$visitor->user_id)
		{
			return $this->redirect($redirect, '');
		}

		$this->assertPostOnly();

		/** @var \XF\Service\User\Login $loginService */
		$loginService = $this->service('XF:User\Login', $visitor->username, $this->request->getIp());
		if ($loginService->isLoginLimited())
		{
			return $this->error(\XF::phrase('your_account_has_temporarily_been_locked_due_to_failed_login_attempts'));
		}

		$password = $this->filter('password', 'str');
		if (!$loginService->validate($password, $error))
		{
			return $this->error($error);
		}

		$this->session()->passwordConfirm = \XF::$time;
		return $this->redirect($redirect, '');
	}

	public function actionKeepAlive()
	{
		$this->controller->assertPostOnly();

		// if there's no cookie, then we need to generate a new one
		if ($this->request->getCookie('csrf'))
		{
			$this->controller->assertValidCsrfToken(null, 0); // ignore time errors and allow it to be updated in all cases
		}

		$json = [
			'csrf' => $this->app['csrf.token'],
			'time' => \XF::$time,
			'user_id' => \XF::visitor()->user_id,
		];
		$view = $this->view();
		$view->setJsonParams($json);
		return $view;
	}

	public function createVisitorRememberKey()
	{
		$visitor = \XF::visitor();
		if (!$visitor->user_id)
		{
			return;
		}

		/** @var \XF\Repository\UserRemember $rememberRepo */
		$rememberRepo = $this->repository('XF:UserRemember');
		$key = $rememberRepo->createRememberRecord($visitor->user_id);
		$value = $rememberRepo->getCookieValue($visitor->user_id, $key);

		$this->app->response()->setCookie('user', $value, 365 * 86400);
	}

	public function logoutVisitor()
	{
		$this->lastActivityUpdate();
		$this->deleteVisitorRememberRecord(false);
		$this->session->logoutUser();
		$this->clearCookies();
		$this->clearSiteData();
	}

	public function lastActivityUpdate()
	{
		$visitor = \XF::visitor();
		$userId = $visitor->user_id;
		if (!$userId)
		{
			return;
		}

		$activity = $visitor->Activity;
		if (!$activity)
		{
			return;
		}

		$visitor->last_activity = $activity->view_date;
		$visitor->save();

		$activity->delete();
	}

	public function deleteVisitorRememberRecord($deleteCookie = true)
	{
		$userRemember = $this->validateVisitorRememberKey();
		if ($userRemember)
		{
			$userRemember->delete();
		}

		if ($deleteCookie)
		{
			$this->app->response()->setCookie('user', false);
		}
	}

	/**
	 * @return null|\XF\Entity\UserRemember
	 */
	public function validateVisitorRememberKey()
	{
		$rememberCookie = $this->request->getCookie('user');
		if (!$rememberCookie)
		{
			return null;
		}

		/** @var \XF\Repository\UserRemember $rememberRepo */
		$rememberRepo = $this->repository('XF:UserRemember');
		if ($rememberRepo->validateByCookieValue($rememberCookie, $remember))
		{
			return $remember;
		}
		else
		{
			return null;
		}
	}

	protected function clearCookieSkipList()
	{
		return ['notice_dismiss', 'push_notice_dismiss', 'session', 'tfa_trust'];
	}

	public function clearCookies()
	{
		$skip = $this->clearCookieSkipList();
		$response = $this->app->response();

		foreach ($this->request->getCookies() AS $cookie => $null)
		{
			if (in_array($cookie, $skip))
			{
				continue;
			}

			$response->setCookie($cookie, false);
		}
	}

	public function clearSiteData()
	{
		$response = $this->app->response();
		$response->header('Clear-Site-Data', '"cache"');
	}

	public function handleVisitorPasswordChange()
	{
		$visitor = \XF::visitor();
		if (!$visitor->user_id)
		{
			return;
		}

		/** @var \XF\Repository\UserRemember $rememberRepo */
		$rememberRepo = $this->repository('XF:UserRemember');

		$userRemember = $this->validateVisitorRememberKey();

		$rememberRepo->clearUserRememberRecords($visitor->user_id);

		if ($userRemember)
		{
			// had a remember key before which has been invalidated, so give another one
			$this->createVisitorRememberKey();
		}

		// this will reset the necessary details in the session (such as password date)
		$this->session->changeUser($visitor);
	}
}