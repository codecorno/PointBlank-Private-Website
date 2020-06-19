<?php

namespace XF\Repository;

use XF\Mvc\Entity\Finder;
use XF\Mvc\Entity\Repository;

class Tfa extends Repository
{
	/**
	 * @return Finder
	 */
	public function findProvidersForList()
	{
		$finder = $this->finder('XF:TfaProvider')
			->setDefaultOrder('priority');

		return $finder;
	}

	/**
	 * @param null|int $userId
	 * @return \XF\Entity\TfaProvider[]
	 */
	public function getValidProviderList($userId = null)
	{
		$finder = $this->finder('XF:TfaProvider')
			->where('active', 1)
			->order('priority');

		if ($userId !== null)
		{
			$finder->with('UserEntries|' . $userId);
		}

		$providers = $finder->fetch();

		foreach ($providers AS $k => $provider)
		{
			/** @var \XF\Entity\TfaProvider $provider */
			if (!$provider->isValid())
			{
				unset($providers[$k]);
			}
		}

		return $providers->toArray();
	}

	public function getAvailableProvidersForUser($userId)
	{
		$providers = $this->getValidProviderList($userId);
		foreach ($providers AS $key => $provider)
		{
			if (!$provider->isEnabled($userId))
			{
				unset($providers[$key]);
			}
		}

		return $providers;
	}

	public function userRequiresTfa(\XF\Entity\User $user)
	{
		$providers = $this->getAvailableProvidersForUser($user->user_id);
		if (!$providers)
		{
			return false;
		}

		if (count($providers) == 1)
		{
			$provider = reset($providers);
			if ($provider->provider_id == 'backup')
			{
				return false;
			}
		}

		return true;
	}

	public function isUserTfaConfirmationRequired(\XF\Entity\User $user, $trustKey = null)
	{
		if (!\XF::config('enableTfa'))
		{
			return false;
		}

		if (!$user->Option || !$user->Option->use_tfa)
		{
			return false;
		}

		if (!$this->userRequiresTfa($user))
		{
			return false;
		}

		/** @var \XF\Repository\UserTfaTrusted $tfaTrustRepo */
		$tfaTrustRepo = $this->repository('XF:UserTfaTrusted');
		if ($trustKey && $tfaTrustRepo->getTfaTrustRecord($user->user_id, $trustKey))
		{
			return false;
		}

		return true;
	}

	public function enableUserTfaProvider(\XF\Entity\User $user, \XF\Entity\TfaProvider $provider, array $config, &$backupAdded = false)
	{
		$this->db()->beginTransaction();

		$userTfa = $this->em->create('XF:UserTfa');
		$userTfa->user_id = $user->user_id;
		$userTfa->provider_id = $provider->provider_id;
		$userTfa->provider_data = $config;
		$userTfa->save();

		/** @var \XF\Entity\TfaProvider $backupProvider */
		$backupProvider = $this->em->find('XF:TfaProvider', 'backup');
		if ($backupProvider && $backupProvider->isValid() && !$backupProvider->UserEntries[$user->user_id])
		{
			/** @var \XF\Tfa\Backup $backupHandler */
			$backupHandler = $backupProvider->handler;

			$backupTfa = $this->em->create('XF:UserTfa');
			$backupTfa->user_id = $user->user_id;
			$backupTfa->provider_id = 'backup';
			$backupTfa->provider_data = $backupHandler->generateInitialData($user);
			$backupTfa->save();

			$backupAdded = true;
		}
		else
		{
			$backupAdded = false;
		}

		$this->db()->commit();

		return $userTfa;
	}

	public function updateUserTfaData(\XF\Entity\User $user, \XF\Entity\TfaProvider $provider, array $config, $updateLastUsed = true)
	{
		/** @var \XF\Entity\UserTfa $userTfa */
		$userTfa = $provider->UserEntries[$user->user_id];
		if (!$userTfa)
		{
			return false;
		}

		$userTfa->provider_data = $config;
		if ($updateLastUsed)
		{
			$userTfa->last_used_date = \XF::$time;
		}

		$userTfa->save();

		return true;
	}

	public function disableTfaForUser(\XF\Entity\User $user)
	{
		$db = $this->db();

		$db->beginTransaction();

		$db->delete('xf_user_tfa', 'user_id = ?', $user->user_id);

		if ($user->Option)
		{
			$user->Option->use_tfa = false;
			$user->Option->save();
		}

		$db->delete('xf_user_tfa_trusted', 'user_id = ?', $user->user_id);

		$db->commit();
	}
}