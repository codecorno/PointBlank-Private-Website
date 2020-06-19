<?php

namespace XF\Template\Compiler\Tag;

use XF\Template\Compiler\Syntax\Tag;
use XF\Template\Compiler;

class WidgetPos extends AbstractTag
{
	public function compile(Tag $tag, Compiler $compiler, array $context, $inlineExpected)
	{
		$tag->assertAttribute('id')->assertEmpty();

		$attributes = $tag->attributes;

		$id = $attributes['id']->compile($compiler, $context, true);

		$contextParams = [];
		foreach ($attributes AS $attribute => $value)
		{
			if (preg_match('#^context-([a-zA-Z0-9_-]+)$#', $attribute, $match))
			{
				if (strpos($match[1], '-') !== false)
				{
					throw $tag->exception(\XF::phrase('context_param_names_may_only_contain_alphanumeric_underscore'));
				}

				$contextParams[$match[1]] = $value;
			}
		}

		if ($contextParams)
		{
			$contextParams = $this->compileAttributesAsArray($contextParams, $compiler, $context);
			$indent = $compiler->indent();
			$contextCode = "array(" . implode('', $contextParams) . "\n$indent)";
		}
		else
		{
			$contextCode = 'array()';
		}

		$widgetPosition = "{$compiler->templaterVariable}->widgetPosition($id, {$contextCode})";

		if (!empty($attributes['position']))
		{
			if (!($attributes['position'] instanceof Compiler\Syntax\Str))
			{
				throw $tag->exception(\XF::phrase('template_tag_attribute_x_must_be_literal_string', ['name' => 'position']));
			}

			$position = strtolower($attributes['position']->content);
			switch ($position)
			{
				case 'sidebar':
					$key = '_xfWidgetPositionSidebar' . \XF\Util\Php::camelCase($attributes['id']->content);
					$compiler->write("{$compiler->templaterVariable}->modifySidebarHtml('{$key}', {$widgetPosition}, 'replace');");
					break;

				case 'sidenav':
					$key = '_xfWidgetPositionSideNav' . \XF\Util\Php::camelCase($attributes['id']->content);
					$compiler->write("{$compiler->templaterVariable}->modifySideNavHtml('{$key}', {$widgetPosition}, 'replace');");
					break;

				default:
					throw $tag->exception(\XF::phrase('unknown_position_x', ['position' => $position]));
			}

			return $inlineExpected ? "''" : false;
		}
		else
		{
			return $widgetPosition;
		}
	}
}