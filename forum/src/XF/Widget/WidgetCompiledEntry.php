<?php

namespace XF\Widget;

class WidgetCompiledEntry
{
	public $key;

	public $conditionExpression = '';

	public function __construct($key)
	{
		$this->key = $key;
	}

	public function applyCondition($conditionExpression)
	{
		$this->conditionExpression = $conditionExpression;
	}

	public function generateWidgetCode($widgetVar, $optionsVar, $depth = 1)
	{
		$output = '';
		$indent = str_repeat("\t", $depth);

		if ($this->conditionExpression)
		{
			$output .= "{$indent}{$widgetVar} = '';\n\n";
			$output .= "{$indent}if ({$this->conditionExpression}) {\n";

			$depth++;
			$indent .= "\t";
		}

		$key = addcslashes($this->key, "\\'");
		$output .= "{$indent}{$widgetVar} = \\XF::app()->widget()->widget('{$key}', {$optionsVar})->render();\n";

		if ($this->conditionExpression)
		{
			$depth--;
			$indent = str_repeat("\t", $depth);

			$output .= "{$indent}}";
		}

		return rtrim($output);
	}
}