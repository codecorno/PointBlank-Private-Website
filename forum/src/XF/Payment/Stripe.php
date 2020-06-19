<?php

namespace XF\Payment;

use XF\Entity\PaymentProfile;
use XF\Entity\PurchaseRequest;
use XF\Mvc\Controller;
use XF\Purchasable\Purchase;

class Stripe extends AbstractProvider
{
	// Force a specific Stripe version so we can better control
	// when we are ready for potential code breaking changes.

	protected $stripeVersion = '2019-08-14';

	public function getTitle()
	{
		return 'Stripe';
	}

	public function verifyConfig(array &$options, &$errors = [])
	{
		if (\XF::config('enableLivePayments'))
		{
			$keyName = 'live_secret_key';
		}
		else
		{
			$keyName = 'test_secret_key';
		}

		$secretKey = $options[$keyName];

		if ($secretKey)
		{
			try
			{
				\Stripe\Stripe::setAppInfo(
					'XenForo',
					\XF::$version,
					'https://xenforo.com/contact'
				);
				\Stripe\Stripe::setApiKey($secretKey);
				\Stripe\Stripe::setApiVersion($this->stripeVersion);

				$stripeAccount = \Stripe\Account::retrieve();
			}
			catch (\Exception $e)
			{
				$errors[] = \XF::phrase('cannot_verify_that_your_key_x_is_valid', ['keyName' => $keyName]);
				return false;
			}

			$options['stripe_country'] = $stripeAccount->country;
		}

		return true;
	}

	protected function getStripeKey(PaymentProfile $paymentProfile, $type = 'secret')
	{
		if (\XF::config('enableLivePayments'))
		{
			$key = $paymentProfile->options['live_' . $type . '_key'];
		}
		else
		{
			$key = $paymentProfile->options['test_' . $type . '_key'];
		}

		return $key;
	}

	protected function setupStripe(PaymentProfile $paymentProfile)
	{
		\Stripe\Stripe::setAppInfo(
			'XenForo',
			\XF::$version,
			'https://xenforo.com/contact'
		);
		\Stripe\Stripe::setApiKey($this->getStripeKey($paymentProfile));
		\Stripe\Stripe::setApiVersion($this->stripeVersion);
	}

	protected function getChargeMetadata(PurchaseRequest $purchaseRequest)
	{
		return [
			'request_key' => $purchaseRequest->request_key
		] + $this->getCustomerMetadata($purchaseRequest);
	}

	protected function getCustomerMetadata(PurchaseRequest $purchaseRequest)
	{
		/** @var \XF\Validator\Email $validator */
		$validator = \XF::app()->validator('Email');

		$email = null;

		if ($purchaseRequest->User)
		{
			$email = $purchaseRequest->User->email;
			if (!$email || !$validator->isValid($email))
			{
				$email = null;
			}
		}

		if (!$email)
		{
			$email = 'invalid@example.com';
		}

		return [
			'user_id' => $purchaseRequest->user_id,
			'username' => $purchaseRequest->User
				? $purchaseRequest->User->username
				: \XF::phrase('guest'),
			'email' => $email
		];
	}

	protected function getTransactionMetadata(Purchase $purchase)
	{
		return [
			'purchasable_type_id' => $purchase->purchasableTypeId,
			'purchasable_id' => $purchase->purchasableId,
			'currency' => $purchase->currency,
			'cost' => $purchase->cost,
			'length_amount' => $purchase->lengthAmount,
			'length_unit' => $purchase->lengthUnit
		];
	}

	protected function getStatementDescriptor()
	{
		$cleanBoardTitle = str_replace(["'", '"', '*', '<', '>'], '', \XF::app()->options()->boardTitle);

		return \XF::app()->stringFormatter()->wholeWordTrim(
			$cleanBoardTitle, 22
		);
	}

	protected function getPaymentParams(PurchaseRequest $purchaseRequest, Purchase $purchase)
	{
		$paymentProfile = $purchase->paymentProfile;
		$publishableKey = $this->getStripeKey($paymentProfile, 'publishable');

		return [
			'purchaseRequest' => $purchaseRequest,
			'paymentProfile' => $paymentProfile,
			'purchaser' => $purchase->purchaser,
			'purchase' => $purchase,
			'purchasableTypeId' => $purchase->purchasableTypeId,
			'purchasableId' => $purchase->purchasableId,
			'publishableKey' => $publishableKey,
			'cost' => $this->getStripeFormattedCost(
				$purchaseRequest,
				$purchase
			)
		];
	}

	public function initiatePayment(Controller $controller, PurchaseRequest $purchaseRequest, Purchase $purchase)
	{
		$viewParams = $this->getPaymentParams($purchaseRequest, $purchase);

		$paymentIntent = $this->createPaymentIntent($purchaseRequest, $purchase, $error);

		if (!$paymentIntent)
		{
			throw $controller->exception($controller->error($error));
		}

		$viewParams['paymentIntent'] = $paymentIntent;

		if ($purchase->recurring)
		{
			$product = $this->createStripeProduct($purchase->paymentProfile, $purchase, $error);
			if (!$product)
			{
				$errorPhrase = \XF::phrase('error_occurred_while_creating_stripe_product:');
				throw $controller->exception($controller->error("$errorPhrase $error"));
			}

			$plan = $this->createStripePlan($product, $purchase->paymentProfile, $purchase, $error);
			if (!$plan)
			{
				$errorPhrase = \XF::phrase('error_occurred_while_creating_stripe_plan:');
				throw $controller->exception($controller->error("$errorPhrase $error"));
			}

			$customer = $this->getStripeCustomer($purchaseRequest, $purchase->paymentProfile, $purchase, $error);
			if (!$customer)
			{
				$errorPhrase = \XF::phrase('error_occurred_while_creating_stripe_customer:');
				throw $controller->exception($controller->error("$errorPhrase $error"));
			}

			$subscription = $this->createStripeSubscription($customer, $plan, null, $purchase->paymentProfile, $purchase, $error);
			if (!$subscription)
			{
				$errorPhrase = \XF::phrase('error_occurred_while_creating_stripe_subscription:');
				throw $controller->exception($controller->error("$errorPhrase $error"));
			}

			$purchaseRequest->fastUpdate('provider_metadata', $subscription->id);
		}

		return $controller->view('XF:Purchase\StripeInitiate', 'payment_initiate_stripe', $viewParams);
	}

	protected function getStripeProductAndPlanId(Purchase $purchase)
	{
		return $purchase->purchasableTypeId . '_' . md5(
			$purchase->currency . $purchase->cost . $purchase->lengthAmount . $purchase->lengthUnit
		);
	}

	protected function getStripeCustomer(PurchaseRequest $purchaseRequest, PaymentProfile $paymentProfile, Purchase $purchase, &$error = null)
	{
		$this->setupStripe($paymentProfile);
		$metadata = $this->getCustomerMetadata($purchaseRequest);

		$customer = null;

		try
		{
			$customers = \Stripe\Customer::all([
				'limit' => 1,
				'email' => $metadata['email']
			]);

			/** @var \Stripe\Customer $customer */
			$customer = reset($customers->data);
		}
		catch (\Stripe\Error\Base $e) {}

		if (!$customer)
		{
			try
			{
				/** @var \Stripe\Customer $customer */
				$customer = \Stripe\Customer::create([
					'description' => $metadata['username'],
					'email' => $metadata['email'],
					'metadata' => $this->getCustomerMetadata($purchaseRequest)
				]);
			}
			catch (\Stripe\Error\Base $e)
			{
				// failed to create
				$error = $e->getMessage();
				return false;
			}
		}

		return $customer;
	}

	protected function getStripeFormattedCost(PurchaseRequest $purchaseRequest, Purchase $purchase)
	{
		return $this->prepareCost($purchase->cost, $purchase->currency);
	}

	protected function createPaymentIntent(PurchaseRequest $purchaseRequest, Purchase $purchase, &$error = null)
	{
		$paymentProfile = $purchase->paymentProfile;
		$this->setupStripe($paymentProfile);

		$customer = $this->getStripeCustomer(
			$purchaseRequest,
			$paymentProfile,
			$purchase,
			$error
		);
		if (!$customer)
		{
			return false;
		}

		try
		{
			// note: this object must be updated if the amount changes
			$paymentIntent = \Stripe\PaymentIntent::create([
				'amount' => $this->getStripeFormattedCost(
					$purchaseRequest, $purchase
				),
				'currency' => $purchase->currency,
				'customer' => $customer->id,
				'description' => $purchase->title,
				'receipt_email' => $purchaseRequest->User
					? $purchaseRequest->User->email
					: null,
				'statement_descriptor' => $this->getStatementDescriptor(),
				'metadata' => $this->getChargeMetadata($purchaseRequest),
				'setup_future_usage' => $purchase->recurring ? 'off_session' : null
			]);
		}
		catch (\Stripe\Error\Base $e)
		{
			// failed to create
			$error = $e->getMessage();
			return false;
		}

		return $paymentIntent;
	}

	protected function createStripeProduct(PaymentProfile $paymentProfile, Purchase $purchase, &$error = null)
	{
		$this->setupStripe($paymentProfile);
		$productId = $this->getStripeProductAndPlanId($purchase);

		try
		{
			/** @var \Stripe\Product $product */
			$product = \Stripe\Product::retrieve($productId);
		}
		catch (\Stripe\Error\Base $e)
		{
			// likely means no existing product, so lets create it
			try
			{
				/** @var \Stripe\Product $product */
				$product = \Stripe\Product::create([
					'id' => $productId,
					'name' => $purchase->purchasableTitle,
					'type' => 'service',
					'metadata' => $this->getTransactionMetadata($purchase),
					'statement_descriptor' => $this->getStatementDescriptor()
				]);
			}
			catch (\Stripe\Error\Base $e)
			{
				// failed to retrieve, failed to create
				$error = $e->getMessage();
				return false;
			}
		}

		return $product;
	}

	protected function createStripePlan(\Stripe\Product $product, PaymentProfile $paymentProfile, Purchase $purchase, &$error = null)
	{
		$this->setupStripe($paymentProfile);
		$planId = $this->getStripeProductAndPlanId($purchase);

		try
		{
			/** @var \Stripe\Plan $plan */
			$plan = \Stripe\Plan::retrieve($planId);
		}
		catch (\Stripe\Error\Base $e)
		{
			// likely means no existing plan, so lets create it
			try
			{
				/** @var \Stripe\Plan $plan */
				$plan = \Stripe\Plan::create([
					'id' => $planId,
					'currency' => $purchase->currency,
					'amount' => $this->prepareCost($purchase->cost, $purchase->currency),
					'billing_scheme' => 'per_unit',
					'interval' => $purchase->lengthUnit,
					'interval_count' => $purchase->lengthAmount,
					'product' => $product->id,
					'metadata' => $this->getTransactionMetadata($purchase)
				]);
			}
			catch (\Stripe\Error\Base $e)
			{
				// failed to retrieve, failed to create
				$error = $e->getMessage();
				return false;
			}
		}

		return $plan;
	}

	/**
	 * @deprecated use getStripeCustomer instead
	 *
	 * @return bool|\Stripe\Customer|null
	 */
	protected function createStripeCustomer($chargeId, PurchaseRequest $purchaseRequest, PaymentProfile $paymentProfile, Purchase $purchase, &$error = null)
	{
		return $this->getStripeCustomer($purchaseRequest, $paymentProfile, $purchase, $error);
	}

	protected function createStripeSubscription(\Stripe\Customer $customer, \Stripe\Plan $plan, $cardToken, PaymentProfile $paymentProfile, Purchase $purchase, &$error = null)
	{
		$this->setupStripe($paymentProfile);

		try
		{
			/** @var \Stripe\Subscription $subscription */
			$subscription = \Stripe\Subscription::create([
				'customer' => $customer->id,
				'items' => [
					['plan' => $plan->id]
				],
				'trial_end' => strtotime("+ {$purchase->lengthAmount} {$purchase->lengthUnit}"),
				'metadata' => $this->getTransactionMetadata($purchase)
			]);
		}
		catch (\Stripe\Error\Base $e)
		{
			$error = $e->getMessage();
			return false;
		}

		return $subscription;
	}

	public function renderCancellationTemplate(PurchaseRequest $purchaseRequest)
	{
		return $this->renderCancellationDefault($purchaseRequest);
	}

	public function processCancellation(Controller $controller, PurchaseRequest $purchaseRequest, PaymentProfile $paymentProfile)
	{
		if (!$purchaseRequest->provider_metadata || strpos($purchaseRequest->provider_metadata, 'sub_') !== 0)
		{
			$logFinder = \XF::finder('XF:PaymentProviderLog')
				->where('purchase_request_key', $purchaseRequest->request_key)
				->where('provider_id', $this->providerId)
				->order('log_date', 'desc');

			$logs = $logFinder->fetch();

			$subscriberId = null;
			foreach ($logs AS $log)
			{
				if ($log->subscriber_id && strpos($log->subscriber_id, 'sub_') === 0)
				{
					$subscriberId = $log->subscriber_id;
					break;
				}
			}

			if (!$subscriberId)
			{
				return $controller->error(\XF::phrase('could_not_find_subscriber_id_for_this_purchase_request'));
			}
		}
		else
		{
			$subscriberId = $purchaseRequest->provider_metadata;
		}

		$this->setupStripe($paymentProfile);

		try
		{
			/** @var \Stripe\Subscription $subscription */
			$subscription = \Stripe\Subscription::retrieve($subscriberId);
			$cancelledSubscription = $subscription->cancel();

			if ($cancelledSubscription->status != 'canceled')
			{
				throw $controller->exception($controller->error(
					\XF::phrase('this_subscription_cannot_be_cancelled_maybe_already_cancelled')
				));
			}
		}
		catch (\Stripe\Error\Base $e)
		{
			throw $controller->exception($controller->error(
				\XF::phrase('this_subscription_cannot_be_cancelled_maybe_already_cancelled')
			));
		}

		return $controller->redirect(
			$controller->getDynamicRedirect(),
			\XF::phrase('stripe_subscription_cancelled_successfully')
		);
	}

	public function setupCallback(\XF\Http\Request $request)
	{
		$state = new CallbackState();

		$inputRaw = $request->getInputRaw();
		$state->inputRaw = $inputRaw;
		$state->signature = isset($_SERVER['HTTP_STRIPE_SIGNATURE']) ? $_SERVER['HTTP_STRIPE_SIGNATURE'] : null;

		$input = @json_decode($inputRaw, true);
		$filtered = \XF::app()->inputFilterer()->filterArray($input ?: [], [
			'data' => 'array',
			'id' => 'str',
			'type' => 'str'
		]);

		$event = $filtered['data'];

		$state->transactionId = $filtered['id'];
		$state->eventType = $filtered['type'];
		$state->event = isset($event['object']) ? $event['object'] : [];

		if (isset($state->event['metadata']['request_key']))
		{
			$state->requestKey = $state->event['metadata']['request_key'];
		}
		else if (isset($state->event['subscription']) && strpos($state->event['subscription'], 'sub_') === 0)
		{
			$subscriberId = $state->event['subscription'];

			$purchaseRequest = \XF::em()->findOne('XF:PurchaseRequest', ['provider_metadata' => $subscriberId]);

			if ($purchaseRequest)
			{
				$state->purchaseRequest = $purchaseRequest; // sets requestKey too
			}
			else
			{
				// generally for legacy recurring payments where the metadata doesn't contain the subscriber ID

				$logFinder = \XF::finder('XF:PaymentProviderLog')
					->where('subscriber_id', $subscriberId)
					->where('provider_id', $this->providerId)
					->order('log_date', 'desc');

				$logs = $logFinder->fetch();

				$subscriberId = null;
				foreach ($logs AS $log)
				{
					if ($log->purchase_request_key)
					{
						$state->requestKey = $log->purchase_request_key; // sets purchaseRequest too
						break;
					}
				}
			}

			$state->subscriberId = $subscriberId;
		}
		else if (isset($state->event['object']) && $state->event['object'] == 'review')
		{
			// reviews don't have a metadata object, but set the charge id
			$chargeId = $state->event['charge'];

			$purchaseRequest = \XF::em()->findOne('XF:PurchaseRequest', ['provider_metadata' => $chargeId]);
			$state->purchaseRequest = $purchaseRequest; // sets request key too
		}
		else if (isset($state->event['object']) && $state->event['object'] == 'charge')
		{
			// generally for legacy one off payments where the charge object metadata doesn't contain the request key
			$chargeId = $state->event['id'];

			$purchaseRequest = \XF::em()->findOne('XF:PurchaseRequest', ['provider_metadata' => $chargeId]);
			$state->purchaseRequest = $purchaseRequest; // sets request key too
		}

		return $state;
	}

	protected function validateExpectedValues(CallbackState $state)
	{
		return ($state->getPurchaseRequest() && $state->getPaymentProfile());
	}

	protected function verifyWebhookSignature(CallbackState $state)
	{
		$paymentProfile = $state->getPaymentProfile();

		if (empty($paymentProfile->options['signing_secret']))
		{
			return true; // not enabled so pass
		}

		if (empty($state->signature))
		{
			return false; // enabled but signature missing so fail
		}

		$secret = $paymentProfile->options['signing_secret'];
		$payload = $state->inputRaw;
		$signature = $state->signature;

		try
		{
			$this->setupStripe($paymentProfile);
			$verifiedEvent = \Stripe\Webhook::constructEvent($payload, $signature, $secret);
		}
		catch(\UnexpectedValueException $e)
		{
			return false;
		}
		catch(\Stripe\Error\SignatureVerification $e)
		{
			return false;
		}

		return $verifiedEvent;
	}

	protected function getActionableEvents()
	{
		// charge.dispute.created doesn't automatically indicate a loss of funds
		// charge.dispute.funds_withdrawn is the indicator for that, so we will ignore creation.
		return [
			'charge.dispute.funds_withdrawn',
			'charge.dispute.funds_reinstated',
			'charge.refunded',
			'charge.succeeded',
			'invoice.payment_succeeded',
			'review.closed'
		];
	}

	protected function isEventSkippable(CallbackState $state)
	{
		$eventType = $state->eventType;

		if (!in_array($eventType, $this->getActionableEvents()))
		{
			return true;
		}

		if ($eventType === 'invoice.payment_succeeded' && (array_key_exists('charge', $state->event) && $state->event['charge'] === null))
		{
			// no charge associated so we already charged in a separate transaction
			// this is likely the initial invoice payment so we can skip this.
			return true;
		}

		return false;
	}

	public function validateCallback(CallbackState $state)
	{
		if ($this->isEventSkippable($state))
		{
			// Stripe sends a lot of webhooks, we shouldn't log them.
			// They are viewable verbosely in the Stripe Dashboard.
			$state->httpCode = 200;
			return false;
		}

		if (!$this->validateExpectedValues($state))
		{
			$state->logType = 'error';
			$state->logMessage = 'Event data received from Stripe does not contain the expected values.';
			if (!$state->requestKey)
			{
				$state->httpCode = 200; // Not likely to recover from this error so send a successful response.
			}
			return false;
		}

		if (!$this->verifyWebhookSignature($state))
		{
			$state->logType = 'error';
			$state->logMessage = 'Webhook received from Stripe could not be verified as being valid.';
			$state->httpCode = 400;

			return false;
		}

		return true;
	}

	public function validateCost(CallbackState $state)
	{
		$purchaseRequest = $state->getPurchaseRequest();

		$currency = $purchaseRequest->cost_currency;
		$cost = $this->prepareCost($purchaseRequest->cost_amount, $currency);

		$amountPaid = null;

		switch ($state->eventType)
		{
			case 'charge.succeeded':
				$amountPaid = $state->event['amount'];
				break;
			case 'invoice.payment_succeeded':
				$amountPaid = $state->event['amount_paid'];
				break;
		}

		if ($amountPaid !== null)
		{
			$costValidated = (
				$amountPaid === $cost
				&& strtoupper($state->event['currency']) === $currency
			);

			if (!$costValidated)
			{
				$state->logType = 'error';
				$state->logMessage = 'Invalid cost amount';
				return false;
			}

			return true;
		}

		return true;
	}

	public function getPaymentResult(CallbackState $state)
	{
		switch ($state->eventType)
		{
			case 'charge.succeeded':
				$state->paymentResult = CallbackState::PAYMENT_RECEIVED;

				// sleep for 5 seconds to offset an insanely fast webhook
				// being processed before the user is redirected
				// TODO: consider a different (better) approach
				usleep(5 * 1000000);

				$purchaseRequest = $state->purchaseRequest;

				if ($purchaseRequest
					&& $purchaseRequest->provider_metadata
					&& strpos($purchaseRequest->provider_metadata, 'sub_') === 0
					&& !empty($state->event['payment_method'])
				)
				{
					try
					{
						/** @var \Stripe\Subscription $subscription */
						$subscription = \Stripe\Subscription::retrieve(
							$purchaseRequest->provider_metadata
						);

						/** @var \Stripe\PaymentMethod $paymentMethod */
						$paymentMethod = \Stripe\PaymentMethod::retrieve(
							$state->event['payment_method']
						);

						\Stripe\Subscription::update($subscription->id, [
							'default_payment_method' => $paymentMethod->id
						]);
					}
					catch (\Stripe\Error\Base $e) {}
				}

				break;

			case 'invoice.payment_succeeded':
				$state->paymentResult = CallbackState::PAYMENT_RECEIVED;
				break;

			case 'review.closed':
				if ($state->event['reason'] == 'approved')
				{
					$state->paymentResult = CallbackState::PAYMENT_RECEIVED;
				}
				else
				{
					$state->logType = 'info';
					$state->logMessage = 'Previous payment review now closed, but not approved.';
				}
				break;

			case 'charge.refunded':
			case 'charge.dispute.funds_withdrawn':
				$state->paymentResult = CallbackState::PAYMENT_REVERSED;
				break;

			case 'charge.dispute.funds_reinstated':
				$state->paymentResult = CallbackState::PAYMENT_REINSTATED;
				break;
		}
	}

	public function prepareLogData(CallbackState $state)
	{
		$state->logDetails = $state->event;
		$state->logDetails['eventType'] = $state->eventType;
	}

	protected $supportedCurrencies = [
		'AED', 'AFN', 'ALL', 'AMD', 'AOA',
		'ARS', 'AUD', 'AWG', 'AZN', 'BAM',
		'BBD', 'BDT', 'BGN', 'BIF', 'BMD',
		'BND', 'BOB', 'BRL', 'BWP', 'BZD',
		'CAD', 'CDF', 'CHF', 'CLP', 'CNY',
		'COP', 'CRC', 'CVE', 'CZK', 'DJF',
		'DKK', 'DOP', 'DZD', 'EGP', 'ETB',
		'EUR', 'GBP', 'GEL', 'GNF', 'GTQ',
		'GYD', 'HKD', 'HNL', 'HRK', 'HUF',
		'IDR', 'ILS', 'INR', 'ISK', 'JMD',
		'JPY', 'KES', 'KHR', 'KMF', 'KRW',
		'KZT', 'LBP', 'LKR', 'LRD', 'MAD',
		'MDL', 'MGA', 'MKD', 'MOP', 'MUR',
		'MXN', 'MYR', 'MZN', 'NAD', 'NGN',
		'NIO', 'NOK', 'NPR', 'NZD', 'PAB',
		'PEN', 'PHP', 'PKR', 'PLN', 'PYG',
		'QAR', 'RON', 'RSD', 'RUB', 'RWF',
		'SAR', 'SEK', 'SGD', 'SOS', 'STD',
		'THB', 'TOP', 'TRY', 'TTD', 'TWD',
		'TZS', 'UAH', 'UGX', 'USD', 'UYU',
		'UZS', 'VND', 'XAF', 'XOF', 'ZAR'
	];

	/**
	 * List of zero-decimal currencies as defined by Stripe's documentation. If we're dealing with one of these,
	 * this is already the smallest currency unit, and can be passed as-is. Otherwise convert it.
	 *
	 * @var array
	 */
	protected $zeroDecimalCurrencies = [
		'BIF', 'CLP', 'DJF', 'GNF', 'JPY',
		'KMF', 'KRW', 'MGA', 'PYG', 'RWF',
		'VND', 'VUV', 'XAF', 'XOF', 'XPF'
	];

	/**
	 * Given a cost and a currency, this will return the cost as an integer converted to the smallest currency unit.
	 *
	 * @param $cost
	 * @param $currency
	 *
	 * @return int
	 */
	protected function prepareCost($cost, $currency)
	{
		if (!in_array($currency, $this->zeroDecimalCurrencies))
		{
			$cost *= 100;
		}
		return intval($cost);
	}

	public function verifyCurrency(PaymentProfile $paymentProfile, $currencyCode)
	{
		return (in_array($currencyCode, $this->supportedCurrencies));
	}
}