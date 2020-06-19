<?php

namespace XF\Admin\Controller;

use XF\Mvc\FormAction;
use XF\Mvc\ParameterBag;

class UserUpgrade extends AbstractController
{
	protected function preDispatchController($action, ParameterBag $params)
	{
		$this->assertAdminPermission('userUpgrade');
	}

	public function actionIndex()
	{
		$upgradeRepo = $this->getUserUpgradeRepo();
		$upgrades = $upgradeRepo->findUserUpgradesForList();

		$activeFinder = $upgradeRepo->findActiveUserUpgradesForList();
		$activeUpgrades = $activeFinder->fetch(5);

		$expiredFinder = $upgradeRepo->findExpiredUserUpgradesForList();
		$expiredUpgrades = $expiredFinder->fetch(5);

		$viewParams = [
			'upgrades' => $upgrades->fetch(),
			'activeUpgrades' => $activeUpgrades,
			'totalActiveUpgrades' => $activeFinder->total(),
			'expiredUpgrades' => $expiredUpgrades,
			'totalExpiredUpgrades' => $expiredFinder->total()
		];
		return $this->view('XF:UserUpgrade\Listing', 'user_upgrade_list', $viewParams);
	}

	public function upgradeAddEdit(\XF\Entity\UserUpgrade $upgrade)
	{
		$paymentRepo = $this->repository('XF:Payment');
		$paymentProfiles = $paymentRepo->findPaymentProfilesForList()->fetch();

		$upgradeRepo = $this->repository('XF:UserUpgrade');
		$upgrades = $upgradeRepo->getUpgradeTitlePairs();
		unset($upgrades[$upgrade->user_upgrade_id]);

		$viewParams = [
			'upgrade' => $upgrade,
			'upgrades' => $upgrades,
			'profiles' => $paymentProfiles,
			'totalProfiles' => $paymentProfiles->count(),
			'userGroups' => $this->repository('XF:UserGroup')->getUserGroupTitlePairs()
		];
		return $this->view('XF:UserUpgrade\Edit', 'user_upgrade_edit', $viewParams);
	}

	public function actionEdit(ParameterBag $params)
	{
		$upgrade = $this->assertUpgradeExists($params->user_upgrade_id);
		return $this->upgradeAddEdit($upgrade);
	}

	public function actionAdd()
	{
		$paymentRepo = $this->repository('XF:Payment');
		if (!$paymentRepo->findPaymentProfilesForList()->total())
		{
			throw $this->exception($this->error(\XF::phrase('please_create_at_least_one_payment_profile_before_continuing')));
		}

		/** @var \XF\Entity\UserUpgrade $upgrade */
		$upgrade = $this->em()->create('XF:UserUpgrade');
		return $this->upgradeAddEdit($upgrade);
	}

	protected function upgradeSaveProcess(\XF\Entity\UserUpgrade $upgrade)
	{
		$form = $this->formAction();

		$input = $this->filter([
			'title' => 'str',
			'description' => 'str',
			'display_order' => 'uint',
			'extra_group_ids' => 'array-uint',
			'recurring' => 'bool',
			'cost_amount' => 'unum',
			'cost_currency' => 'str',
			'length_amount' => 'uint',
			'length_unit' => 'string',
			'payment_profile_ids' => 'array-uint',
			'disabled_upgrade_ids' => 'array-uint',
			'can_purchase' => 'bool'
		]);
		$form->basicEntitySave($upgrade, $input);

		$form->setup(function() use($upgrade)
		{
			if ($this->filter('length_type', 'str') == 'permanent')
			{
				$upgrade->length_amount = 0;
				$upgrade->length_unit = '';
			}
		});

		return $form;
	}

	public function actionSave(ParameterBag $params)
	{
		$this->assertPostOnly();

		if ($params->user_upgrade_id)
		{
			$upgrade = $this->assertUpgradeExists($params->user_upgrade_id);
		}
		else
		{
			$upgrade = $this->em()->create('XF:UserUpgrade');
		}
		$this->upgradeSaveProcess($upgrade)->run();

		return $this->redirect($this->buildLink('user-upgrades') . $this->buildLinkHash($upgrade->user_upgrade_id));
	}

	public function actionDelete(ParameterBag $params)
	{
		$upgrade = $this->assertUpgradeExists($params->user_upgrade_id);

		/** @var \XF\ControllerPlugin\Delete $plugin */
		$plugin = $this->plugin('XF:Delete');
		return $plugin->actionDelete(
			$upgrade,
			$this->buildLink('user-upgrades/delete', $upgrade),
			$this->buildLink('user-upgrades/edit', $upgrade),
			$this->buildLink('user-upgrades'),
			$upgrade->title
		);
	}

	public function actionToggle()
	{
		/** @var \XF\ControllerPlugin\Toggle $plugin */
		$plugin = $this->plugin('XF:Toggle');
		return $plugin->actionToggle('XF:UserUpgrade', 'can_purchase');
	}

	public function actionManual(ParameterBag $params)
	{
		$upgrade = $this->assertUpgradeExists($params->user_upgrade_id);

		if ($this->isPost())
		{
			$username = $this->filter('username', 'str');
			$user = $this->em()->findOne('XF:User', ['username' => $username]);
			if (!$user)
			{
				return $this->error(\XF::phrase('requested_user_not_found'));
			}

			$endDate = $this->filter('end_type', 'str') == 'date'
				? $this->filter('end_date', 'datetime')
				: 0;

			/** @var \XF\Service\User\Upgrade $upgradeService */
			$upgradeService = $this->service('XF:User\Upgrade', $upgrade, $user);
			$upgradeService->setEndDate($endDate);
			$upgradeService->ignoreUnpurchasable(true);
			$upgradeService->upgrade();

			return $this->redirect($this->buildLink('user-upgrades'));
		}
		else
		{
			if ($upgrade->length_unit)
			{
				$endDate = strtotime('+' . $upgrade->length_amount . ' ' . $upgrade->length_unit);
			}
			else
			{
				$endDate = false;
			}

			$viewParams = [
				'endDate' => $endDate,
				'upgrade' => $upgrade
			];
			return $this->view('XF:UserUpgrade\Manual', 'user_upgrade_manual', $viewParams);
		}
	}

	protected function prepareActiveExpiredList(\XF\Mvc\Entity\Finder $finder, ParameterBag $params, array &$linkParams)
	{
		$userUpgrade = null;
		if ($params->user_upgrade_id)
		{
			$userUpgrade = $this->assertUpgradeExists($params->user_upgrade_id);
			$finder->where('user_upgrade_id', $params->user_upgrade_id);
		}

		$order = $this->filter('order', 'str');
		if ($order)
		{
			if ($order == 'username')
			{
				$finder->order('User.username', 'ASC');
				$linkParams['order'] = $order;
				$linkParams['direction'] = '';
			}
			else
			{
				$direction = $this->filter('direction', 'str');
				$finder->order($order, $direction);
				$linkParams['order'] = $order;
				$linkParams['direction'] = $direction;
			}
		}

		if ($username = $this->filter('username', 'str'))
		{
			$user = $this->em()->findOne('XF:User', ['username' => $username]);
			if (!$user)
			{
				throw $this->exception($this->error(\XF::phrase('requested_user_not_found')));
			}
			$finder->where('user_id', $user->user_id);
			$linkParams['username'] = $username;
		}

		return $userUpgrade;
	}

	public function actionActive(ParameterBag $params)
	{
		$userUpgradeRepo = $this->getUserUpgradeRepo();
		$activeFinder = $userUpgradeRepo->findActiveUserUpgradesForList();

		$linkParams = [];
		$userUpgrade = $this->prepareActiveExpiredList($activeFinder, $params, $linkParams);

		$page = $this->filterPage();
		$perPage = 20;

		$activeFinder->limitByPage($page, $perPage);
		$totalActive = $activeFinder->total();

		$this->assertValidPage($page, $perPage, $totalActive, 'user-upgrades/active', $userUpgrade);

		if ($this->isPost())
		{
			// Redirect to GET
			return $this->redirect($this->buildLink('user-upgrades/active', $userUpgrade, $linkParams));
		}

		$viewParams = [
			'page' => $page,
			'perPage' => $perPage,
			'linkParams' => $linkParams,
			'userUpgrade' => $userUpgrade,
			'totalActive' => $totalActive,
			'activeUpgrades' => $activeFinder->fetch()
		];
		return $this->view('XF:UserUpgrade\Active', 'user_upgrade_active_list', $viewParams);
	}

	public function actionExpired(ParameterBag $params)
	{
		$userUpgradeRepo = $this->getUserUpgradeRepo();
		$expiredFinder = $userUpgradeRepo->findExpiredUserUpgradesForList()
			->with('Upgrade', true);

		$linkParams = [];
		$userUpgrade = $this->prepareActiveExpiredList($expiredFinder, $params, $linkParams);

		$page = $this->filterPage();
		$perPage = 20;

		$expiredFinder->limitByPage($page, $perPage);
		$totalExpired = $expiredFinder->total();

		$this->assertValidPage($page, $perPage, $totalExpired, 'user-upgrades/expired', $userUpgrade);

		if ($this->isPost())
		{
			// Redirect to GET
			return $this->redirect($this->buildLink('user-upgrades/expired', $userUpgrade, $linkParams));
		}

		$viewParams = [
			'page' => $page,
			'perPage' => $perPage,
			'linkParams' => $linkParams,
			'userUpgrade' => $userUpgrade,
			'totalExpired' => $totalExpired,
			'expiredUpgrades' => $expiredFinder->fetch()
		];
		return $this->view('XF:UserUpgrade\Expired', 'user_upgrade_expired_list', $viewParams);
	}

	public function actionEditActive()
	{
		$activeUpgrade = $this->assertRecordExists(
			'XF:UserUpgradeActive', $this->filter('user_upgrade_record_id', 'uint'), ['Upgrade', 'User']
		);

		if ($this->isPost())
		{
			$upgradeService = $this->service('XF:User\Upgrade', $activeUpgrade->Upgrade, $activeUpgrade->User);
			$upgradeService->ignoreUnpurchasable(true);
			$endType = $this->filter('end_type', 'str');
			if ($endType == 'permanent')
			{
				$upgradeService->setEndDate(0);
			}
			else
			{
				$upgradeService->setEndDate($this->filter('end_date', 'datetime'));
			}
			$upgradeService->upgrade();

			return $this->redirect($this->buildLink('user-upgrades/active'));
		}
		else
		{
			$viewParams = [
				'activeUpgrade' => $activeUpgrade
			];
			return $this->view('XF:UserUpgrade\EditActive', 'user_upgrade_active_edit', $viewParams);
		}
	}

	public function actionDowngrade()
	{
		$activeUpgrade = $this->assertRecordExists(
			'XF:UserUpgradeActive', $this->filter('user_upgrade_record_id', 'uint'), ['Upgrade', 'User']
		);

		if ($this->isPost())
		{
			/** @var \XF\Service\User\Downgrade $downgradeService */
			$downgradeService = $this->service('XF:User\Downgrade', $activeUpgrade->Upgrade, $activeUpgrade->User);
			$downgradeService->setSendAlert(false);
			$downgradeService->downgrade();

			return $this->redirect($this->buildLink('user-upgrades/active'));
		}
		else
		{
			$viewParams = [
				'activeUpgrade' => $activeUpgrade
			];
			return $this->view('XF:UserUpgrade\Downgrade', 'user_upgrade_active_downgrade', $viewParams);
		}
	}

	/**
	 * @param string $id
	 * @param array|string|null $with
	 * @param null|string $phraseKey
	 *
	 * @return \XF\Entity\UserUpgrade
	 */
	protected function assertUpgradeExists($id, $with = null, $phraseKey = null)
	{
		return $this->assertRecordExists('XF:UserUpgrade', $id, $with, $phraseKey);
	}

	/**
	 * @return \XF\Repository\UserUpgrade
	 */
	protected function getUserUpgradeRepo()
	{
		return $this->repository('XF:UserUpgrade');
	}
}