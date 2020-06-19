<?php

namespace XF\Payment;

use XF\Entity\PaymentProfile;
use XF\Entity\PurchaseRequest;
use XF\Mvc\Controller;
use XF\Purchasable\Purchase;

use Braintree\ClientToken;
use Braintree\Configuration;
use Braintree\Customer;
use Braintree\MerchantAccount;
use Braintree\PaymentMethod;
use Braintree\Plan;
use Braintree\Subscription;
use Braintree\Transaction;
use Braintree\WebhookNotification;

class Braintree extends AbstractProvider
{
	public function getTitle()
	{
		return 'Braintree';
	}

	public function setupBraintreeConfig(array $options, &$error = '')
	{
		try
		{
			if (!\XF::config('enableLivePayments'))
			{
				Configuration::environment('sandbox');
			}
			else
			{
				Configuration::environment('production');
			}
			Configuration::merchantId($options['merchant_id']);
			Configuration::publicKey($options['public_key']);
			Configuration::privateKey($options['private_key']);
			Configuration::assertGlobalHasAccessTokenOrKeys();
		}
		catch (\Braintree\Exception\Configuration $e)
		{
			$error = 'The following error occurred while verifying the Braintree configuration: ' . $e->getMessage();
		}
	}

	public function getBraintreeCustomerId(\XF\Entity\User $purchaser)
	{
		$userId = $purchaser->user_id;
		$userSecret = $purchaser->secret_key;

		// base64 characters / and + are not usable in Braintree customer IDs, but - and _ are
		$encoded = str_replace(['/', '+'], ['-', '_'], base64_encode(
			hash_hmac('sha1', $userId, $userSecret, true)
		));
		return "xf_{$userId}_" . substr($encoded, 0, 16);
	}

	public function verifyConfig(array &$options, &$errors = [])
	{
		$this->setupBraintreeConfig($options, $error);
		if ($error)
		{
			$errors[] = $error;
			return false;
		}
		if (!$options['merchant_account'])
		{
			$errors[] = 'You must provide a valid merchant account ID. Your merchant account ID dictates which currency your payments are charged in.';
			return false;
		}
		try
		{
			MerchantAccount::find($options['merchant_account']);
		}
		catch (\Braintree\Exception\NotFound $e)
		{
			$errors[] = 'You must provide a valid merchant account ID: ' . $e->getMessage();
			return false;
		}
		catch (\Braintree\Exception\Authentication $e)
		{
			$errors[] = 'Authentication error: ' . $e->getMessage();
		}
		return true;
	}

	protected function getPaymentParams(PurchaseRequest $purchaseRequest, Purchase $purchase)
	{
		$paymentProfile = $purchase->paymentProfile;
		$this->setupBraintreeConfig($paymentProfile->options);

		try
		{
			$customer = Customer::find($this->getBraintreeCustomerId($purchase->purchaser));
			$tokenParams = [
				'customerId' => $customer->id
			];
		}
		catch (\Braintree\Exception\NotFound $e)
		{
			$tokenParams = [];
		}

		$clientToken = ClientToken::generate($tokenParams);

		return [
			'purchaseRequest' => $purchaseRequest,
			'paymentProfile' => $paymentProfile,
			'purchaser' => $purchase->purchaser,
			'purchase' => $purchase,
			'clientToken' => $clientToken
		];
	}

	public function initiatePayment(Controller $controller, PurchaseRequest $purchaseRequest, Purchase $purchase)
	{
		$viewParams = $this->getPaymentParams($purchaseRequest, $purchase);
		return $controller->view('XF:Payment\BraintreeInitiate', 'payment_initiate_braintree', $viewParams);
	}

	public function processPayment(Controller $controller, PurchaseRequest $purchaseRequest, PaymentProfile $paymentProfile, Purchase $purchase)
	{
		$nonce = $controller->filter('nonce', 'str');
		$type = $controller->filter('type', 'str');

		$this->setupBraintreeConfig($paymentProfile->options);

		$purchaser = $purchase->purchaser;
		$customerId = $this->getBraintreeCustomerId($purchaser);

		try
		{
			Customer::find($customerId);
		}
		catch (\Braintree\Exception\NotFound $e)
		{
			Customer::create([
				'id' => $customerId,
				'email' => $purchaser->email,
				'website' => $purchaser->Profile->website
			]);
		}

		if ($purchase->recurring)
		{
			$paymentMethod = PaymentMethod::create([
				'customerId' => $customerId,
				'paymentMethodNonce' => $nonce,
				'options' => [
					'failOnDuplicatePaymentMethod' => true,
					'makeDefault' => true
				]
			]);

			if (!$paymentMethod->success)
			{
				return $controller->error($paymentMethod->message ?: 'Error while storing Braintree payment method.');
			}

			$token = $paymentMethod->paymentMethod->token;

			$subscription = Subscription::create([
				'price' => $purchase->cost,
				'paymentMethodToken' => $token,
				'planId' => isset($paymentProfile->options['plan_id'])
					? $paymentProfile->options['plan_id']
					: '',
				'options' => [
					'doNotInheritAddOnsOrDiscounts' => true,
					'startImmediately' => true
				],
				'id' => $purchaseRequest->request_key,
				'merchantAccountId' => $paymentProfile->options['merchant_account']
			]);

			if ($subscription->success && $subscription->subscription->status === 'Active')
			{
				$state = new CallbackState();
				$state->notification = $subscription;
				$state->subscriberId = $subscription->subscription->id;

				// Payment result will come through as a webhook when the subscription is charged.

				$state->purchaseRequest = $purchaseRequest;
				$state->paymentProfile = $paymentProfile;

				$this->completeTransaction($state);

				$this->log($state);

				return $controller->redirect($purchase->returnUrl, '');
			}

			return $controller->error($subscription->message ?: 'Error while processing Braintree subscription.');
		}
		else
		{
			$saleParams = [
				'amount' => $purchase->cost,
				'paymentMethodNonce' => $nonce,
				'options' => [
					'submitForSettlement' => true,
					'storeInVaultOnSuccess' => true
				],
				'orderId' => $purchaseRequest->request_key,
				'merchantAccountId' => $paymentProfile->options['merchant_account'],
				'customerId' => $customerId
			];
			if ($type == 'PayPalAccount')
			{
				$saleParams['options']['paypal'] = [
					'customField' => $purchaseRequest->request_key,
					'description' => $purchase->title
				];
			}

			/** @var \Braintree\Result\Successful $sale */
			$sale = Transaction::sale($saleParams);

			if ($sale->success && $sale->transaction->id
				&& ($sale->transaction->status === 'submitted_for_settlement' || $sale->transaction->status === 'settling')
			)
			{
				$state = new CallbackState();
				$state->notification = $sale;
				$state->transactionId = $sale->transaction->id;
				$state->paymentResult = CallbackState::PAYMENT_RECEIVED;
				$state->purchaseRequest = $purchaseRequest;
				$state->paymentProfile = $paymentProfile;

				$this->completeTransaction($state);

				$this->log($state);

				return $controller->redirect($purchase->returnUrl, '');
			}

			return $controller->error($sale->message ?: 'Error while processing Braintree sale.');
		}
	}

	public function renderCancellationTemplate(PurchaseRequest $purchaseRequest)
	{
		return $this->renderCancellationDefault($purchaseRequest);
	}

	public function processCancellation(Controller $controller, PurchaseRequest $purchaseRequest, PaymentProfile $paymentProfile)
	{
		$logFinder = \XF::finder('XF:PaymentProviderLog')
			->where('purchase_request_key', $purchaseRequest->request_key)
			->where('provider_id', $this->providerId)
			->order('log_date', 'desc');

		$subscriberId = null;
		foreach ($logFinder->fetch() AS $log)
		{
			if ($log->subscriber_id)
			{
				$subscriberId = $log->subscriber_id;
				break;
			}
		}

		if (!$subscriberId)
		{
			return $controller->error('Could not find a subscriber ID for this purchase request.');
		}

		$this->setupBraintreeConfig($paymentProfile->options, $error);
		if ($error)
		{
			return $controller->error($error);
		}

		try
		{
			Subscription::cancel($subscriberId);
		}
		catch (\Braintree\Exception\NotFound $e)
		{
			throw $controller->exception($controller->error('This subscription cannot be cancelled because it cannot be found. It may already be cancelled.'));
		}

		return $controller->redirect($controller->getDynamicRedirect(), 'Braintree subscription cancelled successfully');
	}

	public function setupCallback(\XF\Http\Request $request)
	{
		$state = new CallbackState();

		$state->bt_signature = $request->filter('bt_signature', 'str');
		$state->bt_payload = $request->filter('bt_payload', 'str');

		if (!$state->bt_signature || !$state->bt_payload)
		{
			return $state;
		}

		$xml = base64_decode($state->bt_payload);
		$attributes = \Braintree\Xml::buildArrayFromXml($xml);
		if (isset($attributes['notification']))
		{
			$state->notification = \XF::app()->inputFilterer()->filter($attributes['notification'], 'array');
		}
		else
		{
			$state->notification = [];
		}

		return $state;
	}

	public function validateCallback(CallbackState $state)
	{
		if (isset($state->notification['subject']['subscription']['id']))
		{
			$state->requestKey = $state->subscriberId = $state->notification['subject']['subscription']['id'];
		}
		else if (isset($state->notification['subject']['dispute']['transaction']['id']))
		{
			$state->originalTransactionId = $state->notification['subject']['dispute']['transaction']['id'];

			$providerLog = \XF::finder('XF:PaymentProviderLog')
				->where([
					'provider_id' => $this->providerId,
					'transaction_id' => $state->originalTransactionId
				])
				->order('log_date', 'DESC')
				->fetchOne();
			if ($providerLog)
			{
				$state->requestKey = $providerLog->purchase_request_key;
			}
			else
			{
				$state->logType = 'error';
				$state->logMessage = 'Could not find a corresponding purchase request.';
				return false;
			}
		}
		else
		{
			$state->logType = 'error';
			$state->logMessage = 'Raw notification received from Braintree does not appear to have a transaction ID or subscriber ID.';
			return false;
		}

		$purchaseRequest = $state->getPurchaseRequest();
		$paymentProfile = $state->getPaymentProfile();

		if (!$paymentProfile)
		{
			$state->logType = 'error';
			$state->logMessage = 'Invalid purchase request.';
			return false;
		}

		$options = $paymentProfile->options;

		// We can now use the Braintree SDK to verify the webhook we received was valid and get the transaction details.
		try
		{
			$this->setupBraintreeConfig($options);

			$notification = WebhookNotification::parse(
				$state->bt_signature,
				$state->bt_payload
			);
		}
		catch (\Exception $e)
		{
			$state->logType = 'error';
			$state->logMessage = 'Configuration or webhook could not be verified: ' . $e->getMessage();
			return false;
		}

		if ($state->subscriberId)
		{
			if ($notification->subscription->id !== $state->subscriberId)
			{
				$state->logType = 'error';
				$state->logMessage = 'Webhook did not contain the details of the expected purchase request';
				return false;
			}
		}
		else
		{
			if (!isset($notification->dispute['transaction']['id'])
				|| $notification->dispute['transaction']['id'] !== $state->originalTransactionId
			)
			{
				$state->logType = 'error';
				$state->logMessage = 'Webhook did not contain the details of the expected transaction';
				return false;
			}

			$transaction = Transaction::find($state->originalTransactionId);
			if (!$transaction->orderId || $transaction->orderId !== $purchaseRequest->request_key)
			{
				$state->logType = 'error';
				$state->logMessage = 'Webhook did not contain the details of the expected purchase request';
				return false;
			}
		}

		$state->transactionId = md5(serialize($notification));
		$state->kind = $notification->kind;

		return true;
	}

	public function validateTransaction(CallbackState $state)
	{
		if (!$state->transactionId)
		{
			$state->logType = 'info';
			$state->logMessage = 'No transaction ID. No action to take.';
			return false;
		}
		return parent::validateTransaction($state);
	}

	public function validateCost(CallbackState $state)
	{
		$purchaseRequest = $state->getPurchaseRequest();

		$costValidated = false;

		switch ($state->kind)
		{
			case WebhookNotification::SUBSCRIPTION_CHARGED_SUCCESSFULLY:

				if (isset($state->notification['subject']['subscription']['transactions']))
				{
					foreach ($state->notification['subject']['subscription']['transactions'] AS $transaction)
					{
						if ($transaction['amount'] === $purchaseRequest->cost_amount)
						{
							$costValidated = true;
							break;
						}
					}
				}

				if (!$costValidated)
				{
					$state->logType = 'error';
					$state->logMessage = 'Invalid cost amount';
					return false;
				}
				break;
		}

		return true;
	}

	public function getPaymentResult(CallbackState $state)
	{
		switch ($state->kind)
		{
			case WebhookNotification::DISPUTE_OPENED:
			case WebhookNotification::DISPUTE_LOST:

				$state->paymentResult = CallbackState::PAYMENT_REVERSED;
				break;

			case WebhookNotification::DISPUTE_WON:

				$state->paymentResult = CallbackState::PAYMENT_REINSTATED;
				break;

			case WebhookNotification::SUBSCRIPTION_CHARGED_SUCCESSFULLY:

				$state->paymentResult = CallbackState::PAYMENT_RECEIVED;
				break;
		}
	}

	public function prepareLogData(CallbackState $state)
	{
		$logDetails = [];
		if (!empty($state->notification->transaction) && $transaction = $state->notification->transaction)
		{
			$keys = [
				'addOns', 'additionalProcessorResponse', 'amount', 'avsErrorResponseCode', 'avsPostalCodeResponseCode',
				'avsStreetAddressResponseCode', 'billingDetails', 'channel', 'createdAt', 'creditCardDetails', 'currencyIsoCode',
				'customFields', 'customerDetails', 'cvvResponseCode', 'descriptor', 'disbursementDetails', 'discounts',
				'disputes', 'escrowStatus', 'europeBankAccount', 'gatewayRejectionReason', 'id', 'merchantAccountId', 'orderId',
				'paymentInstrumentType', 'paypalDetails', 'planId', 'processorAuthorizationCode', 'processorResponseCode',
				'processorResponseText', 'processorSettlementResponseCode', 'processorSettlementResponseText', 'purchaseOrderNumber',
				'recurring', 'refundIds', 'refundedTransactionId', 'riskData', 'serviceFeeAmount', 'settlementBatchId',
				'shippingDetails', 'status', 'statusHistory', 'subscriptionDetails', 'subscriptionId', 'taxAmount', 'taxExempt',
				'threeDSecureInfo', 'type', 'updatedAt', 'voiceReferralNumber'
			];
			foreach ($keys AS $key)
			{
				$logDetails['transaction'][$key] = isset($transaction->{$key}) ? $transaction->{$key} : null;
			}
		}
		else if (!empty($state->notification->subscription) && $subscription = $state->notification->subscription)
		{
			$keys = [
				'addOns', 'balance', 'billingDayOfMonth', 'billingPeriodEndDate', 'billingPeriodStartDate', 'createdAt',
				'currentBillingCycle', 'daysPastDue', 'descriptor', 'discounts', 'failureCount', 'firstBillingDate', 'id',
				'merchantAccountId', 'neverExpires', 'nextBillAmount', 'nextBillingDate', 'nextBillingPeriodAmount',
				'numberOfBillingCycles', 'paidThroughDate', 'paymentMethodToken', 'planId', 'price', 'status', 'statusHistory',
				'transactions', 'trialDuration', 'trialDurationUnit', 'trialPeriod', 'updatedAt'
			];
			foreach ($keys AS $key)
			{
				$logDetails['subscription'][$key] = isset($subscription->{$key}) ? $subscription->{$key} : null;
			}
		}
		else if (is_array($state->notification))
		{
			$logDetails = $state->notification;
		}
		$state->logDetails = $logDetails;
	}

	public function supportsRecurring(PaymentProfile $paymentProfile, $unit, $amount, &$result = self::ERR_NO_RECURRING)
	{
		if (empty($paymentProfile->options['plan_id']))
		{
			return false;
		}

		$this->setupBraintreeConfig($paymentProfile->options, $error);
		if ($error)
		{
			return false;
		}

		$planValid = false;

		$plans = Plan::all();
		foreach ($plans AS $plan)
		{
			if ($plan->id != $paymentProfile->options['plan_id'])
			{
				continue;
			}

			if ($plan->billingFrequency == $amount)
			{
				$planValid = true;
			}
		}

		if ($unit !== 'month' || $amount > 12 || !$planValid)
		{
			$result = self::ERR_INVALID_RECURRENCE;
			return false;
		}

		return true;
	}

	public function verifyCurrency(PaymentProfile $paymentProfile, $currencyCode)
	{
		$this->setupBraintreeConfig($paymentProfile->options, $error);
		if ($error)
		{
			return false;
		}

		$merchantAccount = MerchantAccount::find($paymentProfile->options['merchant_account']);
		if ($merchantAccount->currencyIsoCode !== $currencyCode)
		{
			return false;
		}

		if (!empty($paymentProfile->options['plan_id']))
		{
			$planValid = true;

			$plans = Plan::all();
			foreach ($plans AS $plan)
			{
				if ($plan->id != $paymentProfile->options['plan_id'])
				{
					continue;
				}

				if ($plan->currencyIsoCode !== $currencyCode)
				{
					$planValid = false;
				}
			}

			if (!$planValid)
			{
				return false;
			}
		}
		
		return true;
	}
}