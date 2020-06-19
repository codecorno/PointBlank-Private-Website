<?php

namespace XF\Service;

use XF\Entity\User;

class Contact extends AbstractService
{
	protected $fromName = '';
	protected $fromEmail = '';
	protected $fromIp = null;

	/**
	 * @var null|User
	 */
	protected $fromUser = null;

	protected $subject = '';
	protected $message = '';

	protected $isSpam = false;

	protected $validated;

	public function setFromUser(User $user)
	{
		if (!$user->user_id)
		{
			throw new \InvalidArgumentException("Can only set a from user that's registered");
		}

		$this->fromName = $user->username;
		$this->fromEmail = $user->email;
		$this->fromUser = $user;

		return $this;
	}

	public function setEmail($email, &$error = null)
	{
		if (!$this->validateEmail($email, $error))
		{
			return false;
		}
		$this->fromEmail = $email;
		return true;
	}

	public function setFromGuest($name, $email)
	{
		$this->fromName = $name;
		$this->fromEmail = $email;
		$this->fromUser = null;

		return $this;
	}

	public function setFromIp($ip)
	{
		$this->fromIp = $ip;

		return $this;
	}

	public function setMessageDetails($subject, $message)
	{
		$this->subject = $subject;
		$this->message = $message;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getFromName()
	{
		return $this->fromName;
	}

	/**
	 * @return string
	 */
	public function getFromEmail()
	{
		return $this->fromEmail;
	}

	/**
	 * @return null|User
	 */
	public function getFromUser()
	{
		return $this->fromUser;
	}

	/**
	 * @return mixed
	 */
	public function getFromIp()
	{
		return $this->fromIp;
	}

	/**
	 * @return string
	 */
	public function getSubject()
	{
		return $this->subject;
	}

	/**
	 * @return string
	 */
	public function getMessage()
	{
		return $this->message;
	}

	public function checkForSpam()
	{
		$this->isSpam = false;

		if (!$this->fromUser || $this->fromUser->isSpamCheckRequired())
		{
			/** @var User $user */

			if ($this->fromUser)
			{
				$user = $this->fromUser;
			}
			else
			{
				$user = $this->repository('XF:User')->getGuestUser($this->fromName);
				$user->setAsSaved('email', $this->fromEmail);
			}

			$message = $this->subject . "\n" . $this->message;

			$checker = $this->app->spam()->contentChecker();
			$checker->check($user, $message, [
				'content_type' => 'contact'
			]);

			$decision = $checker->getFinalDecision();
			switch ($decision)
			{
				case 'moderated':
				case 'denied':
					$checker->logSpamTrigger('contact', null);
					$this->isSpam = true;
					break;
			}
		}
	}

	public function validate(&$errors = [])
	{
		$errors = [];

		if (!$this->validated)
		{
			if (!$this->fromUser)
			{
				if (!$this->validateEmail($this->fromEmail, $error))
				{
					$errors['email'] = $error;
				}
			}

			if (!$this->fromName || !$this->subject || !$this->message)
			{
				$errors['required'] = \XF::phrase('please_complete_required_fields');
			}

			if ($this->isSpam)
			{
				$errors['spam'] = \XF::phrase('your_content_cannot_be_submitted_try_later');
			}

			$this->validated = true;
		}

		return !count($errors);
	}

	public function send()
	{
		if (!$this->validate($errors))
		{
			throw new \XF\PrintableException($errors);
		}

		return $this->getMail()->send();
	}

	protected function validateEmail(&$email, &$error = null)
	{
		$validator = $this->app->validator('Email');
		$validator->setOption('check_typos', true);
		$email = $validator->coerceValue($email);
		if (!$validator->isValid($email))
		{
			$error = \XF::phrase('please_enter_valid_email');
			return false;
		}
		return true;
	}

	protected function getMail()
	{
		$options = $this->app->options();

		$mail = $this->app->mailer()->newMail();
		$mail->setTemplate('contact_email', [
			'user' => $this->fromUser,
			'name' => $this->fromName,
			'email' => $this->fromEmail,
			'subject' => $this->subject,
			'message' => $this->message,
			'ip' => $this->fromIp
		]);

		$toEmail = $options->contactEmailAddress ? $options->contactEmailAddress : $options->defaultEmailAddress;
		$mail->setTo($toEmail);

		$mail->setFrom($mail->getFromAddress(), $this->fromName);

		if ($options->contactEmailSenderHeader)
		{
			$mail->setSender($options->contactEmailAddress)
				->setFrom($this->fromEmail, $this->fromName);
		}
		else if ($this->fromEmail)
		{
			$mail->setReplyTo($this->fromEmail, $this->fromName);
		}

		return $mail;
	}
}