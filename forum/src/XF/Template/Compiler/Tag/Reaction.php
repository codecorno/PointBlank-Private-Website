<?php

namespace XF\Template\Compiler\Tag;

use XF\Template\Compiler\Syntax\Tag;
use XF\Template\Compiler;

class Reaction extends AbstractTag
{
	public function compile(Tag $tag, Compiler $compiler, array $context, $inlineExpected)
	{
		$tag->assertAttribute('id');

		$config = $this->compileAttributesAsArray($tag->attributes, $compiler, $context);
		$indent = $compiler->indent();
		$configCode = "array(array(" . implode('', $config) . "\n$indent))";

		return "{$compiler->templaterVariable}->func('reaction', $configCode)";
	}
}