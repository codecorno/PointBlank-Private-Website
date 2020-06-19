<?php

namespace XF\Template\Compiler\Tag;

use XF\Template\Compiler\Syntax\Tag;
use XF\Template\Compiler;

class SubmitRow extends AbstractFormElement
{
	public function compile(Tag $tag, Compiler $compiler, array $context, $inlineExpected)
	{
		$context['escape'] = true;

		list($controlOptions, $rowOptions) = $this->getOptionsFromAttributes(
			$tag, $compiler, $context,
			['submit']
		);

		foreach ($tag->children AS $child)
		{
			$foundExpected = (
				$this->compileRowOptionChild($child, $compiler, $context, $rowOptions)
			);
			if (!$foundExpected)
			{
				throw $child->exception(\XF::phrase('tag_x_contains_unexpected_child_element', ['name' => $tag->name]));
			}
		}

		$indent = $compiler->indent();
		$rowOptionCode = "array(" . implode('', $rowOptions)  . "\n$indent)";
		$controlOptionCode = "array(" . implode('', $controlOptions) . "\n$indent)";

		return "{$compiler->templaterVariable}->formSubmitRow($controlOptionCode, $rowOptionCode)";
	}
}