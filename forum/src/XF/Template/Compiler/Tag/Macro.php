<?php

namespace XF\Template\Compiler\Tag;

use XF\Template\Compiler\Syntax\Tag;
use XF\Template\Compiler;

class Macro extends AbstractTag
{
	public function compile(Tag $tag, Compiler $compiler, array $context, $inlineExpected)
	{
		$attributes = $tag->attributes;

		$rawContext = $context;
		$rawContext['escape'] = false;

		$tag->assertAttribute('name');

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

		if ($tag->children)
		{
			// defining a macro
			if (!($attributes['name'] instanceof Compiler\Syntax\Str))
			{
				throw $tag->exception(\XF::phrase('macro_names_must_be_literal_strings'));
			}
			$name = $attributes['name']->content;

			if (!preg_match('#^[a-z0-9_]#i', $name))
			{
				throw $tag->exception(\XF::phrase('macro_names_may_only_contain_alphanumeric_underscore'));
			}

			$globalScope = $compiler->getCodeScope();

			$macroScope = new Compiler\CodeScope($compiler->finalVarName, $compiler);
			$compiler->setCodeScope($macroScope);

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

			// write this in case a temp var is needed
			$compiler->write(
				"{$compiler->variableContainer} = {$compiler->templaterVariable}->mergeMacroArguments({$argumentsCode}, {$compiler->macroArgumentsVariable}, {$compiler->variableContainer});"
			);

			$compiler->traverseBlockChildren($tag->children, $context);

			if (isset($tag->attributes['global']))
			{
				$global = $compiler->compileForcedExpression($tag->attributes['global'], $context);
			}
			else
			{
				$global = 'false';
			}

			$macroCode = "{$compiler->variableContainer} = {$compiler->templaterVariable}->setupBaseParamsForMacro({$compiler->variableContainer}, {$global});
	{$compiler->finalVarName} = '';
" . implode("\n", $compiler->getOutput()) . "
	return {$compiler->finalVarName};";

			$compiler->setCodeScope($globalScope);

			$compiler->defineMacro($name, $macroCode);

			return '';
		}
		else
		{
			// accessing a macro
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

			$name = $attributes['name']->compile($compiler, $rawContext, true);

			if (empty($attributes['template']))
			{
				$template = 'null';
			}
			else
			{
				$template = $attributes['template']->compile($compiler, $rawContext, true);
			}

			return "{$compiler->templaterVariable}->callMacro({$template}, {$name}, {$argumentsCode}, {$compiler->variableContainer})";
		}
	}
}