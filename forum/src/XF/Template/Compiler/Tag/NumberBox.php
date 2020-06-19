<?php

namespace XF\Template\Compiler\Tag;

use XF\Template\Compiler\Syntax\Tag;
use XF\Template\Compiler;

class NumberBox extends AbstractFormElement
{
	public function compile(Tag $tag, Compiler $compiler, array $context, $inlineExpected)
	{
		$context['escape'] = false;

		$attributes = $tag->attributes;
		$withEscaping = [];
		$rowAttributes = [];

		foreach ($this->defaultRowOptions AS $option => $escaped)
		{
			if ($escaped)
			{
				$withEscaping[] = $option;
			}
			if (isset($attributes[$option]))
			{
				$rowAttributes[$option] = $attributes[$option];
				unset($attributes[$option]);
			}
		}

		$config = $this->compileAttributesAsArray($attributes, $compiler, $context);
		$indent = $compiler->indent();
		$attributesCode = "array(" . implode('', $config) . "\n$indent)";

		$contentHtml = "{$compiler->templaterVariable}->formNumberBox($attributesCode)";

		if ($tag->name == 'numberboxrow')
		{
			$rowOptions = $this->compileAttributesAsArray($rowAttributes, $compiler, $context, $withEscaping);
			$indent = $compiler->indent();
			$rowOptionCode = "array(" . implode('', $rowOptions)  . "\n$indent)";

			return "{$compiler->templaterVariable}->formRow($contentHtml, $rowOptionCode)";
		}
		else
		{
			return $contentHtml;
		}
	}
}