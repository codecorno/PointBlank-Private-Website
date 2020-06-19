<?php

namespace XF\Template\Compiler\Tag;

use XF\Template\Compiler\Syntax\Tag;
use XF\Template\Compiler;

class Set extends AbstractTag
{
	public function compile(Tag $tag, Compiler $compiler, array $context, $inlineExpected)
	{
		$tag->assertAttribute('var');

		$varContext = $context;
		$varContext['escape'] = false;
		$var = $compiler->compileSimpleVariable($tag->attributes['var'], $varContext);

		if (isset($tag->attributes['value']))
		{
			$tag->assertEmpty();
			$value = $tag->attributes['value']->compile($compiler, $varContext, true);
		}
		else
		{
			$value = "{$compiler->templaterVariable}->preEscaped("
				. $compiler->compileInlineList($tag->children, $context) . ')';
		}

		$compiler->write("$var = $value;");

		return $inlineExpected ? "''" : false;
	}
}