<?php

namespace XF\Repository;

use XF\Mvc\Entity\Finder;
use XF\Mvc\Entity\Repository;

class Widget extends Repository
{
	/**
	 * @return Finder
	 */
	public function findWidgetsForList()
	{
		return $this->finder('XF:Widget')
			->with('WidgetDefinition', true)
			->whereAddOnActive([
				'relation' => 'WidgetDefinition.AddOn',
				'column' => 'WidgetDefinition.addon_id'
			])
			->order('widget_id');
	}

	public function groupWidgetsByPositions(\XF\Mvc\Entity\ArrayCollection $widgets, &$total = 0)
	{
		$positionMap = [];
		$groupedWidgets = [];

		foreach ($widgets AS $widgetId => $widget)
		{
			if (!$widget->positions)
			{
				$groupedWidgets[''][$widgetId] = $widget;
				continue;
			}
			foreach ($widget->positions AS $positionId => $displayOrder)
			{
				$positionMap[$positionId][$widgetId] = $displayOrder;
			}
		}

		foreach (array_keys($positionMap) AS $positionId)
		{
			asort($positionMap[$positionId]);
			$total += count($positionMap[$positionId]);

			foreach ($positionMap[$positionId] AS $widgetId => $displayOrder)
			{
				$groupedWidgets[$positionId][$widgetId] = $widgets[$widgetId];
			}
		}

		return $groupedWidgets;
	}

	/**
	 * @return Finder
	 */
	public function findWidgetDefinitionsForList($activeOnly = false)
	{
		$finder = $this->finder('XF:WidgetDefinition')->order('definition_id');
		if ($activeOnly)
		{
			$finder->with('AddOn')
				->whereAddOnActive();
		}
		return $finder;
	}

	public function getWidgetDefinitionTitlePairs($activeOnly = false)
	{
		return $this->findWidgetDefinitionsForList($activeOnly)->fetch()->pluckNamed('title', 'definition_id');
	}

	/**
	 * @return Finder
	 */
	public function findWidgetPositionsForList($activeOnly = false)
	{
		$finder = $this->finder('XF:WidgetPosition')->order('position_id');
		if ($activeOnly)
		{
			$finder->with('AddOn')
				->whereAddOnActive()
				->where('active', 1);
		}
		return $finder;
	}

	public function getWidgetCache()
	{
		$output = [];

		$widgets = $this->finder('XF:Widget')
			->with('WidgetDefinition', true)
			->with('WidgetDefinition.AddOn')
			->whereAddOnActive([
				'relation' => 'WidgetDefinition.AddOn',
				'column' => 'WidgetDefinition.addon_id'
			]);

		foreach ($widgets->fetch() AS $widget)
		{
			if ($widget['positions'])
			{
				foreach ($widget['positions'] AS $positionId => $displayOrder)
				{
					$output[$positionId][$widget->widget_id] = [
						'widget_id' => $widget->widget_id,
						'widget_key' => $widget->widget_key,
						'definition_id' => $widget->WidgetDefinition->definition_id,
						'definition_class' => $widget->WidgetDefinition->definition_class,
						'options' => $widget->options,
						'display_order' => $displayOrder
					];
				}
			}
			else
			{
				$output[''][$widget->widget_id] = [
					'widget_id' => $widget->widget_id,
					'widget_key' => $widget->widget_key,
					'definition_id' => $widget->WidgetDefinition->definition_id,
					'definition_class' => $widget->WidgetDefinition->definition_class,
					'options' => $widget->options,
					'display_order' => 0
				];
			}
		}

		foreach (array_keys($output) AS $positionId)
		{
			if (!$positionId)
			{
				continue;
			}

			uasort($output[$positionId], function($a, $b)
			{
				return $a['display_order'] - $b['display_order'];
			});
		}

		return $output;
	}

	public function rebuildWidgetCache()
	{
		$cache = $this->getWidgetCache();
		\XF::registry()->set('widgetCache', $cache);
		return $cache;
	}

	public function recompileWidgets()
	{
		$widgets = $this->finder('XF:Widget')->order('widget_id')->fetch();

		if ($widgets)
		{
			foreach ($widgets AS $widget)
			{
				/** @var \XF\Service\Widget\Compile $compileService */
				$compileService = $this->app()->service('XF:Widget\Compile', $widget);
				$compileService->compile();
			}
		}
	}

	public function getWidgetDefinitionCache()
	{
		$output = [];

		$widgetDefinitions = $this->finder('XF:WidgetDefinition')
			->with('AddOn')
			->whereAddOnActive()
			->order('definition_id');

		foreach ($widgetDefinitions->fetch() AS $widgetDefinition)
		{
			$output[$widgetDefinition->definition_id] = [
				'definition_id' => $widgetDefinition->definition_id,
				'definition_class' => $widgetDefinition->definition_class,
				'addon_id' => $widgetDefinition->addon_id
			];
		}

		return $output;
	}

	public function rebuildWidgetDefinitionCache()
	{
		$cache = $this->getWidgetDefinitionCache();
		\XF::registry()->set('widgetDefinition', $cache);

		\XF::runOnce('widgetCacheRebuild', function()
		{
			$this->rebuildWidgetCache();
		});

		return $cache;
	}

	public function getWidgetPositionCache()
	{
		$output = [];

		$widgetPositions = $this->finder('XF:WidgetPosition')
			->with('AddOn')
			->whereAddOnActive()
			->where('active', 1)
			->order('position_id');

		foreach ($widgetPositions->fetch() AS $widgetPosition)
		{
			$output[$widgetPosition->position_id] = [
				'position_id' => $widgetPosition->position_id,
				'addon_id' => $widgetPosition->addon_id
			];
		}

		return $output;
	}

	public function rebuildWidgetPositionCache()
	{
		$cache = $this->getWidgetPositionCache();
		\XF::registry()->set('widgetPosition', $cache);
		return $cache;
	}
}