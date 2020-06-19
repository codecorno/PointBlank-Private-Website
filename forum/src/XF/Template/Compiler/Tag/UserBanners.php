<?php

namespace XF\Template\Compiler\Tag;

use XF\Template\Compiler\Syntax\Tag;
use XF\Template\Compiler;

class UserBanners extends AbstractTag
{
	public function compile(Tag $tag, Compiler $compiler, array $context, $inlineExpected)
	{
		$tag->assertEmpty()->assertAttribute('user');

		$context['escape'] = false;

		$user = $compiler->compileForcedExpression($tag->attributes['user'], $context);

		$otherAttributes = $tag->attributes;
		unset($otherAttributes['user']);

		$config = $this->compileAttributesAsArray($otherAttributes, $compiler, $context);
		$indent = $compiler->indent();
		$attributesCode = "array(" . implode('', $config) . "\n$indent)";

		return "{$compiler->templaterVariable}->func('user_banners', array($user, $attributesCode))";
	}
}