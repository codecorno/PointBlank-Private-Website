<?php

namespace XF\Template\Compiler\Tag;

use XF\Template\Compiler\Syntax\Tag;
use XF\Template\Compiler;

class Ad extends AbstractTag
{
	public function compile(Tag $tag, Compiler $compiler, array $context, $inlineExpected)
	{
		$attributes = $tag->attributes;

		$rawContext = $context;
		$rawContext['escape'] = false;

		$tag->assertAttribute('position');

		$arguments = [];
		foreach ($attributes AS $attribute => $value)
		{
			if (preg_match('#^arg-([a-zA-Z0-9_-]+)$#', $attribute, $match))
			{
				if (strpos($match[1], '-') !== false)
				{
					throw $tag->exception(\XF::phrase('macro_argument_names_may_only_contain_alphanumeric_underscore'));
				}

				$arguments[$match[1]] = $value;
			}
		}

		if ($arguments)
		{
			$arguments = $this->compileAttributesAsArray($arguments, $compiler, $context);
			$indent = $compiler->indent();
			$argumentsCode = "array(" . implode('', $arguments) . "\n$indent)";
		}
		else
		{
			$argumentsCode = 'array()';
		}

		$position = $attributes['position']->compile($compiler, $rawContext, true);

		return "{$compiler->templaterVariable}->callAdsMacro({$position}, {$argumentsCode}, {$compiler->variableContainer})";
	}
}