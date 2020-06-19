<?php

namespace XF\Tfa;

use Base32\Base32;
use Otp\GoogleAuthenticator;
use Otp\Otp;

class Totp extends AbstractProvider
{
	public function generateInitialData(\XF\Entity\User $user, array $config = [])
	{
		$length = 16;
		$secret = substr(Base32::encode(\XF::generateRandomString($length, true)), 0, $length);

		return [
			'secret' => $secret
		];
	}

	public function trigger($context, \XF\Entity\User $user, array &$config, \XF\Http\Request $request)
	{
		return [];
	}

	public function render($context, \XF\Entity\User $user, array $config, array $triggerData)
	{
		$issuer = \XF::app()->stringFormatter()->wholeWordTrim(
			str_replace(':', '', \XF::options()->boardTitle),
			50
		);
		$user = str_replace(':', '', $user->username);

		$otpUrl = GoogleAuthenticator::getKeyUri('totp', "$issuer: $user", $config['secret'], null, [
			'issuer' => $issuer
		]);

		$params = [
			'secret' => $config['secret'],
			'otpUrl' => $otpUrl,
			'config' => $config,
			'context' => $context
		];
		return \XF::app()->templater()->renderTemplate('public:two_step_totp', $params);
	}

	public function verify($context, \XF\Entity\User $user, array &$config, \XF\Http\Request $request)
	{
		if (empty($config['secret']))
		{
			return false;
		}

		$code = $request->filter('code', 'str');
		$code = preg_replace('/[^0-9]/', '', $code);
		if (!$code)
		{
			return false;
		}

		if (!empty($config['lastCode']) && $config['lastCode'] === $code)
		{
			// prevent a replay attack: once the code has been used, don't allow it to be used in the slice again
			if (!empty($config['lastCodeTime']) && time() - $config['lastCodeTime'] < 150)
			{
				return false;
			}
		}

		$otp = new Otp();
		if (!$otp->checkTotp(Base32::decode($config['secret']), $code, 2))
		{
			return false;
		}

		$config['lastCode'] = $code;
		$config['lastCodeTime'] = time();

		return true;
	}

	public function canManage()
	{
		return true;
	}

	public function handleManage(\XF\Mvc\Controller $controller, \XF\Entity\TfaProvider $provider, \XF\Entity\User $user, array $config)
	{
		$request = $controller->request();
		$session = $controller->session();

		$newProviderData = null;
		$newTriggerData = null;
		$showSetup = false;

		if ($request->isPost())
		{
			$sessionKey = 'tfaData_totp';

			if ($request->filter('regen', 'bool'))
			{
				$newProviderData = $this->generateInitialData($user);
				$newTriggerData = $this->trigger('setup', $user, $newProviderData, $request);

				$session->set($sessionKey, $newProviderData);
				$showSetup = true;
			}
			else if ($request->filter('confirm', 'bool'))
			{
				$newProviderData = $session->get($sessionKey);
				if (!is_array($newProviderData))
				{
					return null;
				}

				if (!$this->verify('setup', $user, $newProviderData, $request))
				{
					return $controller->error(\XF::phrase('two_step_verification_value_could_not_be_confirmed'));
				}

				/** @var \XF\Repository\Tfa $tfaRepo */
				$tfaRepo = \XF::repository('XF:Tfa');
				$tfaRepo->updateUserTfaData($user, $provider, $newProviderData);

				$session->remove($sessionKey);

				return null;
			}
			else
			{
				return null;
			}
		}

		$viewParams = [
			'provider' => $provider,
			'user' => $user,
			'providerData' => $config,
			'newProviderData' => $newProviderData,
			'newTriggerData' => $newTriggerData,
			'showSetup' => $showSetup
		];
		return $controller->view(
			'XF:Account\TwoStepTotpManage', 'account_two_step_totp_manage', $viewParams
		);
	}
}