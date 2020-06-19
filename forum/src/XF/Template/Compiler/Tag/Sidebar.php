<?php

namespace XF\Template\Compiler\Tag;

use XF\Template\Compiler\Syntax\Tag;
use XF\Template\Compiler;

class Sidebar extends AbstractTag
{
	public function compile(Tag $tag, Compiler $compiler, array $context, $inlineExpected)
	{
		$value = $compiler->compileInlineList($tag->children, $context);

		if (isset($tag->attributes['mode']))
		{
			$mode = $tag->attributes['mode']->compile($compiler, $context, true);
		}
		else
		{
			$mode = "'replace'";
		}

		if (isset($tag->attributes['key']))
		{
			$key = $tag->attributes['key']->compile($compiler, $context, true);
		}
		else
		{
			$key = "null";
		}

		$compiler->write("{$compiler->templaterVariable}->modifySidebarHtml({$key}, {$value}, {$mode});");
		return $inlineExpected ? "''" : false;
	}
}