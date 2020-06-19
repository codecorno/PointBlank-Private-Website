<?php

namespace XF\Template\Compiler\Tag;

use XF\Template\Compiler;
use XF\Template\Compiler\Syntax\Tag;

class FontAwesome extends AbstractTag
{
	public function compile(Tag $tag, Compiler $compiler, array $context, $inlineExpected)
	{
		$tag->assertEmpty();
		$tag->assertAttribute('icon');

		$attributes = $tag->attributes;
		$iconClasses = $attributes['icon']->compile($compiler, $context, true);

		$otherAttributes = $tag->attributes;
		unset($otherAttributes['icon']);

		$indent = $compiler->indent();

		$attributesArray = $this->compileAttributesAsArray($otherAttributes, $compiler, $context);
		$attributesCode = "array(" . implode('', $attributesArray) . "\n$indent)";

		return "{$compiler->templaterVariable}->fontAwesome({$iconClasses}, {$attributesCode})";
	}
}