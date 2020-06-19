<?php

namespace XF\Purchasable;

/**
 * @property string $title
 * @property string $description
 * @property float $cost
 * @property string $currency
 * @property bool $recurring
 * @property int $lengthAmount
 * @property string $lengthUnit
 * @property \XF\Entity\User $purchaser
 * @property \XF\Entity\PaymentProfile $paymentProfile
 * @property string $purchasableTypeId
 * @property string $purchasableId
 * @property string $purchasableTitle
 * @property array $extraData
 * @property string $cancelUrl
 * @property string $returnUrl
 * @property string $requestKey
 */
class Purchase implements \ArrayAccess
{
	protected $title;

	protected $description;

	protected $cost;

	protected $currency;

	protected $recurring = false;

	protected $lengthAmount;

	protected $lengthUnit;

	protected $purchaser;

	protected $paymentProfile;

	protected $purchasableTypeId;

	protected $purchasableId;

	protected $purchasableTitle;

	protected $extraData = [];

	protected $returnUrl;

	protected $cancelUrl;

	protected $requestKey;

	public function __get($key)
	{
		if (property_exists($this, $key))
		{
			return $this->{$key};
		}
		else
		{
			throw new \InvalidArgumentException("Unknown purchase object field '$key'");
		}
	}

	public function __set($key, $value)
	{
		if (property_exists($this, $key))
		{
			$this->{$key} = $value;
		}
		else
		{
			throw new \InvalidArgumentException("Unknown purchase field '$key'");
		}
	}

	public function offsetExists($offset)
	{
		return property_exists($this, $offset);
	}

	public function offsetGet($offset)
	{
		return $this->__get($offset);
	}

	public function offsetSet($offset, $value)
	{
		$this->__set($offset, $value);
	}

	public function offsetUnset($offset)
	{
		throw new \LogicException('Offsets cannot be unset from the purchase object');
	}
}