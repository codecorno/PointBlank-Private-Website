<?php

namespace XF\Repository;

use XF\Mvc\Entity\Repository;

class Purchase extends Repository
{
	public function insertPurchaseRequest(\XF\Purchasable\Purchase $purchase)
	{
		$purchaseRequest = $this->em->create('XF:PurchaseRequest');

		$purchaseRequest->request_key = $this->generateRequestKey();
		$purchaseRequest->user_id = $purchase->purchaser->user_id;
		$purchaseRequest->provider_id = $purchase->paymentProfile->provider_id;
		$purchaseRequest->payment_profile_id = $purchase->paymentProfile->payment_profile_id;
		$purchaseRequest->purchasable_type_id = $purchase->purchasableTypeId;
		$purchaseRequest->cost_amount = $purchase->cost;
		$purchaseRequest->cost_currency = $purchase->currency;
		$purchaseRequest->extra_data = $purchase->extraData;

		$purchaseRequest->save();

		return $purchaseRequest;
	}

	/**
	 * @return string
	 */
	public function generateRequestKey()
	{
		$finder = $this->finder('XF:PurchaseRequest');

		do
		{
			$requestKey = \XF::generateRandomString(32);

			$found = $finder->resetWhere()
				->where('request_key', $requestKey)
				->fetchOne();

			if (!$found)
			{
				break;
			}
		}
		while (true);

		return $requestKey;
	}
}