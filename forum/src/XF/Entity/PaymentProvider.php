<?php

namespace XF\Entity;

use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Structure;

/**
 * COLUMNS
 * @property string provider_id
 * @property string provider_class
 * @property string addon_id
 *
 * GETTERS
 * @property string title
 * @property \XF\Payment\AbstractProvider|null handler
 */
class PaymentProvider extends Entity
{
	/**
	 * @return string
	 */
	public function getTitle()
	{
		$handler = $this->handler;
		return $handler ? $handler->getTitle() : '';
	}

	public function renderConfig(PaymentProfile $profile)
	{
		$handler = $this->handler;
		if (!$handler)
		{
			return '';
		}
		return $handler->renderConfig($profile);
	}

	public function renderCancellation(UserUpgradeActive $active)
	{
		$handler = $this->handler;
		if (!$handler || !$active->PurchaseRequest)
		{
			return '';
		}
		return $handler->renderCancellationTemplate($active->PurchaseRequest);
	}

	/**
	 * @return \XF\Payment\AbstractProvider|null
	 */
	public function getHandler()
	{
		$class = \XF::stringToClass($this->provider_class, '%s\Payment\%s');
		if (!class_exists($class))
		{
			return null;
		}

		$class = \XF::extendClass($class);
		return new $class($this->provider_id);
	}

	public static function getStructure(Structure $structure)
	{
		$structure->table = 'xf_payment_provider';
		$structure->shortName = 'XF:PaymentProvider';
		$structure->primaryKey = 'provider_id';
		$structure->columns = [
			'provider_id' => ['type' => self::STR, 'maxLength' => 25, 'match' => 'alphanumeric', 'required' => true],
			'provider_class' => ['type' => self::STR, 'maxLength' => 100, 'required' => true],
			'addon_id' => ['type' => self::BINARY, 'maxLength' => 50, 'default' => '']
		];
		$structure->getters = [
			'title' => false,
			'handler' => true
		];
		$structure->relations = [];

		return $structure;
	}
}