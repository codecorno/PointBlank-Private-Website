<?php

namespace XF\Entity;

use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Structure;

/**
 * COLUMNS
 * @property int node_id
 *
 * GETTERS
 * @property string|null node_name
 * @property string|null title
 * @property string|null description
 * @property int depth
 *
 * RELATIONS
 * @property \XF\Entity\Node Node
 */
class Category extends AbstractNode
{
	public function getNodeTemplateRenderer($depth)
	{
		return [
			'template' => 'node_list_category',
			'macro' => $depth <= 2 ? 'depth' . $depth : 'depthN'
		];
	}

	public function getCategoryAnchor()
	{
		return $this->app()->router('public')->prepareStringForUrl($this->title, true) . '.' . $this->node_id;
	}

	public static function getStructure(Structure $structure)
	{
		$structure->table = 'xf_category';
		$structure->shortName = 'XF:Category';
		$structure->primaryKey = 'node_id';
		$structure->columns = [
			'node_id' => ['type' => self::UINT, 'required' => true],
		];
		$structure->getters = [];
		$structure->relations = [];

		static::addDefaultNodeElements($structure);

		return $structure;
	}
}