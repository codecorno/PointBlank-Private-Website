<?php

namespace XF\Repository;

use XF\Mvc\Entity\Finder;
use XF\Mvc\Entity\Repository;

class Payment extends Repository
{
	/**
	 * @return Finder
	 */
	public function findPaymentProvidersForList()
	{
		return $this->finder('XF:PaymentProvider')
			->order('provider_id');
	}

	/**
	 * @return Finder
	 */
	public function findPaymentProfilesForList()
	{
		return $this->finder('XF:PaymentProfile')
			->with('Provider', true)
			->where('active', true)
			->order('title');
	}

	public function getPaymentProfileTitlePairs()
	{
		return $this->findPaymentProfilesForList()
			->fetch()
			->pluck(function(\XF\Entity\PaymentProfile $profile)
			{
				return [
					$profile->payment_profile_id,
					$profile->display_title ?: $profile->title
				];
			}, false);
	}

	public function getPaymentProfileOptionsData($includeEmpty = true)
	{
		$choices = [];
		if ($includeEmpty)
		{
			$choices = [
				0 => ['value' => 0, 'label' => \XF::phrase('(choose_payment_method)')]
			];
		}

		$choices += $this->getPaymentProfileTitlePairs();

		return $choices;
	}

	/**
	 * @param $transactionId
	 *
	 * @return Finder
	 */
	public function findLogsByTransactionId($transactionId, $logType = ['payment', 'cancel'])
	{
		return $this->finder('XF:PaymentProviderLog')
			->where('transaction_id', $transactionId)
			->where('log_type', $logType)
			->setDefaultOrder('log_date');
	}

	public function logCallback($requestKey, $providerId, $txnId, $logType, $logMessage, array $logDetails, $subId = null)
	{
		/** @var \XF\Entity\PaymentProviderLog $providerLog */
		$providerLog = $this->em->create('XF:PaymentProviderLog');

		if (strlen($requestKey) > 32)
		{
			$requestKey = substr($requestKey, 0, 29) . '...';
		}

		$providerLog->purchase_request_key = $requestKey;
		$providerLog->provider_id = $providerId;
		$providerLog->transaction_id = $txnId;
		$providerLog->log_type = $logType;
		$providerLog->log_message = $logMessage;
		$providerLog->log_details = $logDetails;
		$providerLog->subscriber_id = $subId;
		$providerLog->log_date = time();

		return $providerLog->save();
	}
}
