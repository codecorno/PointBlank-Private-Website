<?php

namespace XF\Template\Compiler\Tag;

use XF\Template\Compiler\Syntax\Tag;
use XF\Template\Compiler;

class Wrap extends AbstractTag
{
	public function compile(Tag $tag, Compiler $compiler, array $context, $inlineExpected)
	{
		$attributes = $tag->attributes;

		$tag->assertAttribute('template');

		if ($tag->children)
		{
			$varContainer = $compiler->getTempVar();
			$compiler->write("$varContainer = {$compiler->variableContainer};");
		}
		else
		{
			$varContainer = $compiler->variableContainer;
		}

		$includeContext = $context;
		$includeContext['escape'] = false;

		$template = $attributes['template']->compile($compiler, $includeContext, true);

		/** @var $child Tag */
		foreach ($tag->children AS $child)
		{
			if ($this->isNamedTag($child, 'map'))
			{
				$child->assertAttribute('from')->assertAttribute('to');

				$from = $compiler->compileSimpleVariable($child->attributes['from'], $includeContext);
				$to = $compiler->requireSimpleVariable($child->attributes['to'])->compileToVarContainer(
					$varContainer, $compiler, $includeContext, true
				);
				$compiler->write("{$to} = {$from};");
			}
			else if ($this->isNamedTag($child, 'set'))
			{
				$child->assertAttribute('var');

				$var = $compiler->requireSimpleVariable($child->attributes['var'])->compileToVarContainer(
					$varContainer, $compiler, $includeContext, true
				);
				if (!empty($child->attributes['value']))
				{
					$value = $child->attributes['value']->compile($compiler, $includeContext, true);
				}
				else
				{
					$value = "{$compiler->templaterVariable}->preEscaped("
						. $compiler->compileInlineList($child->children, $context) . ')';
				}

				$compiler->write("{$var} = {$value};");
			}
			else if ($this->isNamedTag($child, 'extract'))
			{
				$child->assertAttribute('var');

				$var = $compiler->requireSimpleVariable($child->attributes['var'])->compile(
					$compiler, $includeContext, true
				);

				$compiler->write("{$compiler->templaterVariable}->extractIntoVarContainer({$varContainer}, {$var});");
			}
		}

		$compiler->write("{$compiler->templaterVariable}->wrapTemplate({$template}, {$varContainer});");

		return $inlineExpected ? "''" : false;
	}
}