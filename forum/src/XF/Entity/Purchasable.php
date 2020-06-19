<?php

namespace XF\Entity;

use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Structure;

/**
 * COLUMNS
 * @property string purchasable_type_id
 * @property string purchasable_class
 * @property string addon_id
 *
 * GETTERS
 * @property mixed|string title
 * @property \XF\Purchasable\AbstractPurchasable|null handler
 *
 * RELATIONS
 * @property \XF\Entity\AddOn AddOn
 */
class Purchasable extends Entity
{
	public function isActive()
	{
		return ($this->AddOn ? $this->AddOn->active : false);
	}

	/**
	 * @return mixed|string
	 */
	public function getTitle()
	{
		$handler = $this->handler;
		return $handler ? $handler->getTitle() : '';
	}

	/**
	 * @return \XF\Purchasable\AbstractPurchasable|null
	 */
	public function getHandler()
	{
		$class = \XF::stringToClass($this->purchasable_class, '%s\Purchasable\%s');
		if (!class_exists($class))
		{
			return null;
		}

		$class = \XF::extendClass($class);
		return new $class($this->purchasable_type_id);
	}

	protected function _setupDefaults()
	{
		/** @var \XF\Repository\AddOn $addOnRepo */
		$addOnRepo = $this->_em->getRepository('XF:AddOn');
		$this->addon_id = $addOnRepo->getDefaultAddOnId();
	}

	public static function getStructure(Structure $structure)
	{
		$structure->table = 'xf_purchasable';
		$structure->shortName = 'XF:Purchasable';
		$structure->primaryKey = 'purchasable_type_id';
		$structure->columns = [
			'purchasable_type_id' => ['type' => self::STR, 'maxLength' => 50, 'required' => true],
			'purchasable_class' => ['type' => self::STR, 'maxLength' => 100, 'required' => true],
			'addon_id' => ['type' => self::BINARY, 'maxLength' => 50, 'default' => '']
		];
		$structure->getters = [
			'title' => false,
			'handler' => true
		];
		$structure->relations = [
			'AddOn' => [
				'entity' => 'XF:AddOn',
				'type' => self::TO_ONE,
				'conditions' => 'addon_id',
				'primary' => true
			]
		];

		return $structure;
	}
}