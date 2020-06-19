<?php

namespace XF\Payment;

use XF\Entity\PaymentProfile;
use XF\Entity\PurchaseRequest;
use XF\Mvc\Controller;
use XF\Purchasable\Purchase;

abstract class AbstractProvider
{
	const ERR_NO_RECURRING = 1;
	const ERR_INVALID_RECURRENCE = 2;
	const VALID_RECURRING = 3;

	protected $providerId;

	abstract public function getTitle();

	protected function getPaymentParams(PurchaseRequest $purchaseRequest, Purchase $purchase)
	{
		return [];
	}

	abstract public function initiatePayment(Controller $controller, PurchaseRequest $purchaseRequest, Purchase $purchase);

	/**
	 * @param \XF\Http\Request $request
	 *
	 * @return CallbackState
	 */
	abstract public function setupCallback(\XF\Http\Request $request);
	
	abstract public function getPaymentResult(CallbackState $state);

	abstract public function prepareLogData(CallbackState $state);

	public function __construct($providerId)
	{
		$this->providerId = $providerId;
	}

	public function renderConfig(\XF\Entity\PaymentProfile $profile)
	{
		$data = [
			'profile' => $profile
		];
		return \XF::app()->templater()->renderTemplate('admin:payment_profile_' . $this->providerId, $data);
	}

	public function verifyConfig(array &$options, &$errors = [])
	{
		return true;
	}

	public function processPayment(Controller $controller, PurchaseRequest $purchaseRequest, PaymentProfile $paymentProfile, Purchase $purchase)
	{
		return null;
	}

	public function renderCancellationTemplate(PurchaseRequest $purchaseRequest)
	{
		return '';
	}

	protected function renderCancellationDefault(PurchaseRequest $purchaseRequest)
	{
		$data = [
			'purchaseRequest' => $purchaseRequest
		];
		return \XF::app()->templater()->renderTemplate('public:payment_cancel_recurring', $data);
	}

	/**
	 * @param Controller $controller
	 * @param PurchaseRequest $purchaseRequest
	 * @param PaymentProfile $paymentProfile
	 *
	 * @return \XF\Mvc\Reply\AbstractReply
	 */
	public function processCancellation(Controller $controller, PurchaseRequest $purchaseRequest, PaymentProfile $paymentProfile)
	{
		throw new \LogicException("processCancellation must be overridden.");
	}

	public function validateCallback(CallbackState $state)
	{
		return true;
	}

	public function validateTransaction(CallbackState $state)
	{
		/** @var \XF\Repository\Payment $paymentRepo */
		$paymentRepo = \XF::repository('XF:Payment');
		if ($paymentRepo->findLogsByTransactionId($state->transactionId)->total())
		{
			$state->logType = 'info';
			$state->logMessage = 'Transaction already processed. Skipping.';
			return false;
		}
		return true;
	}

	public function validatePurchaseRequest(CallbackState $state)
	{
		if (!$state->getPurchaseRequest())
		{
			$state->logType = 'error';
			$state->logMessage = 'Invalid purchase request.';
			return false;
		}
		return true;
	}

	public function validatePurchasableHandler(CallbackState $state)
	{
		if (!$state->getPurchasableHandler())
		{
			$state->logType = 'error';
			$state->logMessage = 'Could not find handler for purchasable type \'' . $state->getPurchaseRequest()->purchasable_type_id  . '\'.';
			return false;
		}
		return true;
	}
	
	public function validatePaymentProfile(CallbackState $state)
	{
		if (!$state->getPaymentProfile())
		{
			$state->logType = 'error';
			$state->logMessage = 'Could not find a matching payment profile.';
			return false;
		}
		return true;
	}

	public function validatePurchaser(CallbackState $state)
	{
		$handler = $state->getPurchasableHandler();

		if (!$handler->validatePurchaser($state, $error))
		{
			$state->logType = 'error';
			$state->logMessage = $error;
			return false;
		}
		return true;
	}

	public function validatePurchasableData(CallbackState $state)
	{
		return true;
	}

	public function validateCost(CallbackState $state)
	{
		return true;
	}

	public function setProviderMetadata(CallbackState $state)
	{
		return;
	}

	public function completeTransaction(CallbackState $state)
	{
		$purchasableHandler = $state->getPurchasableHandler();

		switch ($state->paymentResult)
		{
			case CallbackState::PAYMENT_RECEIVED:
				$purchasableHandler->completePurchase($state);
				try
				{
					$purchasableHandler->sendPaymentReceipt($state);
				}
				catch (\Exception $e)
				{
					\XF::logException($e, false, "Error when sending payment receipt: ");
				}
				break;

			case CallbackState::PAYMENT_REINSTATED:
				$purchasableHandler->completePurchase($state);
				break;

			case CallbackState::PAYMENT_REVERSED:
				$purchasableHandler->reversePurchase($state);
				break;

			default:
				$state->logType = 'info';
				$state->logMessage = 'OK, no action';
				break;
		}
	}

	public function log(CallbackState $state)
	{
		$this->prepareLogData($state);

		/** @var \XF\Repository\Payment $paymentRepo */
		$paymentRepo = \XF::repository('XF:Payment');
		$paymentRepo->logCallback(
			$state->requestKey,
			$this->providerId,
			$state->transactionId,
			$state->logType,
			$state->logMessage,
			$state->logDetails,
			$state->subscriberId
		);
	}

	/**
	 * Verifies whether a provider supports recurring payments and verifies if the desired length is allowed
	 * by the provider. The $result param provides more detail.
	 *
	 * Most providers only support one year so unless overridden, that will be the case for all providers.
	 *
	 * @return bool
	 */
	public function supportsRecurring(PaymentProfile $paymentProfile, $unit, $amount, &$result = self::ERR_NO_RECURRING)
	{
		$supported = false;

		$ranges = $this->getSupportedRecurrenceRanges();
		if (isset($ranges[$unit]))
		{
			list($minRange, $maxRange) = $ranges[$unit];
			if ($amount >= $minRange && $amount <= $maxRange)
			{
				$supported = true;
			}
		}

		if ($supported)
		{
			$result = self::VALID_RECURRING;
		}
		else
		{
			$result = self::ERR_INVALID_RECURRENCE;
		}

		return $supported;
	}

	protected function getSupportedRecurrenceRanges()
	{
		return [
			'day' => [1, 365],
			'week' => [1, 52],
			'month' => [1, 12],
			'year' => [1, 1]
		];
	}

	public function verifyCurrency(PaymentProfile $paymentProfile, $currencyCode)
	{
		return true;
	}

	public function getCallbackUrl()
	{
		return \XF::app()->options()->boardUrl . '/payment_callback.php?_xfProvider=' . $this->providerId;
	}

	public function getApiEndpoint()
	{
		return '';
	}

	public function getProviderId()
	{
		return $this->providerId;
	}
}