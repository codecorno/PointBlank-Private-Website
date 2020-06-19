<?php

namespace XF\Spam\Cleaner;

use XF\Entity\User;

abstract class AbstractHandler
{
	protected $user;

	public function __construct(User $user)
	{
		$this->setUser($user);
	}

	public function setUser(User $user)
	{
		$this->user = $user;
	}

	public function canCleanUp(array $options = [])
	{
		return true;
	}

	abstract public function cleanUp(array &$log, &$error = null);

	abstract public function restore(array $log, &$error = null);
}