<?php

namespace XF\Entity;

use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Structure;

/**
 * COLUMNS
 * @property int|null payment_profile_id
 * @property string provider_id
 * @property string title
 * @property string display_title
 * @property array options
 * @property bool active
 *
 * RELATIONS
 * @property \XF\Entity\PaymentProvider Provider
 */
class PaymentProfile extends Entity
{
	public function supportsRecurring($unit, $amount, &$result = null)
	{
		$handler = $this->getPaymentHandler();
		return $handler ? $handler->supportsRecurring($this, $unit, $amount, $result) : false;
	}

	public function verifyCurrency($currencyCode)
	{
		$handler = $this->getPaymentHandler();
		return $handler ? $handler->verifyCurrency($this, $currencyCode) : false;
	}

	public function getPaymentHandler()
	{
		return $this->Provider ? $this->Provider->handler : null;
	}

	protected function _preDelete()
	{
		throw new \LogicException('Payment profiles cannot be deleted. Doing so would invalidate existing purchases.');
	}

	public static function getStructure(Structure $structure)
	{
		$structure->table = 'xf_payment_profile';
		$structure->shortName = 'XF:PaymentProfile';
		$structure->primaryKey = 'payment_profile_id';
		$structure->columns = [
			'payment_profile_id' => ['type' => self::UINT, 'autoIncrement' => true, 'nullable' => true],
			'provider_id' => ['type' => self::STR, 'maxLength' => 25, 'required' => true],
			'title' => ['type' => self::STR, 'maxLength' => 100,
				'required' => 'please_enter_valid_title'
			],
			'display_title' => ['type' => self::STR, 'maxLength' => 100, 'default' => ''],
			'options' => ['type' => self::JSON_ARRAY, 'default' => []],
			'active' => ['type' => self::BOOL, 'default' => true]
		];
		$structure->getters = [];
		$structure->relations = [
			'Provider' => [
				'entity' => 'XF:PaymentProvider',
				'type' => self::TO_ONE,
				'conditions' => 'provider_id',
				'primary' => true
			]
		];
		$structure->defaultWith = 'Provider';

		return $structure;
	}
}