<?php

namespace XF\Service\Node;

use XF\Mvc\Entity\Entity;

class RebuildNestedSet extends \XF\Service\RebuildNestedSet
{
	protected function getBasePassableData()
	{
		$passable = parent::getBasePassableData();
		$passable['effective_style_id'] = 0;
		$passable['effective_navigation_id'] = '';

		return $passable;
	}

	protected function getSelfData(array $passData, Entity $entity, $depth, $left)
	{
		if ($entity->style_id)
		{
			$passData['effective_style_id'] = $entity->style_id;
		}
		if ($entity->navigation_id)
		{
			if ($passData['effective_navigation_id'] != $entity->navigation_id)
			{
				// parent is a different navigation section, so restart the breadcrumb
				$passData[$this->config['breadcrumbField']] = [];
			}

			$passData['effective_navigation_id'] = $entity->navigation_id;
		}

		return $passData;
	}

	protected function getChildPassableData(array $passData, Entity $entity, $depth, $left)
	{
		$passData = parent::getChildPassableData($passData, $entity, $depth, $left);

		if ($entity->style_id)
		{
			$passData['effective_style_id'] = $entity->style_id;
		}
		if ($entity->navigation_id)
		{
			if ($passData['effective_navigation_id'] != $entity->navigation_id)
			{
				// parent is a different navigation section, so restart the breadcrumb
				$passData[$this->config['breadcrumbField']] = [];
			}

			$passData['effective_navigation_id'] = $entity->navigation_id;
		}

		return $passData;
	}

	protected function getBreadcrumbEntry(Entity $entity, $depth, $left)
	{
		$breadcrumb = parent::getBreadcrumbEntry($entity, $depth, $left);
		$breadcrumb['node_name'] = $entity->node_name;
		$breadcrumb['node_type_id'] = $entity->node_type_id;

		return $breadcrumb;
	}
}