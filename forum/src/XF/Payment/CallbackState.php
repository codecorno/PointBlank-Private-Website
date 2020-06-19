<?php

namespace XF\Payment;

/**
 * @property \XF\Entity\PurchaseRequest $purchaseRequest
 * @property \XF\Purchasable\AbstractPurchasable $purchasableHandler
 * @property \XF\Entity\PaymentProfile $paymentProfile
 * @property \XF\Entity\User $purchaser;
 *
 * @property int $paymentResult
 *
 * @property string $requestKey
 *
 * @property string $transactionId
 * @property string $subscriberId
 * @property string $paymentCountry
 *
 * @property string $logType
 * @property string $logMessage
 * @property array $logDetails
 * @property int $httpCode
 */
class CallbackState
{
	protected $purchaseRequest;
	protected $purchasableHandler;
	protected $paymentProfile;
	protected $purchaser;
	protected $paymentResult;

	const PAYMENT_RECEIVED = 1; // received payment
	const PAYMENT_REVERSED = 2; // refund/reversal
	const PAYMENT_REINSTATED = 3; // reversal cancelled

	public function getPurchaseRequest()
	{
		return $this->purchaseRequest;
	}

	public function getPurchasableHandler()
	{
		if ($this->purchasableHandler)
		{
			return $this->purchasableHandler;
		}

		$purchaseRequest = $this->getPurchaseRequest();
		if (!$purchaseRequest)
		{
			return false;
		}

		/** @var \XF\Entity\Purchasable $purchasable */
		$purchasable = \XF::em()->find('XF:Purchasable', $purchaseRequest->purchasable_type_id);
		if (!$purchasable || !$purchasable->handler)
		{
			return false;
		}

		$this->purchasableHandler = $purchasable->handler;
		return $this->purchasableHandler;
	}

	public function getPaymentProfile()
	{
		if ($this->paymentProfile)
		{
			return $this->paymentProfile;
		}

		$purchaseRequest = $this->getPurchaseRequest();
		if (!$purchaseRequest)
		{
			return false;
		}

		$paymentProfile = \XF::em()->find('XF:PaymentProfile', $purchaseRequest->payment_profile_id);
		if (!$paymentProfile)
		{
			return false;
		}

		$this->paymentProfile = $paymentProfile;
		return $this->paymentProfile;
	}

	public function getPurchaser()
	{
		if ($this->purchaser)
		{
			return $this->purchaser;
		}

		$purchaseRequest = $this->purchaseRequest;
		if (!$purchaseRequest)
		{
			return false;
		}

		$user = \XF::em()->find('XF:User', $purchaseRequest->user_id);
		if (!$user)
		{
			return false;
		}

		$this->purchaser = $user;
		return $this->purchaser;
	}

	function __get($name)
	{
		return isset($this->{$name}) ? $this->{$name} : null;
	}

	function __set($name, $value)
	{
		switch ($name)
		{
			case 'purchaseRequest':
				$this->purchaseRequest = $value;
				if ($value)
				{
					$this->requestKey = $value->request_key;
				}
				break;

			case 'requestKey':
				$this->purchaseRequest = \XF::em()->findOne('XF:PurchaseRequest', ['request_key' => $value]);
				$this->requestKey = $value;
				break;

			default:
				$this->{$name} = $value;
				break;
		}
	}
}