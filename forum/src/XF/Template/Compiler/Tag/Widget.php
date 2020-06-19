<?php

namespace XF\Template\Compiler\Tag;

use XF\Template\Compiler\Syntax\Tag;
use XF\Template\Compiler;

class Widget extends AbstractTag
{
	public function compile(Tag $tag, Compiler $compiler, array $context, $inlineExpected)
	{
		$tag->assertEmpty();
		$context['escape'] = false;
		$attributes = $tag->attributes;
		$argumentsCode = 'array()';

		if (isset($attributes['key']))
		{
			if (!($attributes['key'] instanceof Compiler\Syntax\Str))
			{
				throw $tag->exception(\XF::phrase('template_tag_attribute_x_must_be_literal_string', ['name' => 'key']));
			}

			if (!preg_match('#^[a-z0-9_]+$#i', $attributes['key']->content))
			{
				throw $tag->exception(\XF::phrase('widget_keys_may_only_contain_alphanumeric_underscore'));
			}

			$identifier = $attributes['key']->compile($compiler, $context, true);
		}
		else if (isset($attributes['class']))
		{
			if (!($attributes['class'] instanceof Compiler\Syntax\Str))
			{
				throw $tag->exception(\XF::phrase('template_tag_attribute_x_must_be_literal_string', ['name' => 'class']));
			}

			$arguments = [];
			foreach ($attributes AS $attribute => $value)
			{
				if (preg_match('#^opt-([a-zA-Z0-9_-]+)$#', $attribute, $match))
				{
					if (strpos($match[1], '-') !== false)
					{
						throw $tag->exception(\XF::phrase('widget_option_names_may_only_contain_alphanumeric_underscore'));
					}

					$arguments[$match[1]] = $value;
				}
				else if ($attribute == 'title')
				{
					// Set an attribute which is treated as a special case later on to override the title
					$arguments['_title'] = $value;
				}
			}

			$class = $attributes['class'];

			if (strpos($class->content, ':') === false && strpos($class->content, '\\') === false)
			{
				throw $tag->exception(\XF::phrase('widget_tag_widget_attribute_must_be_widget_definition_class_name'));
			}

			$class->content = \XF::stringToClass($class->content, '%s\Widget\%s');
			$identifier = $class->compile($compiler, $context, true);

			if ($arguments)
			{
				$arguments = $this->compileAttributesAsArray($arguments, $compiler, $context);
				$indent = $compiler->indent();
				$argumentsCode = "array(" . implode('', $arguments) . "\n$indent)";
			}
		}
		else
		{
			throw $tag->exception(\XF::phrase('widget_tags_must_contain_either_key_or_widget_attribute'));
		}

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

		$widget = "{$compiler->templaterVariable}->renderWidget({$identifier}, {$argumentsCode}, {$contextCode})";

		if (!empty($attributes['position']))
		{
			// this used to be the key/class itself, but that would prevent rendering the same widget with various
			// options set, so instead set it to be unique based on the code generated
			$keyId = md5($widget);

			$position = strtolower($attributes['position']->content);
			switch ($position)
			{
				case 'sidebar':
					$key = '_xfWidgetSidebar' . \XF\Util\Php::camelCase($keyId, ['_', '\\']);
					$compiler->write("{$compiler->templaterVariable}->modifySidebarHtml('{$key}', {$widget}, 'replace');");
					break;

				case 'sidenav':
					$key = '_xfWidgetSideNav' . \XF\Util\Php::camelCase($keyId, ['_', '\\']);
					$compiler->write("{$compiler->templaterVariable}->modifySideNavHtml('{$key}', {$widget}, 'replace');");
					break;

				default:
					throw $tag->exception(\XF::phrase('unknown_position_x', ['position' => $position]));
			}

			return $inlineExpected ? "''" : false;
		}
		else
		{
			return $widget;
		}
	}
}