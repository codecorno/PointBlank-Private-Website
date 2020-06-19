<?php

namespace XF\Template\Compiler\Tag;

use XF\Template\Compiler\Syntax\Tag;
use XF\Template\Compiler;

class Reactions extends AbstractTag
{
	public function compile(Tag $tag, Compiler $compiler, array $context, $inlineExpected)
	{
		$tag->assertEmpty();

		$context['escape'] = false;

		if (isset($tag->attributes['summary']))
		{
			$tag->assertAttribute('reactions');

			$reactions = $tag->attributes['reactions']->compile($compiler, $context, true);

			return "{$compiler->templaterVariable}->func('reactions_summary', array($reactions))";
		}

		$link = $tag->attributes['link']->compile($compiler, $context, true);

		if (isset($tag->attributes['linkparams']))
		{
			$linkParams = $tag->attributes['linkparams']->compile($compiler, $context, true);
		}
		else
		{
			$linkParams = "array()";
		}

		$content = $tag->attributes['content']->compile($compiler, $context, true);

		return "{$compiler->templaterVariable}->func('reactions', array($content, $link, $linkParams))";
	}
}