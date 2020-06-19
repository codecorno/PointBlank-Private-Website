<?php

namespace XF\Payment;

use XF\Entity\PaymentProfile;
use XF\Entity\PurchaseRequest;
use XF\Mvc\Controller;
use XF\Purchasable\Purchase;

class TwoCheckout extends AbstractProvider
{
	public function getTitle()
	{
		return '2Checkout';
	}

	public function getApiEndpoint()
	{
		if (\XF::config('enableLivePayments'))
		{
			return 'https://2checkout.com/api';
		}
		else
		{
			return 'https://sandbox.2checkout.com/api';
		}
	}

	public function verifyConfig(array &$options, &$errors = [])
	{
		if (!$options['account_number'] || !$options['secret_word'])
		{
			$errors[] = \XF::phrase('you_must_provide_account_number_and_secret_word_to_set_up_this_profile');
			return false;
		}
		return true;
	}

	protected function getPaymentParams(PurchaseRequest $purchaseRequest, Purchase $purchase)
	{
		return [
			'purchaseRequest' => $purchaseRequest,
			'paymentProfile' => $purchase->paymentProfile,
			'purchaser' => $purchase->purchaser,
			'purchase' => $purchase,
			'purchasableTypeId' => $purchase->purchasableTypeId,
			'purchasableId' => $purchase->purchasableId,
			'recurrence' => $purchase->recurring ? ($purchase->lengthAmount . ' ' . ucfirst($purchase->lengthUnit)) : false
		];
	}

	public function initiatePayment(Controller $controller, PurchaseRequest $purchaseRequest, Purchase $purchase)
	{
		$viewParams = $this->getPaymentParams($purchaseRequest, $purchase);
		return $controller->view('XF:Purchase\TwoCheckoutInitiate', 'payment_initiate_twocheckout', $viewParams);
	}

	public function processPayment(Controller $controller, PurchaseRequest $purchaseRequest, PaymentProfile $paymentProfile, Purchase $purchase)
	{
		$input = $controller->filter([
			'sid' => 'str',
			'key' => 'str',
			'order_number' => 'str',
			'credit_card_processed' => 'str',
			'merchant_order_id' => 'str',
			'demo' => 'str'
		]);
		$options = $paymentProfile->options;

		$md5Hash = strtoupper(md5(
			$options['secret_word'] .
			$options['account_number'] .
			($input['demo'] === 'Y' ? 1 : $input['order_number']) .
			$purchaseRequest->cost_amount
		));

		if ($md5Hash !== $input['key'])
		{
			throw $controller->exception($controller->error('Could not verify 2Checkout hash.'));
		}

		if ($input['credit_card_processed'] === 'Y')
		{
			$state = new CallbackState();
			$state->transactionId = $input['key'];
			$state->paymentResult = CallbackState::PAYMENT_RECEIVED;
			$state->purchaseRequest = $purchaseRequest;
			$state->paymentProfile = $paymentProfile;

			// If this is a recurring purchase, and an API username/password have been set in the profile
			// then we need to grab the line item IDs and store them as the subscriber ID.
			// At such a time that the user may wish to cancel their subscription, we can
			// cancel the recurring payment for each line item.
			if ($purchase->recurring && $options['api_username'] && $options['api_password'])
			{
				try
				{
					$client = \XF::app()->http()->client();

					$saleResponse = $client->get($this->getApiEndpoint() . '/sales/detail_sale', [
						'headers' => ['Accept' => 'application/json'],
						'auth' => [$options['api_username'], $options['api_password']],
						'query' => ['sale_id' => $input['order_number']]
					]);
					$sale = \GuzzleHttp\json_decode($saleResponse->getBody()->getContents(), true);

					$lineItemIds = [];
					if (isset($sale['sale']['invoices']) && is_array($sale['sale']['invoices']))
					{
						foreach ($sale['sale']['invoices'] AS $invoice)
						{
							foreach ($invoice['lineitems'] AS $lineItem)
							{
								$lineItemIds[] = $lineItem['lineitem_id'];
							}
						}
					}
					$purchaseRequest->provider_metadata = \GuzzleHttp\json_encode($lineItemIds);
					$purchaseRequest->save(false);
				}
				catch (\GuzzleHttp\Exception\RequestException $e) {}
			}

			$this->completeTransaction($state);

			$this->log($state);
		}

		return $controller->redirect($purchase->returnUrl, '');
	}

	public function renderCancellationTemplate(PurchaseRequest $purchaseRequest)
	{
		return $this->renderCancellationDefault($purchaseRequest);
	}

	public function processCancellation(Controller $controller, PurchaseRequest $purchaseRequest, PaymentProfile $paymentProfile)
	{
		$lineItemIds = \GuzzleHttp\json_decode($purchaseRequest->provider_metadata, true);

		if (!$lineItemIds)
		{
			return $controller->error('This purchase cannot be cancelled.');
		}

		$hasError = false;
		$options = $paymentProfile->options;

		$client = \XF::app()->http()->client();

		try
		{
			foreach ($lineItemIds AS $lineItemId)
			{
				$cancelResponse = $client->post($this->getApiEndpoint() . '/sales/stop_lineitem_recurring', [
					'headers' => ['Accept' => 'application/json'],
					'auth' => [$options['api_username'], $options['api_password']],
					'form_params' => [
						'vendor_id' => $options['account_number'],
						'lineitem_id' => $lineItemId
					]
				]);
				$cancellation = \GuzzleHttp\json_decode($cancelResponse->getBody()->getContents(), true);
				if (!$cancellation || $cancellation['response_code'] != 'OK')
				{
					$hasError = true;
				}
			}
		}
		catch (\GuzzleHttp\Exception\RequestException $e) {}

		if ($hasError)
		{
			throw $controller->exception($controller->error('This subscription cannot be cancelled. It may already be cancelled.'));
		}

		return $controller->redirect($controller->getDynamicRedirect(), '2Checkout subscription cancelled successfully');
	}

	public function setupCallback(\XF\Http\Request $request)
	{
		$state = new CallbackState();

		$state->messageType = $request->filter('message_type', 'str');
		$state->timestamp = $request->filter('timestamp', 'str');
		$state->md5Hash = $request->filter('md5_hash', 'str');
		$state->fraudStatus = $request->filter('fraud_status', 'str');
		$state->recurring = $request->filter('recurring', 'bool');
		$state->saleId = $request->filter('sale_id', 'str');
		$state->invoiceId = $request->filter('invoice_id', 'str');

		$state->itemCount = $request->filter('item_count', 'uint');

		$i = 0;
		$total = 0;
		while ($i < $state->itemCount)
		{
			$i++;
			$key = 'item_list_amount_' . $i;
			$itemTotal = $request->filter($key, 'str');
			$state->{$key} = $itemTotal;
			$total += $itemTotal;
		}

		$state->currency = $request->filter('list_currency', 'str');
		$state->cost = $total;

		// Subscription messages are identical, including message ID, even for retries, so concatenate with a hash of the timestamp.
		$state->transactionId = $request->filter('message_id', 'uint') . '_' . md5($state->timestamp);
		$state->requestKey = $request->filter('vendor_order_id', 'str');

		return $state;
	}

	public function validateCallback(CallbackState $state)
	{
		$purchaseRequest = $state->getPurchaseRequest();

		if (!$state->requestKey || !$purchaseRequest)
		{
			$state->logType = 'error';
			$state->logMessage = 'Notification does not contain a recognised purchase request.';
			return false;
		}

		$paymentProfile = $state->getPaymentProfile();
		$options = $paymentProfile->options;

		$md5Hash = strtoupper(md5(
			$state->saleId .
			$options['account_number'] .
			$state->invoiceId .
			$options['secret_word']
		));

		if ($md5Hash !== $state->md5Hash)
		{
			$state->logType = 'error';
			$state->logMessage = 'Could not verify 2Checkout hash.';
			return false;
		}

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

		switch ($state->messageType)
		{
			case 'RECURRING_INSTALLMENT_SUCCESS':
				$costValidated = (
					$state->cost === $purchaseRequest->cost_amount
					&& $state->currency === $purchaseRequest->cost_currency
				);

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
		switch ($state->messageType)
		{
			case 'RECURRING_INSTALLMENT_SUCCESS':
				$state->paymentResult = CallbackState::PAYMENT_RECEIVED;
				break;

			case 'FRAUD_STATUS_CHANGED':
				if ($state->fraudStatus == 'pass')
				{
					$state->paymentResult = CallbackState::PAYMENT_REINSTATED;
				}
				else if ($state->fraudStatus == 'fail' || $state->fraudStatus == 'wait')
				{
					$state->paymentResult = CallbackState::PAYMENT_REVERSED;
				}
				break;

			case 'REFUND_ISSUED':
				$state->paymentResult = CallbackState::PAYMENT_REVERSED;
				break;

			case 'ORDER_CREATED':
				if ($state->fraudStatus == 'fail' || $state->fraudStatus == 'wait')
				{
					$state->paymentResult = CallbackState::PAYMENT_REVERSED;
				}
				else
				{
					// We process the purchase on return from payment so ignore unless the fraud status indicates a problem.
				}
				break;
		}
	}

	public function prepareLogData(CallbackState $state)
	{
		$state->logDetails = [
			'_GET' => $_GET,
			'_POST' => $_POST
		];
	}

	public function supportsRecurring(PaymentProfile $paymentProfile, $unit, $amount, &$result = self::ERR_NO_RECURRING)
	{
		// The minimum unit for 2Checkout is 1 week.
		if ($unit == 'day')
		{
			$result = self::ERR_INVALID_RECURRENCE;
			return false;
		}
		return parent::supportsRecurring($paymentProfile, $unit, $amount, $result);
	}
}