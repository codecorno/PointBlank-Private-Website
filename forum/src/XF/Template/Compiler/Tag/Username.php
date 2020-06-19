<?php

namespace XF\Template\Compiler\Tag;

use XF\Template\Compiler\Syntax\Tag;
use XF\Template\Compiler;

class Username extends AbstractTag
{
	public function compile(Tag $tag, Compiler $compiler, array $context, $inlineExpected)
	{
		$tag->assertEmpty()->assertAttribute('user');

		$context['escape'] = false;

		$user = $compiler->compileForcedExpression($tag->attributes['user'], $context);

		if (isset($tag->attributes['rich']))
		{
			$rich = $compiler->compileForcedExpression($tag->attributes['rich'], $context);
		}
		else
		{
			$rich = 'false';
		}

		$otherAttributes = $tag->attributes;
		unset($otherAttributes['user'], $otherAttributes['rich']);

		$config = $this->compileAttributesAsArray($otherAttributes, $compiler, $context);
		$indent = $compiler->indent();
		$attributesCode = "array(" . implode('', $config) . "\n$indent)";

		return "{$compiler->templaterVariable}->func('username_link', array($user, $rich, $attributesCode))";
	}
}