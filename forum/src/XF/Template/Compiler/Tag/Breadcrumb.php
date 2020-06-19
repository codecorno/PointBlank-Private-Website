<?php

namespace XF\Template\Compiler\Tag;

use XF\Template\Compiler\Syntax\Tag;
use XF\Template\Compiler;

class Breadcrumb extends AbstractTag
{
	public function compile(Tag $tag, Compiler $compiler, array $context, $inlineExpected)
	{
		if (isset($tag->attributes['source']))
		{
			$context['escape'] = false;
			$crumbs = $compiler->compileForcedExpression($tag->attributes['source'], $context);

			$compiler->write("{$compiler->templaterVariable}->breadcrumbs($crumbs);");
			return $inlineExpected ? "''" : false;
		}

		$tag->assertAttribute('href');

		$rawContext = $context;
		$rawContext['escape'] = false;

		$attributes = $tag->attributes;

		$value = $compiler->compileInlineList($tag->children, $context);
		$href = $attributes['href']->compile($compiler, $rawContext, true);

		unset($attributes['href']);
		$config = $this->compileAttributesAsArray($attributes, $compiler, $context);
		$indent = $compiler->indent();
		$configCode = "array(" . implode('', $config) . "\n$indent)";

		$code = "{$compiler->templaterVariable}->breadcrumb({$compiler->templaterVariable}->preEscaped($value), "
			. "$href, $configCode);";
		$compiler->write($code);
		return $inlineExpected ? "''" : false;
	}
}