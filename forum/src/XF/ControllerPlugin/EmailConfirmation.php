<?php

namespace XF\ControllerPlugin;

use XF\Entity\User;

class EmailConfirmation extends AbstractPlugin
{
	public function actionResend(User $user, $confirmUrl, $options = [])
	{
		$options = array_merge([
			'returnUrl' => $this->controller->getDynamicRedirect(),
			'view' => 'XF:EmailConfirmation\Resend',
			'template' => 'public:account_confirm_resend',
			'checkCaptcha' => false,
			'extraViewParams' => []
		], $options);

		/** @var \XF\Service\User\EmailConfirmation $emailConfirmation */
		$emailConfirmation = $this->service('XF:User\EmailConfirmation', $user);

		if (!$emailConfirmation->canTriggerConfirmation($error))
		{
			return $this->error($error);
		}

		if ($options['checkCaptcha'])
		{
			$needsCaptcha = $emailConfirmation->needsCaptcha();
		}
		else
		{
			$needsCaptcha = false;
		}

		if ($this->request->isPost())
		{
			if ($needsCaptcha && !$this->controller->captchaIsValid(true))
			{
				return $this->error(\XF::phrase('did_not_complete_the_captcha_verification_properly'));
			}

			$emailConfirmation->triggerConfirmation();

			return $this->redirect(
				$options['returnUrl'], \XF::phrase('confirmation_email_has_been_resent')
			);
		}
		else
		{
			$viewParams = [
				'user' => $user,
				'confirmUrl' => $confirmUrl,
				'needsCaptcha' => $needsCaptcha
			];
			return $this->view($options['view'], $options['template'], $viewParams + $options['extraViewParams']);
		}
	}
}