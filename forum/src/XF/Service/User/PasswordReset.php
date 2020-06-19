<?php

namespace XF\Service\User;

class PasswordReset extends AbstractConfirmationService
{
	protected $isAdminReset = false;

	public function getType()
	{
		return 'password';
	}

	public function setAdminReset($isAdminReset)
	{
		$this->isAdminReset = (bool)$isAdminReset;
	}

	protected function getEmailTemplateParams()
	{
		$params = parent::getEmailTemplateParams();
		$params['isAdminReset'] = $this->isAdminReset;

		return $params;
	}

	public function canTriggerConfirmation(&$error = null)
	{
		if ($timeLimit = $this->app->options()->lostPasswordTimeLimit)
		{
			if ($this->confirmation->exists())
			{
				$timeDiff = time() - $this->confirmation->confirmation_date;
				if ($timeLimit > $timeDiff)
				{
					$wait = $timeLimit - $timeDiff;
					$error = \XF::phrase('must_wait_x_seconds_before_performing_this_action', ['count' => $wait]);
					return false;
				}
			}
		}

		if ($this->user->email == '')
		{
			$error = \XF::phrase('this_account_cannot_be_confirmed_without_email_address');
			return false;
		}

		return true;
	}

	public function resetLostPassword($newPassword)
	{
		$user = $this->user;

		/** @var \XF\Entity\UserAuth $userAuth */
		$userAuth = $user->getRelationOrDefault('Auth', false);
		$userAuth->setPassword($newPassword);
		$userAuth->save();

		if ($this->confirmation->exists())
		{
			$this->confirmation->delete();
		}

		$this->repository('XF:UserRemember')->clearUserRememberRecords($user->user_id);

		$ip = $this->app->request()->getIp();
		$this->repository('XF:Ip')->logIp(
			$user->user_id, $ip, 'user', $user->user_id, 'reset_password'
		);

		if ($user->email)
		{
			$this->app->mailer()->newMail()
				->setToUser($user)
				->setTemplate('user_lost_password_reset', ['user' => $user])
				->send();
		}

		return $user;
	}
}