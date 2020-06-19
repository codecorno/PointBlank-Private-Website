<?php

namespace XF\Template\Compiler\Tag;

use XF\Template\Compiler\Syntax\Tag;
use XF\Template\Compiler;

class Js extends AbstractTag
{
	public function compile(Tag $tag, Compiler $compiler, array $context, $inlineExpected)
	{
		$context['escape'] = false;
		$attributes = $tag->attributes;

		if ($attributes)
		{
			if (!isset($attributes['src']) && !isset($attributes['dev']) && !isset($attributes['prod']))
			{
				throw $tag->exception(\XF::phrase('at_least_one_attribute_of_src_dev_or_prod_must_be_provided'));
			}

			if (isset($attributes['src']) && (isset($attributes['dev']) || isset($attributes['prod'])))
			{
				throw $tag->exception(\XF::phrase('src_attribute_must_not_be_specified_when_using_dev_or_prod_attributes'));
			}

			$options = $this->compileAttributesAsArray($attributes, $compiler, $context);
			$indent = $compiler->indent();
			$optionsCode = "array(" . implode('', $options) . "\n$indent)";

			$compiler->write("{$compiler->templaterVariable}->includeJs({$optionsCode});");
		}

		if ($tag->children)
		{
			$inline = $compiler->compileInlineList($tag->children, $context);
			$compiler->write("{$compiler->templaterVariable}->inlineJs($inline);");
		}

		return $inlineExpected ? "''" : false;
	}
}