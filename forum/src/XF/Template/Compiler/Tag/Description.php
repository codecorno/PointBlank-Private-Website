<?php

namespace XF\Template\Compiler\Tag;

use XF\Template\Compiler\Syntax\Tag;
use XF\Template\Compiler;

class Description extends AbstractTag
{
	public function compile(Tag $tag, Compiler $compiler, array $context, $inlineExpected)
	{
		if ($tag->isSelfClose)
		{
			return "{$compiler->templaterVariable}->func('page_description')";
		}
		else
		{
			$value = $compiler->compileInlineList($tag->children, $context);

			if (isset($tag->attributes['meta']))
			{
				$meta = $compiler->compileForcedExpression($tag->attributes['meta'], $context);
			}
			else
			{
				$meta = 'true';
			}

			$compiler->write("{$compiler->templaterVariable}->pageParams['pageDescription'] = {$compiler->templaterVariable}->preEscaped($value);");
			$compiler->write("{$compiler->templaterVariable}->pageParams['pageDescriptionMeta'] = $meta;");

			return $inlineExpected ? "''" : false;
		}
	}
}