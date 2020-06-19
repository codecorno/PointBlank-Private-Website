<?php

namespace XF\Template\Compiler\Tag;

use XF\Template\Compiler\Syntax\Tag;
use XF\Template\Compiler;

class Title extends AbstractTag
{
	public function compile(Tag $tag, Compiler $compiler, array $context, $inlineExpected)
	{
		$rawContext = $context;
		$rawContext['escape'] = false;

		if ($tag->isSelfClose)
		{
			if (isset($tag->attributes['formatter']))
			{
				$formatter = $tag->attributes['formatter']->compile($compiler, $rawContext, true);
			}
			else
			{
				$formatter = 'null';
			}

			if (isset($tag->attributes['fallback']))
			{
				$fallback = $tag->attributes['fallback']->compile($compiler, $rawContext, true);
			}
			else
			{
				$fallback = "''";
			}

			if (isset($tag->attributes['page']))
			{
				$page = $tag->attributes['page']->compile($compiler, $rawContext, true);
			}
			else
			{
				$page = 'null';
			}

			return "{$compiler->templaterVariable}->func('page_title', array({$formatter}, {$fallback}, {$page}))";
		}
		else
		{
			$value = $compiler->compileInlineList($tag->children, $context);
			$compiler->write("{$compiler->templaterVariable}->pageParams['pageTitle'] = {$compiler->templaterVariable}->preEscaped($value);");
			if (isset($tag->attributes['page']))
			{
				$page = $tag->attributes['page']->compile($compiler, $rawContext, true);
				$compiler->write("{$compiler->templaterVariable}->pageParams['pageNumber'] = $page;");
			}
			return $inlineExpected ? "''" : false;
		}
	}
}