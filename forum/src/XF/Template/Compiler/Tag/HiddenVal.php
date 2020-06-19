<?php

namespace XF\Template\Compiler\Tag;

use XF\Template\Compiler\Syntax\Tag;
use XF\Template\Compiler;

class HiddenVal extends AbstractTag
{
	public function compile(Tag $tag, Compiler $compiler, array $context, $inlineExpected)
	{
		$tag->assertAttribute('name');

		$context['escape'] = false;

		$extraAttributes = $tag->attributes;

		if (isset($tag->attributes['value']))
		{
			if ($tag->children)
			{
				throw $tag->exception(\XF::phrase('hiddenval_tag_must_provide_value_attribute_or_children'));
			}

			$tag->assertEmpty();
			$value = $tag->attributes['value']->compile($compiler, $context, true);
			unset($extraAttributes['value']);
		}
		else if ($tag->children)
		{
			$value = $compiler->compileInlineList($tag->children, $context);
		}
		else
		{
			$value = "''";
		}

		$name = $tag->attributes['name']->compile($compiler, $context, true);
		unset($extraAttributes['name']);

		$attributesCompiled = $this->compileAttributesAsArray($extraAttributes, $compiler, $context);
		$indent = $compiler->indent();
		$attributesCode = "array(" . implode('', $attributesCompiled) . "\n$indent)";

		return "{$compiler->templaterVariable}->formHiddenVal($name, $value, $attributesCode)";
	}
}