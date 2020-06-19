<?php

namespace XF\Service\User;

use XF\Entity\User;

abstract class AbstractConfirmationService extends \XF\Service\AbstractService
{
	/**
	 * @var User
	 */
	protected $user;

	/**
	 * @var \XF\Entity\UserConfirmation
	 */
	protected $confirmation;

	abstract public function getType();

	public function __construct(\XF\App $app, User $user)
	{
		parent::__construct($app);

		$this->user = $user;

		/** @var \XF\Repository\UserConfirmation $confirmationRepo */
		$confirmationRepo = $this->repository('XF:UserConfirmation');
		$this->confirmation = $confirmationRepo->getConfirmationRecordOrDefault($user, $this->getType());
	}

	public function getUser()
	{
		return $this->user;
	}

	public function getConfirmationRecord()
	{
		return $this->confirmation;
	}

	public function canTriggerConfirmation(&$error = null)
	{
		return true;
	}

	public function needsCaptcha()
	{
		// require a captcha if re-requesting confirmation within 60 minutes to prevent abuse
		return (
			$this->confirmation->exists()
			&& $this->confirmation->confirmation_date >= \XF::$time - 3600
		);
	}

	public function triggerConfirmation()
	{
		if ($this->user->email !== '')
		{
			$this->confirmation->regenerateKey();
			$this->confirmation->save();

			$this->sendConfirmationEmail();
		}
	}

	protected function getEmailTemplateName()
	{
		return 'user_' . $this->getType() . '_confirmation';
	}

	protected function getEmailTemplateParams()
	{
		return [
			'confirmation' => $this->confirmation,
			'user' => $this->user
		];
	}

	protected function sendConfirmationEmail()
	{
		$mail = $this->app->mailer()->newMail();
		$mail->setToUser($this->user)
			->setTemplate($this->getEmailTemplateName(), $this->getEmailTemplateParams());
		$mail->send();
	}

	public function matchesKey($key)
	{
		return ($this->confirmation->exists() && $this->confirmation->confirmation_key === $key);
	}
}