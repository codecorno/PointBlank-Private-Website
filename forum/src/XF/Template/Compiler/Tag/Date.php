<?php

namespace XF\Template\Compiler\Tag;

use XF\Template\Compiler\Syntax\Tag;
use XF\Template\Compiler;

class Date extends AbstractTag
{
	public function compile(Tag $tag, Compiler $compiler, array $context, $inlineExpected)
	{
		$tag->assertAttribute('time')->assertEmpty();

		$time = $compiler->compileForcedExpression($tag->attributes['time'], $context);

		$otherAttributes = $tag->attributes;
		unset($otherAttributes['time']);

		$config = $this->compileAttributesAsArray($otherAttributes, $compiler, $context);
		$indent = $compiler->indent();
		$attributesCode = "array(" . implode('', $config) . "\n$indent)";

		return "{$compiler->templaterVariable}->func('date_dynamic', array($time, $attributesCode))";
	}
}