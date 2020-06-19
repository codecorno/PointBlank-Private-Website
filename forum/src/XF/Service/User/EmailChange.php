<?php

namespace XF\Service\User;

use XF\Entity\User;

class EmailChange extends \XF\Service\AbstractService
{
	/**
	 * @var User
	 */
	protected $user;

	protected $changed = false;
	protected $oldEmail;
	protected $oldState;

	protected $logIp = true;

	protected $confirmationSent = false;
	protected $notificationSent = false;

	protected $confirmationRequired = false;

	public function __construct(\XF\App $app, User $user, $newEmail)
	{
		parent::__construct($app);

		$this->user = $user;
		$this->oldState = $user->user_state;

		$this->changeEmail($newEmail);
	}

	protected function changeEmail($email)
	{
		if ($this->user->email !== $email)
		{
			$this->changed = true;
			$this->oldEmail = $this->user->email;

			$this->user->email = $email;
		}
	}

	public function getUser()
	{
		return $this->user;
	}

	public function logIp($logIp)
	{
		$this->logIp = $logIp;
	}

	public function canChangeEmail(&$error = null)
	{
		$cutOff = \XF::$time - 3600;
		$changes = $this->repository('XF:ChangeLog')->countChangeLogsSince(
			'user', $this->user->user_id, 'email', $cutOff
		);
		return ($changes < 3);
	}

	public function isValid(&$error)
	{
		$errors = $this->user->getErrors();
		if (empty($errors['email']))
		{
			return true;
		}
		else
		{
			$error = $errors['email'];
			return false;
		}
	}

	public function save()
	{
		if (!$this->isValid($error))
		{
			throw new \LogicException("Email change can't be saved, not valid: $error");
		}

		if ($this->isConfirmationRequired())
		{
			$this->changeUserStateForConfirmation();
		}
		else if ($this->user->user_state == 'email_bounce')
		{
			$this->user->user_state = 'valid';
		}

		$saved = $this->user->save(false);

		if ($saved && $this->changed)
		{
			$this->onEmailChange();
		}

		return $saved;
	}

	public function getConfirmationRequired()
	{
		return $this->confirmationRequired;
	}

	protected function isConfirmationRequired()
	{
		if (!$this->changed || !$this->app->options()->registrationSetup['emailConfirmation'])
		{
			return false;
		}

		$user = $this->user;
		if ($user->is_moderator || $user->is_admin || $user->is_staff)
		{
			return false;
		}

		return true;
	}

	protected function changeUserStateForConfirmation()
	{
		switch ($this->user->user_state)
		{
			case 'moderated';
				if ($this->app->options()->registrationSetup['moderation'])
				{
					// only push them back to this if confirmation will return to this state
					$this->user->user_state = 'email_confirm';
					$this->confirmationRequired = true;
				}
				break;

			case 'valid':
			case 'email_bounce':
				$this->user->user_state = 'email_confirm_edit';
				$this->confirmationRequired = true;
				break;
		}
	}

	protected function onEmailChange()
	{
		if ($this->user->isAwaitingEmailConfirmation())
		{
			$this->sendEmailConfirmation();
		}

		if ($this->oldState == 'valid' && $this->oldEmail && $this->user->email)
		{
			$this->sendEmailChangedNotice();
		}

		if ($this->logIp)
		{
			$ip = ($this->logIp === true ? $this->app->request()->getIp() : $this->logIp);
			$this->writeIpLog($ip);
		}
	}

	protected function sendEmailConfirmation()
	{
		/** @var \XF\Service\User\EmailConfirmation $emailConfirmation */
		$emailConfirmation = $this->service('XF:User\EmailConfirmation', $this->user);
		$emailConfirmation->triggerConfirmation();

		$this->confirmationSent = true;
	}

	public function wasConfirmationSent()
	{
		return $this->confirmationSent;
	}

	protected function sendEmailChangedNotice()
	{
		$mail = $this->app->mailer()->newMail();
		$mail->setToUser($this->user)
			->setTo($this->oldEmail, $this->user->username)
			->setTemplate('email_changed', [
				'newEmail' => $this->user->email,
				'user' => $this->user,
				'ip' => ($this->logIp === true ? $this->app->request()->getIp() : $this->logIp)
			]);

		$mail->send();

		$this->notificationSent = true;
	}

	public function wasChangeNotificationSent()
	{
		return $this->notificationSent;
	}

	protected function writeIpLog($ip)
	{
		$user = $this->user;

		/** @var \XF\Repository\Ip $ipRepo */
		$ipRepo = $this->repository('XF:Ip');
		$ipRepo->logIp($user->user_id, $ip, 'user', $user->user_id, 'email_change');
	}
}
