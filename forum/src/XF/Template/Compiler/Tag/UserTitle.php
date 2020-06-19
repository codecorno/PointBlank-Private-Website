<?php

namespace XF\Template\Compiler\Tag;

use XF\Template\Compiler\Syntax\Tag;
use XF\Template\Compiler;

class UserTitle extends AbstractTag
{
	public function compile(Tag $tag, Compiler $compiler, array $context, $inlineExpected)
	{
		$tag->assertEmpty()->assertAttribute('user');

		$context['escape'] = false;

		$user = $compiler->compileForcedExpression($tag->attributes['user'], $context);

		if (isset($tag->attributes['banner']))
		{
			$withBanner = $compiler->compileForcedExpression($tag->attributes['banner'], $context);
		}
		else
		{
			$withBanner = 'false';
		}

		$otherAttributes = $tag->attributes;
		unset($otherAttributes['user'], $otherAttributes['banner']);

		$config = $this->compileAttributesAsArray($otherAttributes, $compiler, $context);
		$indent = $compiler->indent();
		$attributesCode = "array(" . implode('', $config) . "\n$indent)";

		return "{$compiler->templaterVariable}->func('user_title', array($user, $withBanner, $attributesCode))";
	}
}