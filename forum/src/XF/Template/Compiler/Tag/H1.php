<?php

namespace XF\Template\Compiler\Tag;

use XF\Template\Compiler\Syntax\Tag;
use XF\Template\Compiler;

class H1 extends AbstractTag
{
	public function compile(Tag $tag, Compiler $compiler, array $context, $inlineExpected)
	{
		$hidden = !empty($tag->attributes['hidden']);

		if ($hidden && !$tag->children)
		{
			$compiler->write("{$compiler->templaterVariable}->pageParams['noH1'] = true;");
			return $inlineExpected ? "''" : false;
		}

		if ($tag->isSelfClose)
		{
			$rawContext = $context;
			$rawContext['escape'] = false;

			if (isset($tag->attributes['fallback']))
			{
				$fallback = $tag->attributes['fallback']->compile($compiler, $rawContext, true);
			}
			else
			{
				$fallback = "''";
			}

			return "{$compiler->templaterVariable}->func('page_h1', array($fallback))";
		}
		else
		{
			$value = $compiler->compileInlineList($tag->children, $context);
			if ($hidden)
			{
				$compiler->write("{$compiler->templaterVariable}->pageParams['noH1'] = true;");
			}
			$compiler->write("{$compiler->templaterVariable}->pageParams['pageH1'] = {$compiler->templaterVariable}->preEscaped($value);");
			return $inlineExpected ? "''" : false;
		}
	}
}