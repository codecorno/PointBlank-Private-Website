<?php

namespace XF\Repository;

use XF\Mvc\Entity\Finder;
use XF\Mvc\Entity\Repository;

class UserUpgrade extends Repository
{
	/**
	 * @return Finder
	 */
	public function findUserUpgradesForList()
	{
		return $this->finder('XF:UserUpgrade')
			->setDefaultOrder('display_order');
	}

	/**
	 * @return Finder
	 */
	public function findActiveUserUpgradesForList()
	{
		$finder = $this->finder('XF:UserUpgradeActive');

		$finder
			->with(['User', 'PurchaseRequest.PaymentProfile'])
			->with('Upgrade', true)
			->setDefaultOrder('start_date', 'DESC');

		return $finder;
	}

	/**
	 * @return Finder
	 */
	public function findExpiredUserUpgradesForList()
	{
		$finder = $this->finder('XF:UserUpgradeExpired');

		$finder
			->with(['User', 'PurchaseRequest.PaymentProfile'])
			->with('Upgrade', true)
			->setDefaultOrder('end_date', 'DESC');

		return $finder;
	}

	public function getFilteredUserUpgradesForList()
	{
		$visitor = \XF::visitor();

		$finder = $this->findUserUpgradesForList()
			->with('Active|'
				. $visitor->user_id
				. '.PurchaseRequest'
			);

		$purchased = [];
		$upgrades = $finder->fetch();

		if ($visitor->user_id && $upgrades->count())
		{
			/** @var \XF\Entity\UserUpgrade $upgrade */
			foreach ($upgrades AS $upgradeId => $upgrade)
			{
				if (isset($upgrade->Active[$visitor->user_id]))
				{
					// purchased
					$purchased[$upgradeId] = $upgrade;
					unset($upgrades[$upgradeId]); // can't buy again

					// remove any upgrades disabled by this
					foreach ($upgrade['disabled_upgrade_ids'] AS $disabledId)
					{
						unset($upgrades[$disabledId]);
					}
				}
				else if (!$upgrade->canPurchase())
				{
					unset($upgrades[$upgradeId]);
				}
			}
		}

		return [$upgrades, $purchased];
	}

	public function getUpgradeTitlePairs()
	{
		return $this->findUserUpgradesForList()->fetch()->pluck(function($e, $k)
		{
			return [$k, $e->title];
		});
	}

	public function getUserUpgradeCount()
	{
		return $this->finder('XF:UserUpgrade')
			->where('can_purchase', 1)
			->total();
	}

	public function rebuildUpgradeCount()
	{
		$cache = $this->getUserUpgradeCount();
		\XF::registry()->set('userUpgradeCount', $cache);
		return $cache;
	}

	public function downgradeExpiredUpgrades()
	{
		/** @var \XF\Entity\UserUpgradeActive[] $expired */
		$expired = $this->finder('XF:UserUpgradeActive')
			->with('Upgrade')
			->with('User')
			->where('end_date', '<', \XF::$time)
			->where('end_date', '>', 0)
			->order('end_date')
			->fetch(1000);

		$db = $this->db();
		$db->beginTransaction();

		foreach ($expired AS $active)
		{
			$upgrade = $active->Upgrade;

			if ($upgrade && $upgrade->recurring)
			{
				// For recurring payments give a 24 hour grace period
				if ($active->end_date + 86400 >= \XF::$time)
				{
					continue;
				}
			}

			if ($upgrade)
			{
				/** @var \XF\Service\User\Downgrade $downgradeService */
				$downgradeService = $this->app()->service('XF:User\Downgrade', $active->Upgrade, $active->User, $active);
				$downgradeService->downgrade();
			}
			else
			{
				/** @var \XF\Service\User\UserGroupChange $userGroupChange */
				$userGroupChange = $this->app()->service('XF:User\UserGroupChange');
				$userGroupChange->removeUserGroupChange(
					$active->user_id, 'userUpgrade-' . $active->user_upgrade_id
				);

				$this->expireActiveUpgrade($active);
			}
		}

		$db->commit();
	}

	public function expireActiveUpgrade(\XF\Entity\UserUpgradeActive $active, \XF\Entity\UserUpgradeExpired $expired = null)
	{
		if ($expired === null)
		{
			$expired = $this->em->create('XF:UserUpgradeExpired');
		}

		$expired->user_upgrade_record_id = $active->user_upgrade_record_id;
		$expired->user_id = $active->user_id;
		$expired->purchase_request_key = $active->purchase_request_key;
		$expired->user_upgrade_id = $active->user_upgrade_id;
		$expired->extra = $active->extra;
		$expired->start_date = $active->start_date;
		$expired->end_date = time();
		$expired->original_end_date = $active->end_date;

		// There's an edge case where this can fail if the user_upgrade_record_id is already used.
		// There's code that should prevent it from happening, but we need to just ignore that situation.
		$expired->save(false, false);

		$active->delete(true, false);
	}
}