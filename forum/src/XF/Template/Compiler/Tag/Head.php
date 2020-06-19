<?php

namespace XF\Template\Compiler\Tag;

use XF\Template\Compiler\Syntax\Tag;
use XF\Template\Compiler;

class Head extends AbstractTag
{
	public function compile(Tag $tag, Compiler $compiler, array $context, $inlineExpected)
	{
		$tag->assertAttribute('option');

		$localContext['escape'] = false;
		$attributes = $tag->attributes;

		$option = $attributes['option']->compile($compiler, $localContext, true);
		$value = $compiler->compileInlineList($tag->children, $context);

		$compiler->write("{$compiler->templaterVariable}->setPageParam('head.' . $option, {$compiler->templaterVariable}->preEscaped($value));");

		return $inlineExpected ? "''" : false;
	}
}