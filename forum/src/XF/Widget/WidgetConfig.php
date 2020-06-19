<?php

namespace XF\Widget;

class WidgetConfig
{
	public $widgetId;
	public $widgetKey;
	public $definitionId;
	public $title;
	public $positions;
	public $options;

	public function __construct($widgetId, $widgetKey, $definitionId, $title, array $positions, array $options)
	{
		$this->widgetId = $widgetId;
		$this->widgetKey = $widgetKey;
		$this->definitionId = $definitionId;
		$this->title = $title;
		$this->positions = $positions;
		$this->options = $options;
	}

	public static function create($data)
	{
		if (!is_array($data) && !($data instanceof \XF\Mvc\Entity\Entity))
		{
			throw new \InvalidArgumentException(\XF::phrase('data_passed_into_create_widget_config_should_either_be_array_or_entity'));
		}

		if (is_array($data))
		{
			$data = array_replace([
				'widget_id' => 0,
				'widget_key' => '',
				'definition_id' => '',
				'title' => '',
				'positions' => [],
				'options' => []
			], $data);
		}

		return new self(
			$data['widget_id'],
			$data['widget_key'],
			$data['definition_id'],
			$data['title'],
			$data['positions'] ?: [],
			$data['options'] ?: []
		);
	}
}