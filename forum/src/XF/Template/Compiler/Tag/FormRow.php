<?php

namespace XF\Template\Compiler\Tag;

use XF\Template\Compiler\Syntax\Tag;
use XF\Template\Compiler;

class FormRow extends AbstractFormElement
{
	public function compile(Tag $tag, Compiler $compiler, array $context, $inlineExpected)
	{
		$withEscaping = [];
		foreach ($this->defaultRowOptions AS $option => $escaped)
		{
			if ($escaped)
			{
				$withEscaping[] = $option;
			}
		}

		$rowOptions = $this->compileAttributesAsArray($tag->attributes, $compiler, $context, $withEscaping);
		$indent = $compiler->indent();
		$rowOptionCode = "array(" . implode('', $rowOptions)  . "\n$indent)";

		$contentHtml = $compiler->compileInlineList($tag->children, $context);

		return "{$compiler->templaterVariable}->formRow($contentHtml, $rowOptionCode)";
	}
}