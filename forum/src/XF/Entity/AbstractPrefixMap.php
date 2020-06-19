<?php

namespace XF\Entity;

use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Structure;

/**
 * Class AbstractPrefixMap
 *
 * @package XF\Entity
 *
 * COLUMNS
 * @property int prefix_id
 *
 * RELATIONS
 * @property \XF\Entity\AbstractPrefix Prefix
 */
abstract class AbstractPrefixMap extends Entity
{
	public function getContainerId()
	{
		return $this->getValue(self::getContainerKey());
	}

	public static function getContainerKey()
	{
		throw new \LogicException('This must be overridden.');
	}

	protected static function setupDefaultStructure(Structure $structure, $table, $shortName, $prefixIdentifier)
	{
		$containerKey = static::getContainerKey();

		$structure->table = $table;
		$structure->shortName = $shortName;
		$structure->primaryKey = [$containerKey, 'prefix_id'];
		$structure->columns = [
			$containerKey => ['type' => self::UINT, 'required' => true],
			'prefix_id' => ['type' => self::UINT, 'required' => true]
		];
		$structure->getters = [];
		$structure->relations = [
			'Prefix' => [
				'entity' => $prefixIdentifier,
				'type' => self::TO_ONE,
				'conditions' => 'prefix_id',
				'primary' => true
			]
		];

		return $structure;
	}
}