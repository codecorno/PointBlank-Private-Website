<?php

namespace XF\Service\User;

class PasswordChange extends \XF\Service\AbstractService
{
	/**
	 * @var \XF\Entity\User
	 */
	protected $user;

	/**
	 * @var \XF\Entity\UserAuth
	 */
	protected $userAuth;

	protected $logIp = true;

	protected $notify = true;

	protected $invalidateRememberKeys = true;

	public function __construct(\XF\App $app, \XF\Entity\User $user, $newPassword)
	{
		parent::__construct($app);

		$this->user = $user;
		$this->userAuth = $user->getRelationOrDefault('Auth', false);

		$this->setPassword($newPassword);
	}

	public function setPassword($newPassword)
	{
		$this->userAuth->setPassword($newPassword);
	}

	public function getUser()
	{
		return $this->user;
	}

	public function setLogIp($logIp)
	{
		$this->logIp = $logIp;
	}

	public function setNotify($notify)
	{
		$this->notify = $notify;
	}

	public function setInvalidateRememberKeys($invalidate)
	{
		$this->invalidateRememberKeys = $invalidate;
	}

	public function isAdminChange()
	{
		$this->setLogIp(false);
		$this->setNotify(false);
	}

	public function isValid(&$error)
	{
		$this->userAuth->preSave();

		$errors = $this->userAuth->getErrors();
		if (empty($errors))
		{
			return true;
		}
		else
		{
			$error = reset($errors);
			return false;
		}
	}

	public function save()
	{
		if (!$this->isValid($error))
		{
			throw new \LogicException("Password change can't be saved, not valid: $error");
		}

		$saved = $this->userAuth->save(false);

		if ($saved)
		{
			$this->onPasswordChange();
		}

		return $saved;
	}

	protected function onPasswordChange()
	{
		if ($this->invalidateRememberKeys)
		{
			$this->repository('XF:UserRemember')->clearUserRememberRecords($this->user->user_id);
		}

		if ($this->notify)
		{
			$this->sendPasswordChangedNotice();
		}

		if ($this->logIp)
		{
			$ip = ($this->logIp === true ? $this->app->request()->getIp() : $this->logIp);
			$this->writeIpLog($ip);
		}
	}

	protected function sendPasswordChangedNotice()
	{
		$user = $this->user;

		$mail = $this->app->mailer()->newMail();
		$mail->setToUser($user)
			->setTemplate('password_changed', [
				'user' => $user,
				'ip' => ($this->logIp === true ? $this->app->request()->getIp() : $this->logIp)
			]);

		$mail->send();
	}

	protected function writeIpLog($ip)
	{
		$user = $this->user;

		/** @var \XF\Repository\Ip $ipRepo */
		$ipRepo = $this->repository('XF:Ip');
		$ipRepo->logIp($user->user_id, $ip, 'user', $user->user_id, 'password_change');
	}
}