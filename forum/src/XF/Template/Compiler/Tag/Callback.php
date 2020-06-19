<?php

namespace XF\Template\Compiler\Tag;

use XF\Template\Compiler\Syntax\Tag;
use XF\Template\Compiler;

class Callback extends AbstractTag
{
	public function compile(Tag $tag, Compiler $compiler, array $context, $inlineExpected)
	{
		$tag->assertAttribute('class')->assertAttribute('method');

		$localContext['escape'] = false;
		$attributes = $tag->attributes;

		$class = $attributes['class']->compile($compiler, $localContext, true);
		$method = $attributes['method']->compile($compiler, $localContext, true);

		if (isset($attributes['params']))
		{
			$params = $compiler->compileForcedExpression($attributes['params'], $localContext);
		}
		else
		{
			$params = 'array()';
		}

		$children = $compiler->compileInlineList($tag->children, $context);

		return "{$compiler->templaterVariable}->callback($class, $method, $children, $params)";
	}
}