<?php

namespace XF\Template\Compiler\Tag;

use XF\Template\Compiler\Syntax\Tag;
use XF\Template\Compiler;

class InfoRow extends AbstractFormElement
{
	public function compile(Tag $tag, Compiler $compiler, array $context, $inlineExpected)
	{
		$rowOptions = $this->compileAttributesAsArray($tag->attributes, $compiler, $context);
		$indent = $compiler->indent();
		$rowOptionCode = "array(" . implode('', $rowOptions)  . "\n$indent)";

		$contentHtml = $compiler->compileInlineList($tag->children, $context);

		return "{$compiler->templaterVariable}->formInfoRow($contentHtml, $rowOptionCode)";
	}
}