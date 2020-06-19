<?php

namespace XF\Template\Compiler\Tag;

use XF\Template\Compiler\Syntax\Tag;
use XF\Template\Compiler;

class PageAction extends AbstractTag
{
	public function compile(Tag $tag, Compiler $compiler, array $context, $inlineExpected)
	{
		if ($tag->isSelfClose)
		{
			return "(isset({$compiler->templaterVariable}->pageParams['pageAction']) ?"
				. " {$compiler->templaterVariable}->pageParams['pageAction'] : '')";
		}
		else
		{
			$attributes = $tag->attributes;

			if (!empty($attributes['if']))
			{
				$if = $compiler->compileForcedExpression($attributes['if'], $context);
			}
			else
			{
				$if = null;
			}

			if ($if)
			{
				$compiler->write("if ($if) {")->pushIndent();
			}

			$value = $compiler->compileInlineList($tag->children, $context);
			$compiler->write("{$compiler->templaterVariable}->pageParams['pageAction'] = {$compiler->templaterVariable}->preEscaped($value);");

			if ($if)
			{
				$compiler->popIndent()->write('}');
			}

			return $inlineExpected ? "''" : false;
		}
	}
}