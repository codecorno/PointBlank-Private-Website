<?php

namespace XF\Template\Compiler\Tag;

use XF\Template\Compiler\Syntax\Tag;
use XF\Template\Compiler;

class Avatar extends AbstractTag
{
	public function compile(Tag $tag, Compiler $compiler, array $context, $inlineExpected)
	{
		$tag->assertEmpty()->assertAttribute('user')->assertAttribute('size');

		$context['escape'] = false;

		$user = $compiler->compileForcedExpression($tag->attributes['user'], $context);
		$size = $tag->attributes['size']->compile($compiler, $context, true);

		if (isset($tag->attributes['canonical']))
		{
			$canonical = $compiler->compileForcedExpression($tag->attributes['canonical'], $context);
		}
		else
		{
			$canonical = 'false';
		}

		$otherAttributes = $tag->attributes;
		unset($otherAttributes['user'], $otherAttributes['size'], $otherAttributes['canonical']);

		$config = $this->compileAttributesAsArray($otherAttributes, $compiler, $context);
		$indent = $compiler->indent();
		$attributesCode = "array(" . implode('', $config) . "\n$indent)";

		return "{$compiler->templaterVariable}->func('avatar', array($user, $size, $canonical, $attributesCode))";
	}
}