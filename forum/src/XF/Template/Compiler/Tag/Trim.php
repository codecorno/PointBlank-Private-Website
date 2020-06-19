<?php

namespace XF\Template\Compiler\Tag;

use XF\Template\Compiler\Syntax\Tag;
use XF\Template\Compiler;

class Trim extends AbstractTag
{
	public function compile(Tag $tag, Compiler $compiler, array $context, $inlineExpected)
	{
		$valueHtml = $compiler->compileInlineList($tag->children, $context);

		return "trim({$valueHtml})";
	}
}