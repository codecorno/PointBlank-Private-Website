<?php

namespace XF\Template\Compiler\Tag;

use XF\Template\Compiler\Syntax\Tag;
use XF\Template\Compiler;

class PageNav extends AbstractTag
{
	public function compile(Tag $tag, Compiler $compiler, array $context, $inlineExpected)
	{
		$tag->assertAttribute('page')
			->assertAttribute('perpage')
			->assertAttribute('total')
			->assertAttribute('link');

		$attributes = $tag->attributes;

		$attributes['perPage'] = $attributes['perpage'];
		unset($attributes['perpage']);

		if (isset($attributes['pageparam']))
		{
			$attributes['pageParam'] = $attributes['pageparam'];
			unset($attributes['pageparam']);
		}

		$config = $this->compileAttributesAsArray($attributes, $compiler, $context);
		$indent = $compiler->indent();
		$configCode = "array(array(" . implode('', $config) . "\n$indent))";

		return "{$compiler->templaterVariable}->func('page_nav', $configCode)";
	}
}