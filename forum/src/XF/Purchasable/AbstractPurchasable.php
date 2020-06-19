<?php

namespace XF\Purchasable;

use XF\Payment\CallbackState;

abstract class AbstractPurchasable
{
	protected $purchasableTypeId;

	/**
	 * Title of this Purchasable type.
	 *
	 * @return mixed
	 */
	abstract public function getTitle();

	/**
	 * Prepares all the data required to create the Purchase object.
	 *
	 * @param \XF\Http\Request $request
	 * @param \XF\Entity\User $purchaser
	 * @param null $error
	 *
	 * @return Purchase
	 */
	abstract public function getPurchaseFromRequest(\XF\Http\Request $request, \XF\Entity\User $purchaser, &$error = null);

	/**
	 * Given a purchase request's extra data, the purchasable item will be found and returned.
	 *
	 * @param array $extraData
	 *
	 * @return mixed
	 */
	abstract public function getPurchasableFromExtraData(array $extraData);

	/**
	 * Prepares all the data required to create the Purchase object from a purchase request's extra data.
	 *
	 * @param array $extraData
	 * @param \XF\Entity\PaymentProfile $paymentProfile
	 * @param \XF\Entity\User $purchaser
	 * @param string $error
	 *
	 * @return Purchase
	 */
	abstract public function getPurchaseFromExtraData(array $extraData, \XF\Entity\PaymentProfile $paymentProfile, \XF\Entity\User $purchaser, &$error = null);

	/**
	 * Creates the Purchase object which represents all of the data required to request a payment.
	 *
	 * @param \XF\Entity\PaymentProfile $paymentProfile
	 * @param $purchasable
	 * @param \XF\Entity\User $purchaser
	 *
	 * @return mixed
	 */
	abstract public function getPurchaseObject(\XF\Entity\PaymentProfile $paymentProfile, $purchasable, \XF\Entity\User $purchaser);

	/**
	 * @param CallbackState $state
	 *
	 * @return mixed
	 */
	abstract public function completePurchase(CallbackState $state);

	/**
	 * @param CallbackState $state
	 *
	 * @return mixed
	 */
	abstract public function reversePurchase(CallbackState $state);

	public function validatePurchaser(CallbackState $state, &$error = null)
	{
		if (!$state->getPurchaser())
		{
			if ($state->getPurchaseRequest()->user_id)
			{
				$error = 'Could not find user with user_id ' . $state->getPurchaseRequest()->user_id . '.';
			}
			else
			{
				$error = 'Purchasable type ' . $this->purchasableTypeId . ' does not support payments from guests.';
			}

			return false;
		}
		return true;
	}

	/**
	 * Given a payment profile ID, we can enumerate the purchasable items
	 * which are used by these profiles. Useful to block accidental deletion
	 * of payment profiles which may be legitimately in use.
	 *
	 * This method should return an array of purchasables which are in use by the given profile ID.
	 *
	 * @param $profileId
	 *
	 * @return array
	 */
	abstract public function getPurchasablesByProfileId($profileId);

	public function __construct($purchasableTypeId)
	{
		$this->purchasableTypeId = $purchasableTypeId;
	}

	public function sendPaymentReceipt(CallbackState $state)
	{
		if (!$state->getPurchaser())
		{
			return;
		}

		switch ($state->paymentResult)
		{
			case CallbackState::PAYMENT_RECEIVED:
			{
				$purchaser = $state->getPurchaser();
				$purchaseRequest = $state->getPurchaseRequest();
				if ($purchaseRequest)
				{
					$purchasable = $this->getPurchasableFromExtraData($purchaseRequest->extra_data);

					$params = [
						'purchaser' => $purchaser,
						'purchaseRequest' => $purchaseRequest,
						'purchasable' => $purchasable
					];

					\XF::app()->mailer()->newMail()
						->setToUser($purchaser)
						->setTemplate('payment_received_receipt_' . $this->purchasableTypeId, $params)
						->send();
				}
			}
		}
	}
}