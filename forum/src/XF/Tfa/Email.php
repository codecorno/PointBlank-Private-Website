<?php

namespace XF\Tfa;

class Email extends AbstractProvider
{
	public function generateInitialData(\XF\Entity\User $user, array $config = [])
	{
		return [];
	}

	public function trigger($context, \XF\Entity\User $user, array &$config, \XF\Http\Request $request)
	{
		$length = 6;

		$random = \XF::generateRandomString(4, true);
		$code = (
				((ord($random[0]) & 0x7f) << 24 ) |
				((ord($random[1]) & 0xff) << 16 ) |
				((ord($random[2]) & 0xff) << 8 ) |
				(ord($random[3]) & 0xff)
			) % pow(10, $length);
		$code = str_pad($code, $length, '0', STR_PAD_LEFT);

		$config['code'] = $code;
		$config['codeGenerated'] = time();

		$ip = $request->getIp();

		\XF::mailer()->newMail()
			->setToUser($user)
			->setTemplate('two_step_login_email', [
				'user' => $user,
				'ip' => $ip,
				'code' => $code
			])
			->send();

		return [];
	}

	public function render($context, \XF\Entity\User $user, array $config, array $triggerData)
	{
		$params = [
			'email' => $user->email,
			'config' => $config,
			'context' => $context
		];
		return \XF::app()->templater()->renderTemplate('public:two_step_email', $params);
	}

	public function verify($context, \XF\Entity\User $user, array &$config, \XF\Http\Request $request)
	{
		if (empty($config['code']) || empty($config['codeGenerated']))
		{
			return false;
		}

		if (time() - $config['codeGenerated'] > 900)
		{
			return false;
		}

		$code = $request->filter('code', 'str');
		$code = preg_replace('/[^0-9]/', '', $code);

		if (!\XF\Util\Php::hashEquals($config['code'], $code))
		{
			return false;
		}

		unset($config['code']);
		unset($config['codeGenerated']);

		return true;
	}

	public function meetsRequirements(\XF\Entity\User $user, &$error)
	{
		if (!$user->email || $user->user_state != 'valid')
		{
			$error = \XF::phrase('you_must_have_valid_email_account_confirmed');
			return false;
		}

		return true;
	}
}