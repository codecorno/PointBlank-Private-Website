<?php

namespace XF\Entity;

use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Structure;

/**
 * COLUMNS
 * @property int|null provider_log_id
 * @property string|null purchase_request_key
 * @property string provider_id
 * @property string|null transaction_id
 * @property string|null subscriber_id
 * @property string log_type
 * @property string log_message
 * @property string log_details_
 * @property int log_date
 *
 * GETTERS
 * @property array log_details
 *
 * RELATIONS
 * @property \XF\Entity\PaymentProvider Provider
 * @property \XF\Entity\PurchaseRequest PurchaseRequest
 */
class PaymentProviderLog extends Entity
{
	/**
	 * @return array
	 */
	public function getLogDetails()
	{
		$value = $this->log_details_;
		if (substr($value, 0, 2) == 'a:')
		{
			// pre-2.0 version stored serialized (as well as logs with invalid UTF-8)
			$details = \XF\Util\Php::safeUnserialize($value);
			if (!is_array($details))
			{
				$details = [];
			}
		}
		else
		{
			$details = json_decode($value, true);
		}
		return $details;
	}

	public function verifyLogDetails(&$value)
	{
		$newValue = json_encode($value);
		if (json_last_error() == JSON_ERROR_NONE)
		{
			$value = $newValue;
		}
		else
		{
			// likely a UTF-8 error
			$value = serialize($value);
		}

		return true;
	}

	public static function getStructure(Structure $structure)
	{
		$structure->table = 'xf_payment_provider_log';
		$structure->shortName = 'XF:PaymentProviderLog';
		$structure->primaryKey = 'provider_log_id';
		$structure->columns = [
			'provider_log_id' => ['type' => self::UINT, 'autoIncrement' => true, 'nullable' => true],
			'purchase_request_key' => ['type' => self::STR, 'maxLength' => 32, 'nullable' => true],
			'provider_id' => ['type' => self::STR, 'max' => 25, 'required' => true],
			'transaction_id' => ['type' => self::STR, 'maxLength' => 100, 'nullable' => true],
			'subscriber_id' => ['type' => self::STR, 'maxLength' => 100, 'nullable' => true],
			'log_type' => ['type' => self::STR, 'required' => true,
				'allowedValues' => ['payment', 'cancel', 'info', 'error']
			],
			'log_message' => ['type' => self::STR, 'maxLength' => 255, 'forced' => true, 'default' => ''],
			'log_details' => ['type' => self::BINARY, 'default' => []],
			'log_date' => ['type' => self::UINT, 'default' => \XF::$time],
		];
		$structure->getters = [
			'log_details' => true
		];
		$structure->relations = [
			'Provider' => [
				'entity' => 'XF:PaymentProvider',
				'type' => self::TO_ONE,
				'conditions' => 'provider_id',
				'primary' => true
			],
			'PurchaseRequest' => [
				'entity' => 'XF:PurchaseRequest',
				'type' => self::TO_ONE,
				'conditions' => [
					['request_key', '=', '$purchase_request_key']
				]
			]
		];

		return $structure;
	}
}