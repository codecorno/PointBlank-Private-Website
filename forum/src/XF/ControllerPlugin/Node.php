<?php

namespace XF\ControllerPlugin;

use XF\Mvc\Entity\Entity;

class Node extends AbstractPlugin
{
	public function applyNodeContext(\XF\Entity\Node $node)
	{
		$this->controller->setContainerKey('node-' . $node->node_id);

		if ($node->effective_style_id)
		{
			$this->controller->setViewOption('style_id', $node->effective_style_id);
		}
		if ($node->effective_navigation_id)
		{
			$this->controller->setSectionContext($node->effective_navigation_id);
		}
	}

	public static function getNodeActivityDetails(
		array $activities, $nodeTypeId, $phrase, \Closure $fallbackHandler = null
	)
	{
		$nodeIds = [];
		$nodeNames = [];
		$nodeData = [];

		$nodeTypes = \XF::app()->container('nodeTypes');
		if (!isset($nodeTypes[$nodeTypeId]))
		{
			return $phrase;
		}

		$nodeType = $nodeTypes[$nodeTypeId];
		$entityType = $nodeType['entity_identifier'];
		$routeName = $nodeType['public_route'];
		$router = \XF::app()->router('public');

		foreach ($activities AS $activity)
		{
			$nodeId = $activity->pluckParam('node_id');
			if ($nodeId)
			{
				$nodeIds[$nodeId] = $nodeId;
				continue;
			}
			$nodeName = $activity->pluckParam('node_name');
			if ($nodeName)
			{
				$nodeNames[$nodeName] = $nodeName;
			}
		}

		$with = ['Node', 'Node.Permissions|' . \XF::visitor()->permission_combination_id];

		if ($nodeNames)
		{
			$nodes = \XF::finder($entityType)
				->where('Node.node_name', $nodeNames)
				->with($with)
				->fetch()
				->filterViewable();

			foreach ($nodes AS $nodeId => $node)
			{
				$nodeData[$node->node_name] = [
					'title' => $node->title,
					'url' => $router->buildLink($routeName, $node)
				];
			}
		}

		if ($nodeIds)
		{
			$nodes = \XF::em()
				->findByIds($entityType, $nodeIds, $with)
				->filterViewable();

			foreach ($nodes AS $nodeId => $node)
			{
				$nodeData[$nodeId] = [
					'title' => $node->title,
					'url' => $router->buildLink($routeName, $node)
				];
			}
		}

		$output = [];

		foreach ($activities AS $key => $activity)
		{
			$nodeId = $activity->pluckParam('node_id');
			$node = $nodeId && isset($nodeData[$nodeId]) ? $nodeData[$nodeId] : null;
			if ($node)
			{
				$output[$key] = [
					'description' => $phrase,
					'title' => $node['title'],
					'url' => $node['url']
				];
				continue;
			}

			$nodeName = $activity->pluckParam('node_name');
			$node = $nodeName && isset($nodeData[$nodeName]) ? $nodeData[$nodeName] : null;
			if ($node)
			{
				$output[$key] = [
					'description' => $phrase,
					'title' => $node['title'],
					'url' => $node['url']
				];
				continue;
			}

			if ($fallbackHandler)
			{
				$output[$key] = $fallbackHandler($activity, $nodeId, $nodeName);
			}
			else
			{
				$output[$key] = $phrase;
			}
		}

		return $output;
	}
}