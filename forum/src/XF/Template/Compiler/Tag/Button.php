<?php

namespace XF\Template\Compiler\Tag;

use XF\Template\Compiler\Syntax\Tag;
use XF\Template\Compiler;

class Button extends AbstractTag
{
	public function compile(Tag $tag, Compiler $compiler, array $context, $inlineExpected)
	{
		$options = $this->compileAttributesAsArray($tag->attributes, $compiler, $context);

		$menuHtml = "''";
		$menuOptions = [];

		$children = $tag->children;
		foreach ($tag->children AS $i => $child)
		{
			if ($this->isNamedTag($child, 'menu'))
			{
				/** @var $child Tag */
				$menuHtml = $compiler->compileInlineList($child->children, $context);
				$menuOptions = $this->compileAttributesAsArray($child->attributes, $compiler, $context);
				unset($children[$i]);
			}
		}

		$indent = $compiler->indent();

		$optionCode = "array(" . implode('', $options)  . "\n$indent)";
		$menuOptionCode = "array(" . implode('', $menuOptions)  . "\n$indent)";

		$contentHtml = $compiler->compileInlineList($children, $context);

		return "{$compiler->templaterVariable}->button($contentHtml, $optionCode, $menuHtml, $menuOptionCode)";
	}
}