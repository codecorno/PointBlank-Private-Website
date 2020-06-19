<?php

namespace XF\Entity;

use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Structure;

/**
 * COLUMNS
 * @property int permission_combination_id
 * @property string content_type
 * @property int content_id
 * @property array cache_value
 */
class PermissionCacheContent extends Entity implements \XF\Mvc\Entity\Proxyable
{
	public static function instantiateProxied(array $values)
	{
		\XF::app()->permissionCache()->setContentPerms(
			$values['permission_combination_id'],
			$values['content_type'],
			$values['content_id'],
			@json_decode($values['cache_value'], true) ?: []
		);
	}

	public static function getStructure(Structure $structure)
	{
		$structure->table = 'xf_permission_cache_content';
		$structure->shortName = 'XF:PermissionCacheContent';
		$structure->primaryKey = ['permission_combination_id', 'content_type', 'content_id'];
		$structure->columns = [
			'permission_combination_id' => ['type' => self::UINT, 'required' => true],
			'content_type' => ['type' => self::STR, 'maxLength' => 25, 'required' => true],
			'content_id' => ['type' => self::UINT, 'required' => true],
			'cache_value' => ['type' => self::JSON_ARRAY, 'required' => true]
		];
		$structure->getters = [];
		$structure->relations = [];

		return $structure;
	}
}