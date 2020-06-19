<?php

namespace XF\Template\Compiler\Tag;

use XF\Template\Compiler\Syntax\Tag;
use XF\Template\Compiler;

class React extends AbstractTag
{
	public function compile(Tag $tag, Compiler $compiler, array $context, $inlineExpected)
	{
		$tag
			->assertAttribute('link')
			->assertAttribute('content');

		$config = $this->compileAttributesAsArray($tag->attributes, $compiler, $context);
		$indent = $compiler->indent();
		$configCode = "array(array(" . implode('', $config) . "\n$indent))";

		return "{$compiler->templaterVariable}->func('react', $configCode)";
	}
}