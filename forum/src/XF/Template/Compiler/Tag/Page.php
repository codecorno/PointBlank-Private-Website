<?php

namespace XF\Template\Compiler\Tag;

use XF\Template\Compiler\Syntax\Tag;
use XF\Template\Compiler;

class Page extends AbstractTag
{
	public function compile(Tag $tag, Compiler $compiler, array $context, $inlineExpected)
	{
		$tag->assertAttribute('option');

		$localContext['escape'] = false;
		$attributes = $tag->attributes;

		$option = $attributes['option']->compile($compiler, $localContext, true);
		if (isset($tag->attributes['value']))
		{
			$tag->assertEmpty();
			$value = $tag->attributes['value']->compile($compiler, $localContext, true);
		}
		else
		{
			$value = $compiler->compileInlineList($tag->children, $context);
		}

		$compiler->write("{$compiler->templaterVariable}->setPageParam($option, $value);");

		return $inlineExpected ? "''" : false;
	}
}