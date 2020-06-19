<?php

namespace XF\Template\Compiler\Tag;

use XF\Template\Compiler\Syntax\Tag;
use XF\Template\Compiler;

class Css extends AbstractTag
{
	public function compile(Tag $tag, Compiler $compiler, array $context, $inlineExpected)
	{
		$context['escape'] = false;
		$attributes = $tag->attributes;

		if (!empty($attributes['src']))
		{
			$css = $attributes['src']->compile($compiler, $context, true);
			$compiler->write("{$compiler->templaterVariable}->includeCss($css);");
		}

		if ($tag->children)
		{
			$inline = $compiler->compileInlineList($tag->children, $context);
			$compiler->write("{$compiler->templaterVariable}->inlineCss($inline);");
		}

		return $inlineExpected ? "''" : false;
	}
}