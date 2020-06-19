<?php

namespace XF\Template\Compiler\Tag;

use XF\Template\Compiler\Syntax\Tag;
use XF\Template\Compiler;

class DataRow extends AbstractTag
{
	public function compile(Tag $tag, Compiler $compiler, array $context, $inlineExpected)
	{
		$options = $this->compileAttributesAsArray($tag->attributes, $compiler, $context,
			['label', 'hint', 'explain']
		);
		$cells = new Compiler\CellBuilder($compiler);

		foreach ($tag->children AS $child)
		{
			if ($this->isNamedTag($child, ['label', 'hint', 'explain']))
			{
				/** @var $child Tag */
				$options[$child->name] = $compiler->compileToArraySyntax($child->children, $child->name, $context);
			}
			else if ($this->compileExpectedCell($cells, $tag, $child, $compiler, $context))
			{
				// ok
			}
			else
			{
				throw $child->exception(\XF::phrase('tag_x_contains_unexpected_child_element', ['name' => $tag->name]));
			}
		}

		// popup, beforelabel, toggle, toggletitle, link
		// id, selectable/selectname/selectvalue/selectdisabled/selecttooltip, tooltip,
		// linkclass, labelclass, target, linkstyle, deletehint, class

		$indent = $compiler->indent();
		$optionCode = "array(" . implode('', $options) . "\n$indent)";
		$cellsCode = $cells->toInline();

		return "{$compiler->templaterVariable}->dataRow($optionCode, $cellsCode)";
	}

	protected function compileExpectedCell(
		Compiler\CellBuilder $cells,
		Tag $tag,
		Compiler\Syntax\AbstractSyntax $child,
		Compiler $compiler,
		array $context
	)
	{
		if ($this->isNamedTag($child, ['cell', 'action', 'delete', 'toggle', 'popup']))
		{
			/** @var $child Tag */
			$cells->handleCell(
				$child->name,
				$compiler->compileInlineList($child->children, $context),
				$this->compileAttributesAsArray(
					$child->attributes, $compiler, $context, [], ['selected', 'disabled']
				)
			);

			return true;
		}

		if ($this->isNamedTag($child, 'main'))
		{
			/** @var $child Tag */
			$options = $this->compileAttributesAsArray(
				$child->attributes, $compiler, $context, ['label', 'hint', 'explain']
			);
			foreach ($child->children AS $grandchild)
			{
				if ($this->isNamedTag($grandchild, ['label', 'hint', 'explain']))
				{
					/** @var $grandchild Tag */
					$options[$grandchild->name] = $compiler->compileToArraySyntax($grandchild->children, $grandchild->name, $context);
				}
				else if (!$this->isEmptyString($grandchild))
				{
					throw $grandchild->exception(\XF::phrase('tag_x_contains_unexpected_child_element', ['name' => $child->name]));
				}
			}

			$cells->handleCell($child->name, "''", $options);

			return true;
		}

		if ($this->isNamedTag($child, 'if'))
		{
			/** @var $child Tag */

			$cells->forceTempVariable();

			$callback = function($type, array $elements) use ($child, $cells, $tag, $compiler, $context)
			{
				if ($type == 'contentcheck')
				{
					throw $child->exception(\XF::phrase('contentcheck_based_if_tags_not_supported_with_cell_tags'));
				}

				foreach ($elements AS $element)
				{
					$success = $this->compileExpectedCell($cells, $tag, $element, $compiler, $context);
					if (!$success)
					{
						throw $child->exception(\XF::phrase('tag_x_contains_unexpected_child_element', ['name' => $child->name]));
					}
				}
			};

			/** @var IfTag $handler */
			$handler = $child->getTag($compiler);
			$handler->compileToCallback($child, $compiler, $context, $callback);

			return true;
		}

		if ($this->isNamedTag($child, 'foreach'))
		{
			/** @var $child Tag */

			$cells->forceTempVariable();

			$callback = function($type, array $elements) use ($child, $cells, $tag, $compiler, $context)
			{
				foreach ($elements AS $element)
				{
					$success = $this->compileExpectedCell($cells, $tag, $element, $compiler, $context);
					if (!$success)
					{
						throw $child->exception(\XF::phrase('tag_x_contains_unexpected_child_element', ['name' => $child->name]));
					}
				}
			};

			/** @var IfTag $handler */
			$handler = $child->getTag($compiler);
			$handler->compileToCallback($child, $compiler, $context, $callback);

			return true;
		}

		if ($this->isEmptyString($child))
		{
			return true;
		}

		return false;
	}
}