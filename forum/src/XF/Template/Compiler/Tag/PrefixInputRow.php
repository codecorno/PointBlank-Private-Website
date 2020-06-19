<?php

namespace XF\Template\Compiler\Tag;

use XF\Template\Compiler\Syntax\Tag;
use XF\Template\Compiler;

class PrefixInputRow extends AbstractFormElement
{
	public function compile(Tag $tag, Compiler $compiler, array $context, $inlineExpected)
	{
		$tag->assertEmpty()->assertAttribute('prefixes')->assertAttribute('type');

		$context['escape'] = false;

		$prefixes = $compiler->compileForcedExpression($tag->attributes['prefixes'], $context);

		$otherAttributes = $tag->attributes;
		unset($otherAttributes['prefixes']);

		if (!empty($otherAttributes['href']))
		{
			if (!isset($otherAttributes['listen-to']))
			{
				throw $tag->exception(\XF::phrase('to_load_prefixes_dynamically_you_must_also_provide_listen_to_attribute'));
			}
		}

		$withEscaping = [];
		$rowAttributes = [];
		foreach ($this->defaultRowOptions AS $option => $escaped)
		{
			if ($escaped)
			{
				$withEscaping[] = $option;
			}
			if (isset($otherAttributes[$option]))
			{
				$rowAttributes[$option] = $otherAttributes[$option];
				unset($otherAttributes[$option]);
			}
		}

		$config = $this->compileAttributesAsArray($otherAttributes, $compiler, $context);
		$indent = $compiler->indent();
		$attributesCode = "array(" . implode('', $config) . "\n$indent)";

		if ($tag->name == 'prefixinputrow')
		{
			$rowOptions = $this->compileAttributesAsArray($rowAttributes, $compiler, $context, $withEscaping);
			$indent = $compiler->indent();
			$rowOptionCode = "array(" . implode('', $rowOptions)  . "\n$indent)";

			return "{$compiler->templaterVariable}->formPrefixInputRow($prefixes, $attributesCode, $rowOptionCode)";
		}
		else
		{
			return "{$compiler->templaterVariable}->formPrefixInput($prefixes, $attributesCode)";
		}
	}
}