<?php

namespace XF\Pub\Controller;

use XF\Mvc\ParameterBag;

class Purchase extends AbstractController
{
	public function actionIndex(ParameterBag $params)
	{
		$purchasable = $this->assertPurchasableExists($params->purchasable_type_id);

		if (!$purchasable->isActive())
		{
			throw $this->exception($this->error(\XF::phrase('items_of_this_type_cannot_be_purchased_at_moment')));
		}

		/** @var \XF\Purchasable\AbstractPurchasable $purchasableHandler */
		$purchasableHandler = $purchasable->handler;

		$purchase = $purchasableHandler->getPurchaseFromRequest($this->request, \XF::visitor(), $error);
		if (!$purchase)
		{
			throw $this->exception($this->error($error));
		}

		$purchaseRequest = $this->repository('XF:Purchase')->insertPurchaseRequest($purchase);
		
		$providerHandler = $purchase->paymentProfile->getPaymentHandler();
		return $providerHandler->initiatePayment($this, $purchaseRequest, $purchase);
	}

	public function actionProcess()
	{
		$purchaseRequest = $this->em()->findOne('XF:PurchaseRequest', $this->filter(['request_key' => 'str']), 'User');
		if (!$purchaseRequest)
		{
			throw $this->exception($this->error(\XF::phrase('invalid_purchase_request')));
		}

		/** @var \XF\Entity\PaymentProfile $paymentProfile */
		$paymentProfile = $this->em()->find('XF:PaymentProfile', $purchaseRequest->payment_profile_id);
		if (!$paymentProfile)
		{
			throw $this->exception($this->error(\XF::phrase('purchase_request_contains_invalid_payment_profile')));
		}

		$purchasable = $this->assertPurchasableExists($purchaseRequest->purchasable_type_id);

		/** @var \XF\Purchasable\AbstractPurchasable $purchasableHandler */
		$purchasableHandler = $purchasable->handler;

		$purchase = $purchasableHandler->getPurchaseFromExtraData($purchaseRequest->extra_data, $paymentProfile, \XF::visitor(), $error);
		if (!$purchase)
		{
			throw $this->exception($this->error($error));
		}

		$providerHandler = $paymentProfile->Provider->handler;
		$result = $providerHandler->processPayment($this, $purchaseRequest, $paymentProfile, $purchase);
		if (!$result)
		{
			return $this->redirect($purchase->returnUrl);
		}

		return $result;
	}

	public function actionCancelRecurring(ParameterBag $params)
	{
		$purchaseRequest = $this->em()->findOne('XF:PurchaseRequest', $this->filter(['request_key' => 'str']), 'User');
		if (!$purchaseRequest)
		{
			throw $this->exception($this->error(\XF::phrase('invalid_purchase_request')));
		}

		/** @var \XF\Entity\PaymentProfile $paymentProfile */
		$paymentProfile = $this->em()->find('XF:PaymentProfile', $purchaseRequest->payment_profile_id);
		if (!$paymentProfile)
		{
			throw $this->exception($this->error(\XF::phrase('purchase_request_contains_invalid_payment_profile')));
		}

		$purchasable = $this->assertPurchasableExists($purchaseRequest->purchasable_type_id);

		/** @var \XF\Purchasable\AbstractPurchasable $purchasableHandler */
		$purchasableHandler = $purchasable->handler;
		$purchasableItem = $purchasableHandler->getPurchasableFromExtraData($purchaseRequest->extra_data);

		$providerHandler = $paymentProfile->Provider->handler;

		if ($this->isPost())
		{
			return $providerHandler->processCancellation($this, $purchaseRequest, $paymentProfile);
		}
		else
		{
			$viewParams = [
				'purchaseRequest' => $purchaseRequest,
				'paymentProfile' => $paymentProfile,
				'purchasableItem' => $purchasableItem
			];
			return $this->view('XF:Purchase/CancelRecurring', 'payment_cancel_recurring_confirm', $viewParams);
		}
	}

	/**
	 * @param string $id
	 * @param array|string|null $with
	 * @param null|string $phraseKey
	 *
	 * @return \XF\Entity\Purchasable
	 */
	protected function assertPurchasableExists($id, $with = null, $phraseKey = null)
	{
		return $this->assertRecordExists('XF:Purchasable', $id, $with, $phraseKey);
	}

	public static function getActivityDetails(array $activities)
	{
		return \XF::phrase('managing_account_details');
	}
}