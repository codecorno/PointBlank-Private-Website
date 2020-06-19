<?php

namespace XF\Payment;

use XF\Entity\PaymentProfile;
use XF\Entity\PurchaseRequest;
use XF\Mvc\Controller;
use XF\Purchasable\Purchase;
use XF\Util\Arr;

class PayPal extends AbstractProvider
{
	public function getTitle()
	{
		return 'PayPal';
	}

	public function getApiEndpoint()
	{
		if (\XF::config('enableLivePayments'))
		{
			return 'https://www.paypal.com/cgi-bin/webscr';
		}
		else
		{
			return 'https://www.sandbox.paypal.com/cgi-bin/webscr';
		}
	}

	public function verifyConfig(array &$options, &$errors = [])
	{
		if (empty($options['primary_account']))
		{
			$errors[] = \XF::phrase('you_must_provide_primary_paypal_account_email_to_set_up_this_payment');
			return false;
		}

		$emails = [];
		if (!empty($options['alternate_accounts']))
		{
			$emails = Arr::stringToArray($options['alternate_accounts'], '#\r?\n#');
		}
		$emails[] = $options['primary_account'];

		$validator = \XF::app()->validator('Email');
		foreach ($emails AS $email)
		{
			if (!$validator->isValid($email))
			{
				$errors[] = \XF::phrase('value_x_is_not_valid_email_address', ['email' => $email]);
			}
		}

		if ($errors)
		{
			return false;
		}

		return true;
	}

	protected function getPaymentParams(PurchaseRequest $purchaseRequest, Purchase $purchase)
	{
		$paymentProfile = $purchase->paymentProfile;
		$purchaser = $purchase->purchaser;

		$params = [
			'business' => $paymentProfile->options['primary_account'],
			'currency_code' => $purchase->currency,
			'item_name' => $purchase->title,
			'quantity' => 1,
			'no_note' => 1,

			// 2 = required, 1 = not required, 0 = optional
			'no_shipping' => !empty($paymentProfile->options['require_address']) ? 2 : 1,

			'custom' => $purchaseRequest->request_key,

			'charset' => 'utf8',
			'email' => $purchaser->email,

			'return' => $purchase->returnUrl,
			'cancel_return' => $purchase->cancelUrl,
			'notify_url' => $this->getCallbackUrl()
		];
		if ($purchase->recurring)
		{
			$params['cmd'] = '_xclick-subscriptions';
			$params['a3'] = $purchase->cost;
			$params['p3'] = $purchase->lengthAmount;

			switch ($purchase->lengthUnit)
			{
				case 'day': $params['t3'] = 'D'; break;
				case 'week': $params['t3'] = 'W'; break;
				case 'month': $params['t3'] = 'M'; break;
				case 'year': $params['t3'] = 'Y'; break;
				default: $params['t3'] = ''; break;
			}

			$params['src'] = 1;
			$params['sra'] = 1;
		}
		else
		{
			$params['cmd'] = '_xclick';
			$params['amount'] = $purchase->cost;
		}

		return $params;
	}

	public function initiatePayment(Controller $controller, PurchaseRequest $purchaseRequest, Purchase $purchase)
	{
		$params = $this->getPaymentParams($purchaseRequest, $purchase);

		$endpointUrl = $this->getApiEndpoint();
		$endpointUrl .= '?' . http_build_query($params);
		return $controller->redirect($endpointUrl, '');
	}

	public function renderCancellationTemplate(PurchaseRequest $purchaseRequest)
	{
		$data = [
			'purchaseRequest' => $purchaseRequest,
			'endpoint' => $this->getApiEndpoint()
		];
		return \XF::app()->templater()->renderTemplate('public:payment_cancel_recurring_paypal', $data);
	}

	/**
	 * @param \XF\Http\Request $request
	 * 
	 * @return CallbackState
	 */
	public function setupCallback(\XF\Http\Request $request)
	{
		$state = new CallbackState();

		$state->business = $request->filter('business', 'str');
		$state->receiverEmail = $request->filter('receiver_email', 'str');
		$state->transactionType = $request->filter('txn_type', 'str');
		$state->parentTransactionId = $request->filter('parent_txn_id', 'str');
		$state->transactionId = $request->filter('txn_id', 'str');
		$state->subscriberId = $request->filter('subscr_id', 'str');
		$state->paymentCountry = $request->filter('residence_country', 'str');
		$state->costAmount = $request->filter('mc_gross', 'unum');
		$state->taxAmount = $request->filter('tax', 'unum');
		$state->costCurrency = $request->filter('mc_currency', 'str');
		$state->paymentStatus = $request->filter('payment_status', 'str');

		$state->requestKey = $request->filter('custom', 'str');
		$state->testMode = $request->filter('test_ipn', 'bool');

		$state->ip = $request->getIp();
		$state->_POST = $_POST;

		return $state;
	}

	public function validateCallback(CallbackState $state)
	{
		try
		{
			$params = ['form_params' => $state->_POST + ['cmd' => '_notify-validate']];
			$client = \XF::app()->http()->client();
			if ($state->testMode)
			{
				$url = 'https://ipnpb.sandbox.paypal.com/cgi-bin/webscr';
			}
			else
			{
				$url = 'https://ipnpb.paypal.com/cgi-bin/webscr';
			}
			$response = $client->post($url, $params);
			if (!$response || $response->getBody()->getContents() != 'VERIFIED' || $response->getStatusCode() != 200)
			{
				$host = \XF\Util\Ip::getHost($state->ip);
				if (preg_match('#(^|\.)paypal.com$#i', $host))
				{
					$state->logType = 'error';
					$state->logMessage = 'Request not validated';
				}
				else
				{
					$state->logType = false;
					$state->logMessage = 'Request not validated (from unknown source)';
				}
				return false;
			}
		}
		catch (\GuzzleHttp\Exception\RequestException $e)
		{
			$state->logType = 'error';
			$state->logMessage = 'Connection to PayPal failed: ' . $e->getMessage();
			return false;
		}

		return true;
	}

	public function validateTransaction(CallbackState $state)
	{
		if (!$state->requestKey)
		{
			$state->logType = 'info';
			$state->logMessage = 'No purchase request key. Unrelated payment, no action to take.';
			return false;
		}

		if ($state->legacy)
		{
			// The custom data in legacy calls is <user_id>,<user_upgrade_id>,<validation_type>,<validation>.
			// We only need the user_id and user_upgrade_id but we can at least verify it's a familiar format.
			if (!preg_match(
				'/^(?P<user_id>\d+),(?P<user_upgrade_id>\d+),token,.*$/',
				$state->requestKey,
				$itemParts)
				&& count($itemParts) !== 3 // full match + 2 groups
			)
			{
				$state->logType = 'info';
				$state->logMessage = 'Invalid custom field. Unrelated payment, no action to take.';
				return false;
			}

			$user = \XF::em()->find('XF:User', $itemParts['user_id']);
			if (!$user)
			{
				$state->logType = 'error';
				$state->logMessage = 'Could not find user with user_id ' . $itemParts['user_id'] . '.';
				return false;
			}
			$state->purchaser = $user;

			$state->userUpgrade = \XF::em()->find('XF:UserUpgrade', $itemParts['user_upgrade_id']);
			if (!$state->userUpgrade)
			{
				$state->logType = 'error';
				$state->logMessage = 'Could not find user upgrade with user_upgrade_id ' . $itemParts['user_upgrade_id'] . '.';
				return false;
			}
		}
		else
		{
			if (!$state->getPurchaseRequest())
			{
				$state->logType = 'info';
				$state->logMessage = 'Invalid request key. Unrelated payment, no action to take.';
				return false;
			}
		}

		if (!$state->transactionId && !$state->subscriberId)
		{
			$state->logType = 'info';
			$state->logMessage = 'No transaction or subscriber ID. No action to take.';
			return false;
		}

		$paymentRepo = \XF::repository('XF:Payment');
		$matchingLogsFinder = $paymentRepo->findLogsByTransactionId($state->transactionId);
		if ($matchingLogsFinder->total())
		{
			$logs = $matchingLogsFinder->fetch();
			foreach ($logs AS $log)
			{
				if ($log->log_type == 'cancel' && $state->paymentStatus == 'Canceled_Reversal')
				{
					// This is a cancelled transaction we've already seen, but has now been reversed.
					// Let it go through.
					return true;
				}
			}

			$state->logType = 'info';
			$state->logMessage = 'Transaction already processed. Skipping.';
			return false;
		}

		return true;
	}

	public function validatePurchaseRequest(CallbackState $state)
	{
		// validated in validateTransaction
		return true;
	}

	public function validatePurchasableHandler(CallbackState $state)
	{
		if ($state->legacy)
		{
			$purchasable = \XF::em()->find('XF:Purchasable', 'user_upgrade');
			$state->purchasableHandler = $purchasable->handler;
		}
		return parent::validatePurchasableHandler($state);
	}

	public function validatePaymentProfile(CallbackState $state)
	{
		if ($state->legacy)
		{
			$finder = \XF::finder('XF:PaymentProfile')
				->where('provider_id', 'paypal');
			foreach ($finder->fetch() AS $profile)
			{
				if (!empty($profile->options['legacy']))
				{
					$state->paymentProfile = $profile;
					break;
				}
			}
		}
		return parent::validatePaymentProfile($state);
	}

	public function validatePurchaser(CallbackState $state)
	{
		if ($state->legacy)
		{
			// validated in validateTransaction
			return true;
		}
		else
		{
			return parent::validatePurchaser($state);
		}
	}

	public function validatePurchasableData(CallbackState $state)
	{
		$paymentProfile = $state->getPaymentProfile();

		$business = strtolower($state->business);
		$receiverEmail = strtolower($state->receiverEmail);

		$options = $paymentProfile->options;
		$accounts = Arr::stringToArray($options['alternate_accounts'], '#\r?\n#');
		$accounts[] = $options['primary_account'];

		$matched = false;
		foreach ($accounts AS $account)
		{
			$account = trim(strtolower($account));
			if ($account && ($business == $account || $receiverEmail == $account))
			{
				$matched = true;
				break;
			}
		}
		if (!$matched)
		{
			$state->logType = 'error';
			$state->logMessage = 'Invalid business or receiver_email.';
			return false;
		}

		return true;
	}

	public function validateCost(CallbackState $state)
	{
		if ($state->legacy)
		{
			$upgrade = $state->userUpgrade;

			$upgradeRecord = \XF::em()->findOne('XF:UserUpgradeActive', [
				'user_upgrade_id' => $upgrade->user_upgrade_id,
				'user_id' => $state->purchaser->user_id
			]);

			if (!$upgradeRecord && $state->subscriberId)
			{
				$logFinder = \XF::finder('XF:PaymentProviderLog')
					->where('subscriber_id', $state->subscriberId)
					->order('log_date', 'DESC');
				
				foreach ($logFinder->fetch() AS $log)
				{
					if (is_numeric($log->purchase_request_key))
					{
						$upgradeRecord = \XF::em()->find('XF:UserUpgradeExpired', $log->purchase_request_key);
						if ($upgradeRecord)
						{
							$state->userUpgradeRecordId = $upgradeRecord->user_upgrade_record_id;
							break;
						}
					}
				}
			}

			if (!$upgradeRecord && $state->parentTransactionId)
			{
				$logFinder = \XF::finder('XF:PaymentProviderLog')
					->where('transaction_id', $state->parentTransactionId)
					->order('log_date', 'DESC');

				foreach ($logFinder->fetch() AS $log)
				{
					if (is_numeric($log->purchase_request_key))
					{
						$upgradeRecord = \XF::em()->find('XF:UserUpgradeExpired', $log->purchase_request_key);
						if ($upgradeRecord)
						{
							$state->userUpgradeRecordId = $upgradeRecord->user_upgrade_record_id;
							break;
						}
					}
				}
			}

			$cost = $upgrade->cost_amount;
			$currency = $upgrade->cost_currency;
		}
		else
		{
			$upgradeRecord = false;
			$purchaseRequest = $state->getPurchaseRequest();
			$cost = $purchaseRequest->cost_amount;
			$currency = $purchaseRequest->cost_currency;
		}

		switch ($state->transactionType)
		{
			case 'web_accept':
			case 'subscr_payment':
				$costValidated = (
					round(($state->costAmount - $state->taxAmount), 2) == round($cost, 2)
					&& $state->costCurrency == $currency
				);

				if ($state->legacy && !$costValidated && $upgradeRecord && $upgradeRecord->extra)
				{
					$cost = $upgradeRecord->extra['cost_amount'];
					$currency = $upgradeRecord->extra['cost_currency'];

					$costValidated = (
						round(($state->costAmount - $state->taxAmount), 2) == round($cost, 2)
						&& $state->costCurrency == strtoupper($currency)
					);
					if ($costValidated)
					{
						// the upgrade's cost has changed, but we need to continue as if it hasn't
						$state->extraData = [
							'cost_amount' => round($state->costAmount, 2),
							'cost_currency' => $state->costCurrency
						];
					}
				}

				if (!$costValidated)
				{
					$state->logType = 'error';
					$state->logMessage = 'Invalid cost amount';
					return false;
				}
		}
		return true;
	}

	public function getPaymentResult(CallbackState $state)
	{
		switch ($state->transactionType)
		{
			case 'web_accept':
			case 'subscr_payment':
				if ($state->paymentStatus == 'Completed')
				{
					$state->paymentResult = CallbackState::PAYMENT_RECEIVED;
				}
				break;
		}

		if ($state->paymentStatus == 'Refunded' || $state->paymentStatus == 'Reversed')
		{
			$state->paymentResult = CallbackState::PAYMENT_REVERSED;
		}
		else if ($state->paymentStatus == 'Canceled_Reversal')
		{
			$state->paymentResult = CallbackState::PAYMENT_REINSTATED;
		}
	}

	public function prepareLogData(CallbackState $state)
	{
		$state->logDetails = $state->_POST;
	}

	protected function getSupportedRecurrenceRanges()
	{
		return [
			'day' => [1, 90],
			'week' => [1, 52],
			'month' => [1, 24],
			'year' => [1, 5]
		];
	}
}