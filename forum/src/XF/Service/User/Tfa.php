<?php

namespace XF\Service\User;

use XF\Entity\User;

class Tfa extends \XF\Service\AbstractService
{
	/**
	 * @var User
	 */
	protected $user;

	/**
	 * @var \XF\Repository\Tfa
	 */
	protected $tfaRepo;

	/**
	 * @var \XF\Entity\TfaProvider[]
	 */
	protected $providers;

	protected $recordAttempts = true;

	public function __construct(\XF\App $app, User $user)
	{
		parent::__construct($app);

		$this->user = $user;
		$this->tfaRepo = $this->repository('XF:Tfa');
		$this->providers = $this->tfaRepo->getAvailableProvidersForUser($user->user_id);
	}

	public function isTfaAvailable()
	{
		return $this->providers ? true : false;
	}

	public function isProviderValid($providerId)
	{
		return $providerId && isset($this->providers[$providerId]);
	}

	public function setRecordAttempts($value)
	{
		$this->recordAttempts = (bool)$value;
	}

	public function getRecordAttempts()
	{
		return $this->recordAttempts;
	}

	/**
	 * @return \XF\Entity\TfaProvider[]
	 */
	public function getProviders()
	{
		return $this->providers;
	}

	public function hasTooManyTfaAttempts()
	{
		$limits = $this->getAttemptLimits();
		$userId = $this->user->user_id;

		/** @var \XF\Repository\TfaAttempt $attemptRepo */
		$attemptRepo = $this->repository('XF:TfaAttempt');

		foreach ($limits AS $limit)
		{
			$cutOff = \XF::$time - $limit['time'];
			$count = $limit['count'];

			if ($attemptRepo->countTfaAttemptsSince($cutOff, $userId) >= $count)
			{
				return true;
			}
		}

		return false;
	}

	public function getAttemptLimits()
	{
		return [
			['time' => 60, 'count' => 4],
			['time' => 60 * 5, 'count' => 8],
		];
	}

	public function trigger(\XF\Http\Request $request, $providerId = null)
	{
		if ($providerId && isset($this->providers[$providerId]))
		{
			$provider = $this->providers[$providerId];
		}
		else
		{
			$provider = reset($this->providers);
		}

		$providerData = $provider->getUserProviderConfig($this->user->user_id);

		/** @var \XF\TFA\AbstractProvider $handler */
		$handler = $provider->handler;
		$triggerData = $handler->trigger('login', $this->user, $providerData, $request);

		/** @var \XF\Repository\Tfa $tfaRepo */
		$tfaRepo = $this->repository('XF:Tfa');
		$tfaRepo->updateUserTfaData($this->user, $provider, $providerData, false);

		return [
			'provider' => $provider,
			'providerData' => $providerData,
			'triggerData' => $triggerData
		];
	}

	public function verify(\XF\Http\Request $request, $providerId)
	{
		$provider = $this->providers[$providerId];
		$providerData = $provider->getUserProviderConfig($this->user->user_id);

		/** @var \XF\Tfa\AbstractProvider $handler */
		$handler = $provider->handler;

		if (!$handler->verify('login', $this->user, $providerData, $request))
		{
			$bypassLogging = $handler->getBypassFailedAttemptLog();

			if (!$bypassLogging)
			{
				$this->recordFailedAttempt();
			}

			return false;
		}

		$this->tfaRepo->updateUserTfaData($this->user, $provider, $providerData, true);
		$this->clearFailedAttempts();

		return true;
	}

	protected function recordFailedAttempt()
	{
		if (!$this->recordAttempts)
		{
			return;
		}

		/** @var \XF\Repository\TfaAttempt $attemptRepo */
		$attemptRepo = $this->repository('XF:TfaAttempt');
		$attemptRepo->logFailedTfaAttempt($this->user->user_id);
	}

	protected function clearFailedAttempts()
	{
		if (!$this->recordAttempts)
		{
			return;
		}

		/** @var \XF\Repository\TfaAttempt $attemptRepo */
		$attemptRepo = $this->repository('XF:TfaAttempt');
		$attemptRepo->clearTfaAttempts($this->user->user_id);
	}
}