<?php

namespace XF\Service\User;

class Downgrade extends \XF\Service\AbstractService
{
	/**
	 * @var \XF\Entity\User
	 */
	protected $user;

	/**
	 * @var \XF\Entity\UserUpgrade
	 */
	protected $userUpgrade;

	/**
	 * @var \XF\Entity\UserUpgradeActive
	 */
	protected $activeUpgrade;

	/**
	 * @var \XF\Entity\UserUpgradeExpired
	 */
	protected $expiredUpgrade;

	protected $sendAlert = true;

	public function __construct(\XF\App $app, \XF\Entity\UserUpgrade $upgrade, \XF\Entity\User $user, \XF\Entity\UserUpgradeActive $active = null)
	{
		parent::__construct($app);

		$this->user = $user;
		$this->activeUpgrade = $active;
		$this->setUpgrade($upgrade);
	}

	public function getUser()
	{
		return $this->user;
	}

	protected function setUpgrade(\XF\Entity\UserUpgrade $upgrade)
	{
		$this->userUpgrade = $upgrade;
		$user = $this->user;

		if (!$this->activeUpgrade)
		{
			$activeUpgrades = $upgrade->Active;
			$this->activeUpgrade = isset($activeUpgrades[$user->user_id]) ? $activeUpgrades[$user->user_id] : null;
		}

		$this->expiredUpgrade = $this->em()->create('XF:UserUpgradeExpired');
	}

	public function getUpgrade()
	{
		return $this->userUpgrade;
	}

	public function getActiveUpgrade()
	{
		return $this->activeUpgrade;
	}

	public function getExpiredUpgrade()
	{
		return $this->expiredUpgrade;
	}

	public function setSendAlert($sendAlert)
	{
		$this->sendAlert = $sendAlert;
	}

	public function downgrade()
	{
		$user = $this->user;
		$upgrade = $this->userUpgrade;
		$active = $this->activeUpgrade;
		$expired = $this->expiredUpgrade;

		$db = $this->db();
		$db->beginTransaction();

		/** @var UserGroupChange $userGroupChange */
		$userGroupChange = $this->service('XF:User\UserGroupChange');
		$userGroupChange->removeUserGroupChange(
			$user->user_id, 'userUpgrade-' . $upgrade->user_upgrade_id
		);

		if ($active)
		{
			/** @var \XF\Repository\UserUpgrade $upgradeRepo */
			$upgradeRepo = $this->repository('XF:UserUpgrade');
			$upgradeRepo->expireActiveUpgrade($active, $expired);

			if (!$upgrade->recurring && $upgrade->can_purchase && $this->sendAlert)
			{
				/** @var \XF\Repository\UserAlert $alertRepo */
				$alertRepo = $this->app->repository('XF:UserAlert');
				$alertRepo->alert($user, $user->user_id, $user->username, 'user', $user->user_id, 'upgrade_end');
			}
		}

		$db->commit();

		return true;
	}
}