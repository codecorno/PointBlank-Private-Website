<?php

namespace XF\Tfa;

use XF\Entity\TfaProvider;
use XF\PrintableException;

class Authy extends AbstractProvider
{
	public function renderOptions(TfaProvider $provider)
	{
		$params = [
			'provider' => $provider
		];
		return \XF::app()->templater()->renderTemplate('admin:two_step_config_authy', $params);
	}

	public function isUsable()
	{
		return !empty($this->getProvider()->options['authy_api_key']);
	}

	public function requiresConfig()
	{
		return true;
	}

	public function handleConfig(
		\XF\Mvc\Controller $controller, \XF\Entity\TfaProvider $provider, \XF\Entity\User $user, array &$config
	)
	{
		if ($controller->filter('setup', 'bool'))
		{
			$email = $controller->filter('email', 'str');
			if (!$email)
			{
				$email = \XF::visitor()->email;
			}
			if (!$email)
			{
				throw $controller->exception(
					$controller->error(\XF::phrase('please_enter_valid_email'))
				);
			}

			$dialCode = $controller->filter('dial_code', 'uint');
			$intlNumb = $controller->filter('intl_numb', 'str');
			$number = preg_replace('/^\+' . $dialCode .'/', '', $intlNumb);

			$authy = $this->getAuthy();
			$authyUser = $authy->registerUser($email, $number, $dialCode);

			if (!$authyUser->ok())
			{
				throw $controller->exception(
					$controller->error($authyUser->errors())
				);
			}

			$config['authy_id'] = $authyUser->id();

			return null;
		}
		else
		{
			$viewParams = [
				'provider' => $provider,
				'config' => $config
			];
			return $controller->view('XF:Account\TwoStepAuthyConfig', 'account_two_step_authy_config', $viewParams);
		}
	}

	public function generateInitialData(\XF\Entity\User $user, array $config = [])
	{
		return $config;
	}

	public function trigger($context, \XF\Entity\User $user, array &$config, \XF\Http\Request $request)
	{
		return [];
	}

	public function render($context, \XF\Entity\User $user, array $config, array $triggerData)
	{
		$uuid = null;

		if ($context == 'login')
		{
			$authy = $this->getAuthy();
			$authyRequest = $authy->createApprovalRequest(
				$config['authy_id'],
				strval(\XF::phrase('approve_login_request_for_x_at_y', [
					'username' => $user->username,
					'boardTitle' => \XF::options()->boardTitle
				])),
				[
					'details' => [
						'user_id' => $user->user_id,
						'username' => $user->username
					]
				]
			);

			if (!$authyRequest->ok())
			{
				$errorJson = json_encode($authyRequest->errors());
				$errors = json_decode($errorJson, true);
				throw new PrintableException($errors);
			}

			$approvalRequest = $authyRequest->bodyvar('approval_request');
			$uuid = $approvalRequest->uuid;
		}

		$params = [
			'config' => $config,
			'context' => $context,
			'uuid' => $uuid
		];
		return \XF::app()->templater()->renderTemplate('public:two_step_authy', $params);
	}

	public function verify($context, \XF\Entity\User $user, array &$config, \XF\Http\Request $request)
	{
		$this->bypassFailedAttemptLog = false;

		if ($context == 'setup')
		{
			$code = $request->filter('code', 'str');
			$code = preg_replace('/[^0-9]/', '', $code);
			if (!$code)
			{
				return false;
			}

			$authy = $this->getAuthy();
			$authyVerification = $authy->verifyToken($config['authy_id'], $code);

			if (!$authyVerification->ok())
			{
				return false;
			}
		}
		else
		{
			$uuid = $request->filter('uuid', 'str');
			if (!$uuid)
			{
				throw new PrintableException('authy_no_uuid');
			}

			$authy = $this->getAuthy();
			$authyApprovalCheck = $authy->getApprovalRequest($uuid);

			if (!$authyApprovalCheck->ok())
			{
				throw new PrintableException('authy_no_approval_request');
			}

			$approvalRequest = $authyApprovalCheck->bodyvar('approval_request');

			if ($approvalRequest->status == 'pending')
			{
				$this->bypassFailedAttemptLog = true;
				return false;
			}
			else if ($approvalRequest->status == 'denied')
			{
				throw new PrintableException('authy_denied');
			}
		}

		return true;
	}

	/**
	 * @return \Authy\AuthyApi
	 */
	protected function getAuthy()
	{
		$provider = $this->getProvider();
		return new \Authy\AuthyApi($provider->options['authy_api_key']);
	}
}