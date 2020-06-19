<?php

namespace XF\Template\Compiler\Tag;

use XF\Template\Compiler\Syntax\Tag;
use XF\Template\Compiler;

class RedirectInput extends AbstractTag
{
	public function compile(Tag $tag, Compiler $compiler, array $context, $inlineExpected)
	{
		$tag->assertEmpty();

		$rawContext = $context;
		$rawContext['escape'] = false;

		if (isset($tag->attributes['url']))
		{
			$url = $tag->attributes['url']->compile($compiler, $rawContext, true);
		}
		else
		{
			$url = "null";
		}

		if (isset($tag->attributes['fallback']))
		{
			$fallback = $tag->attributes['fallback']->compile($compiler, $rawContext, true);
		}
		else
		{
			$fallback = "null";
		}

		if (isset($tag->attributes['referrer']))
		{
			$referrer = $compiler->compileForcedExpression($tag->attributes['referrer'], $rawContext);
		}
		else
		{
			$referrer = "true";
		}

		return "{$compiler->templaterVariable}->func('redirect_input', array($url, $fallback, $referrer))";
	}
}