<?php

namespace XF\Purchasable;

use XF\Payment\CallbackState;

class UserUpgrade extends AbstractPurchasable
{
	public function getTitle()
	{
		return \XF::phrase('user_upgrades');
	}

	public function getPurchaseFromRequest(\XF\Http\Request $request, \XF\Entity\User $purchaser, &$error = null)
	{
		if (!$purchaser->user_id)
		{
			$error = \XF::phrase('login_required');
			return false;
		}

		$profileId = $request->filter('payment_profile_id', 'uint');
		$paymentProfile = \XF::em()->find('XF:PaymentProfile', $profileId);
		if (!$paymentProfile || !$paymentProfile->active)
		{
			$error = \XF::phrase('please_choose_valid_payment_profile_to_continue_with_your_purchase');
			return false;
		}

		$userUpgradeId = $request->filter('user_upgrade_id', 'uint');
		$userUpgrade = \XF::em()->find('XF:UserUpgrade', $userUpgradeId);
		if (!$userUpgrade || !$userUpgrade->canPurchase())
		{
			$error = \XF::phrase('this_item_cannot_be_purchased_at_moment');
			return false;
		}

		if (!in_array($profileId, $userUpgrade->payment_profile_ids))
		{
			$error = \XF::phrase('selected_payment_profile_is_not_valid_for_this_purchase');
			return false;
		}

		return $this->getPurchaseObject($paymentProfile, $userUpgrade, $purchaser);
	}

	public function getPurchasableFromExtraData(array $extraData)
	{
		$output = [
			'link' => '',
			'title' => '',
			'purchasable' => null
		];
		$userUpgrade = \XF::em()->find('XF:UserUpgrade', $extraData['user_upgrade_id']);
		if ($userUpgrade)
		{
			$output['link'] = \XF::app()->router('admin')->buildLink('user-upgrades/edit', $userUpgrade);
			$output['title'] = $userUpgrade->title;
			$output['purchasable'] = $userUpgrade;
		}
		return $output;
	}

	public function getPurchaseFromExtraData(array $extraData, \XF\Entity\PaymentProfile $paymentProfile, \XF\Entity\User $purchaser, &$error = null)
	{
		$userUpgrade = $this->getPurchasableFromExtraData($extraData);
		if (!$userUpgrade['purchasable'] || !$userUpgrade['purchasable']->canPurchase())
		{
			$error = \XF::phrase('this_item_cannot_be_purchased_at_moment');
			return false;
		}

		if (!in_array($paymentProfile->payment_profile_id, $userUpgrade['purchasable']->payment_profile_ids))
		{
			$error = \XF::phrase('selected_payment_profile_is_not_valid_for_this_purchase');
			return false;
		}

		return $this->getPurchaseObject($paymentProfile, $userUpgrade['purchasable'], $purchaser);
	}

	/**
	 * @param \XF\Entity\PaymentProfile $paymentProfile
	 * @param \XF\Entity\UserUpgrade $purchasable
	 * @param \XF\Entity\User $purchaser
	 *
	 * @return Purchase
	 */
	public function getPurchaseObject(\XF\Entity\PaymentProfile $paymentProfile, $purchasable, \XF\Entity\User $purchaser)
	{
		$purchase = new Purchase();

		$purchase->title = \XF::phrase('account_upgrade') . ': ' . $purchasable->title . ' (' . $purchaser->username . ')';
		$purchase->description = $purchasable->description;
		$purchase->cost = $purchasable->cost_amount;
		$purchase->currency = $purchasable->cost_currency;
		$purchase->recurring = ($purchasable->recurring && $purchasable->length_unit);
		$purchase->lengthAmount = $purchasable->length_amount;
		$purchase->lengthUnit = $purchasable->length_unit;
		$purchase->purchaser = $purchaser;
		$purchase->paymentProfile = $paymentProfile;
		$purchase->purchasableTypeId = $this->purchasableTypeId;
		$purchase->purchasableId = $purchasable->user_upgrade_id;
		$purchase->purchasableTitle = $purchasable->title;
		$purchase->extraData = [
			'user_upgrade_id' => $purchasable->user_upgrade_id
		];

		$router = \XF::app()->router('public');

		$purchase->returnUrl = $router->buildLink('canonical:account/upgrade-purchase');
		$purchase->cancelUrl = $router->buildLink('canonical:account/upgrades');

		return $purchase;
	}

	public function completePurchase(CallbackState $state)
	{
		if ($state->legacy)
		{
			$purchaseRequest = null;
			$userUpgradeId = $state->userUpgrade->user_upgrade_id;
			$userUpgradeRecordId = $state->userUpgradeRecordId;
		}
		else
		{
			$purchaseRequest = $state->getPurchaseRequest();
			$userUpgradeId = $purchaseRequest->extra_data['user_upgrade_id'];
			$userUpgradeRecordId = isset($purchaseRequest->extra_data['user_upgrade_record_id'])
				? $purchaseRequest->extra_data['user_upgrade_record_id']
				: null;
		}

		$paymentResult = $state->paymentResult;
		$purchaser = $state->getPurchaser();

		$userUpgrade = \XF::em()->find(
			'XF:UserUpgrade', $userUpgradeId, 'Active|' . $purchaser->user_id
		);

		/** @var \XF\Service\User\Upgrade $upgradeService */
		$upgradeService = \XF::app()->service('XF:User\Upgrade', $userUpgrade, $purchaser);

		if ($state->extraData && is_array($state->extraData))
		{
			$upgradeService->setExtraData($state->extraData);
		}

		$activeUpgrade = null;

		switch ($paymentResult)
		{
			case CallbackState::PAYMENT_RECEIVED:
				$upgradeService->setPurchaseRequestKey($state->requestKey);
				$activeUpgrade = $upgradeService->upgrade();

				$state->logType = 'payment';
				$state->logMessage = 'Payment received, upgraded/extended.';
				break;

			case CallbackState::PAYMENT_REINSTATED:
				if ($userUpgradeRecordId)
				{
					$existingRecord = \XF::em()->find('XF:UserUpgradeActive', $userUpgradeRecordId);
					$endColumn = 'end_date';

					if (!$existingRecord)
					{
						$existingRecord = \XF::em()->find('XF:UserUpgradeExpired', $userUpgradeRecordId);
						$endColumn = 'original_end_date';
					}
					if ($existingRecord)
					{
						$upgradeService->setEndDate($existingRecord->$endColumn);
					}
					$upgradeService->ignoreUnpurchasable(true);
					$activeUpgrade = $upgradeService->upgrade();

					$state->logType = 'payment';
					$state->logMessage = 'Reversal cancelled, upgrade reactivated.';
				}
				else
				{
					// We can't reinstate the upgrade because there doesn't appear to be an existing record.
					$state->logType = 'info';
					$state->logMessage = 'OK, no action.';
				}
				break;
		}

		if ($activeUpgrade && $purchaseRequest)
		{
			$extraData = $purchaseRequest->extra_data;
			$extraData['user_upgrade_record_id'] = $activeUpgrade->user_upgrade_record_id;
			$purchaseRequest->extra_data = $extraData;
			$purchaseRequest->save();
		}
	}

	public function reversePurchase(CallbackState $state)
	{
		if ($state->legacy)
		{
			$purchaseRequest = null;
			$userUpgradeId = $state->userUpgrade->user_upgrade_id;
		}
		else
		{
			$purchaseRequest = $state->getPurchaseRequest();
			$userUpgradeId = $purchaseRequest->extra_data['user_upgrade_id'];
		}

		$purchaser = $state->getPurchaser();

		$userUpgrade = \XF::em()->find(
			'XF:UserUpgrade', $userUpgradeId, 'Active|' . $purchaser->user_id
		);

		/** @var \XF\Service\User\Downgrade $downgradeService */
		$downgradeService = \XF::app()->service('XF:User\Downgrade', $userUpgrade, $purchaser);
		$downgradeService->setSendAlert(false);
		$downgradeService->downgrade();

		$state->logType = 'cancel';
		$state->logMessage = 'Payment refunded/reversed, downgraded.';
	}

	public function getPurchasablesByProfileId($profileId)
	{
		$finder = \XF::finder('XF:UserUpgrade');

		$quotedProfileId = $finder->quote($profileId);
		$columnName = $finder->columnSqlName('payment_profile_ids');

		$router = \XF::app()->router('admin');
		$upgrades = $finder->whereSql("FIND_IN_SET($quotedProfileId, $columnName)")->fetch();
		return $upgrades->pluck(function(\XF\Entity\UserUpgrade $upgrade, $key) use ($router)
		{
			return ['user_upgrade_' . $key, [
				'title' => $this->getTitle() . ': ' . $upgrade->title,
				'link' => $router->buildLink('user-upgrades/edit', $upgrade)
			]];
		}, false);
	}
}