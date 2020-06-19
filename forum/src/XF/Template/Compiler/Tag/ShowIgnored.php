<?php

namespace XF\Template\Compiler\Tag;

use XF\Template\Compiler\Syntax\Tag;
use XF\Template\Compiler;

class ShowIgnored extends AbstractTag
{
	public function compile(Tag $tag, Compiler $compiler, array $context, $inlineExpected)
	{
		$tag->assertEmpty();

		$context['escape'] = false;

		$attributes = $tag->attributes;
		$config = $this->compileAttributesAsArray($attributes, $compiler, $context);
		$indent = $compiler->indent();
		$attributesCode = "array(" . implode('', $config) . "\n$indent)";

		return "{$compiler->templaterVariable}->func('show_ignored', array({$attributesCode}))";
	}
}