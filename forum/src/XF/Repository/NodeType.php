<?php

namespace XF\Repository;

use XF\Mvc\Entity\AbstractCollection;
use XF\Mvc\Entity\Repository;

class NodeType extends Repository
{
	public function getNodeTypeCacheData()
	{
		$output = [];

		foreach ($this->finder('XF:NodeType')->fetch() AS $nodeType)
		{
			$output[$nodeType->node_type_id] = $nodeType->toArray(false);
		}

		return $output;
	}

	public function rebuildNodeTypeCache()
	{
		$cache = $this->getNodeTypeCacheData();
		\XF::registry()->set('nodeTypes', $cache);
		return $cache;
	}
}