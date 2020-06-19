<?php

namespace XF\Api\Controller;

use XF\Mvc\Entity\Entity;
use XF\Mvc\ParameterBag;

/**
 * @api-group Auth
 */
class Auth extends AbstractController
{
	/**
	 * @api-desc Tests a login and password for validity. Only available to super user keys.
	 *
	 * @api-in <req> str $login The username or email address of the user to test
	 * @api-in <req> str $password The password of the user
	 * @api-in str $limit_ip The IP that should be considered to be making the request. If provided, this will be used to prevent brute force attempts.
	 *
	 * @api-out User $user If successful, the user record of the matching user
	 */
	public function actionPost()
	{
		$this->assertSuperUserKey();
		$this->assertApiScope('auth');
		$this->assertRequiredApiInput(['login', 'password']);

		$input = $this->filter([
			'login' => 'str',
			'password' => 'str',
			'limit_ip' => 'str'
		]);

		/** @var \XF\Service\User\Login $loginService */
		$loginService = $this->service('XF:User\Login', $input['login'], $input['limit_ip']);
		if ($loginService->isLoginLimited($limitType))
		{
			return $this->error(\XF::phrase('your_account_has_temporarily_been_locked_due_to_failed_login_attempts'));
		}

		$user = $loginService->validate($input['password'], $error);
		if (!$user)
		{
			return $this->error($error);
		}

		return $this->apiSuccess([
			'user' => $user->toApiResult(Entity::VERBOSITY_VERBOSE, ['full_profile' => true])
		]);
	}
}