<?php

namespace XF\Template\Compiler\Tag;

use XF\Template\Compiler\Syntax\Tag;
use XF\Template\Compiler;

class Mustache extends AbstractFormElement
{
	public function compile(Tag $tag, Compiler $compiler, array $context, $inlineExpected)
	{
		$attributes = $tag->attributes;

		$tag->assertAttribute('name');

		if (!$attributes['name'] instanceof Compiler\Syntax\Str)
		{
			throw $tag->exception(\XF::phrase('mustache_variable_names_must_be_literal_strings'));
		}
		$name = $attributes['name']->compile($compiler, $context, true);

		if ($tag->children)
		{
			$contentHtml = $compiler->compileInlineList($tag->children, $context);
		}
		else
		{
			$contentHtml = 'null';
		}

		return "{$compiler->templaterVariable}->func('mustache', array($name, $contentHtml))";
	}
}