<?php

namespace XF\Template\Compiler\Tag;

use XF\Template\Compiler\Syntax\Tag;
use XF\Template\Compiler;

abstract class AbstractFormElement extends AbstractTag
{
	/**
	 * Specific attributes that will be treated as row-level.
	 * Key is name of the attribute. The value should be true if variables within
	 * should be escaped (because the value will be displayed raw) or false if
	 * the value will be escaped in all cases before displaying.
	 *
	 * @var array
	 */
	protected $defaultRowOptions = [
		'label' => true,
		'hint' => true,
		'explain' => true,
		'rowclass' => false,
		'rowid' => false,
		'rowtype' => false,
		'initialhtml' => true,
		'html' => true,
		'finalhtml' => true
	];

	public function compileTextInput($functionBaseName, $isRowLevel, Tag $tag, Compiler $compiler, array $context)
	{
		$context['escape'] = true;

		list($controlOptions, $rowOptions) = $this->getOptionsFromAttributes($tag, $compiler, $context);

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

		return $this->getFinalInputElementCode(
			$functionBaseName, $isRowLevel, $compiler,
			$controlOptions, $rowOptions
		);
	}

	public function compileChoiceInput($functionBaseName, $isRowLevel, Tag $tag, Compiler $compiler, array $context, $allowDependent = false)
	{
		$context['escape'] = true;

		list($controlOptions, $rowOptions) = $this->getOptionsFromAttributes($tag, $compiler, $context);

		$choices = new Compiler\ChoiceBuilder($compiler);

		foreach ($tag->children AS $child)
		{
			$foundExpected = (
				$this->compileRowOptionChild($child, $compiler, $context, $rowOptions)
				|| $this->compileExpectedChoice($choices, $tag, $child, $compiler, $context, $allowDependent)
			);
			if (!$foundExpected)
			{
				throw $child->exception(\XF::phrase('tag_x_contains_unexpected_child_element', ['name' => $tag->name]));
			}
		}

		return $this->getFinalChoiceElementCode(
			$functionBaseName, $isRowLevel, $compiler,
			$controlOptions, $choices, $rowOptions
		);
	}

	public function getOptionsFromAttributes(Tag $tag, Compiler $compiler, array $htmlContext, array $htmlAttributes = [])
	{
		$htmlContext['escape'] = true;

		$textContext = $htmlContext;
		$textContext['escape'] = false;

		$controlOptions = [];
		$rowOptions = [];
		foreach ($tag->attributes AS $name => $value)
		{
			if (isset($this->defaultRowOptions[$name]))
			{
				if ($this->defaultRowOptions[$name])
				{
					// allow html - escape variables now
					$rowOptions[$name] = $compiler->compileToArraySyntax($value, $name, $htmlContext);
				}
				else
				{
					// text only - escape at runtime
					$rowOptions[$name] = $compiler->compileToArraySyntax($value, $name, $textContext);
				}
			}
			else
			{
				if ($htmlAttributes && in_array($name, $htmlAttributes))
				{
					$controlOptions[$name] = $compiler->compileToArraySyntax($value, $name, $htmlContext);
				}
				else
				{
					$controlOptions[$name] = $compiler->compileToArraySyntax($value, $name, $textContext);
				}
			}
		}

		return [$controlOptions, $rowOptions];
	}

	public function compileRowOptionChild(
		Compiler\Syntax\AbstractSyntax $child,
		Compiler $compiler,
		array $context,
		array &$rowOptions
	)
	{
		if ($this->isNamedTag($child, ['label', 'hint', 'explain', 'initialhtml', 'html', 'finalhtml']))
		{
			/** @var $child Tag */
			$rowOptions[$child->name] = $compiler->compileToArraySyntax($child->children, $child->name, $context);
			return true;
		}

		if ($this->isEmptyString($child))
		{
			// ok, ignore
			return true;
		}

		return false;
	}

	public function compileExpectedChoice(
		Compiler\ChoiceBuilder $choices,
		Tag $tag,
		Compiler\Syntax\AbstractSyntax $child,
		Compiler $compiler,
		array $context,
		$allowDependent = false
	)
	{
		$rawContext = $context;
		$rawContext['escape'] = false;

		if ($this->isNamedTag($child, 'option'))
		{
			/** @var $child Tag */
			$optionTag = $this->compileAttributesAsArray($child->attributes, $compiler, $context,
				['label', 'hint'], ['selected']
			);
			$this->compileOptionChildren($child->children, $compiler, $context, $optionTag, $allowDependent);
			$choices->handleOptionTag($optionTag);

			return true;
		}

		if ($this->isNamedTag($child, 'options'))
		{
			/** @var $child Tag */
			$child->assertAttribute('source');

			$choices->handleOptionsTag(
				$compiler->compileForcedExpression($child->attributes['source'], $rawContext)
			);

			return true;
		}

		if ($this->isNamedTag($child, 'optgroup'))
		{
			if ($choices->inOptGroup())
			{
				throw $child->exception(\XF::phrase('optgroup_tags_cannot_be_nested'));
			}

			/** @var $child Tag */
			$optgroupAttributes = $this->compileAttributesAsArray($child->attributes, $compiler, $context);
			$choices->startOptGroup($optgroupAttributes);

			foreach ($child->children AS $grandchild)
			{
				$success = $this->compileExpectedChoice($choices, $tag, $grandchild, $compiler, $context, $allowDependent);
				if (!$success)
				{
					throw $child->exception(\XF::phrase('tag_x_contains_unexpected_child_element', ['name' => $child->name]));
				}
			}

			$choices->endOptGroup();

			return true;
		}

		if ($this->isNamedTag($child, 'if'))
		{
			/** @var $child Tag */

			$choices->forceTempVariable();

			$callback = function($type, array $elements) use ($child, $choices, $tag, $compiler, $context, $allowDependent)
			{
				if ($type == 'contentcheck')
				{
					throw $child->exception(\XF::phrase('contentcheck_based_if_tags_not_supported_with_choice_based_tags'));
				}

				foreach ($elements AS $element)
				{
					$success = $this->compileExpectedChoice($choices, $tag, $element, $compiler, $context, $allowDependent);
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

			$choices->forceTempVariable();

			$callback = function($type, array $elements) use ($child, $choices, $tag, $compiler, $context, $allowDependent)
			{
				foreach ($elements AS $element)
				{
					$success = $this->compileExpectedChoice($choices, $tag, $element, $compiler, $context, $allowDependent);
					if (!$success)
					{
						throw $child->exception(\XF::phrase('tag_x_contains_unexpected_child_element', ['name' => $child->name]));
					}
				}
			};

			/** @var ForeachTag $handler */
			$handler = $child->getTag($compiler);
			$handler->compileToCallback($child, $compiler, $context, $callback);

			return true;
		}

		if ($this->isNamedTag($child, 'set'))
		{
			/** @var $child Tag */

			/** @var Set $handler */
			$handler = $child->getTag($compiler);
			$handler->compile($child, $compiler, $context, false);

			return true;
		}

		if ($this->isEmptyString($child))
		{
			// ok, ignore
			return true;
		}

		return false;
	}

	public function compileOptionChildren(array $children, Compiler $compiler, array $context, array &$optionTag, $allowDependent = false)
	{
		if (empty($optionTag['label']) && $children)
		{
			$hasTag = false;
			foreach ($children AS $child)
			{
				if ($child instanceof Tag)
				{
					$hasTag = true;
					break;
				}
			}

			if (!$hasTag)
			{
				// children are automatically the label if there are no other tags within
				$optionTag['label'] = $compiler->compileToArraySyntax($children, 'label', $context);
				return;
			}
		}

		/** @var $children Compiler\Syntax\AbstractSyntax[] */
		foreach ($children AS $child)
		{
			if ($this->isNamedTag($child, ['label', 'hint', 'html', 'afterhint', 'afterhtml']))
			{
				/** @var $child Tag */
				$optionTag[$child->name] = $compiler->compileToArraySyntax($child->children, $child->name, $context);
				continue;
			}

			if ($allowDependent)
			{
				if ($this->isNamedTag($child, [
					'checkbox', 'dateinput', 'macro', 'radio', 'select',
					'textarea', 'textbox', 'tokeninput', 'numberbox', 'upload'
				]))
				{
					/** @var $child Tag */
					$optionTag['_dependent'][] = $child->compile($compiler, $context, true);
					continue;
				}

				if ($this->isNamedTag($child, 'dependent'))
				{
					/** @var $child Tag */
					$optionTag['_dependent'][] = $compiler->compileInlineList($child->children, $context);
					continue;
				}
			}

			if ($this->isEmptyString($child))
			{
				// ok, ignore
				continue;
			}

			throw $child->exception(\XF::phrase('option_tag_contains_unexpected_child_element'));
		}
	}

	public function getFinalChoiceElementCode(
		$functionBaseName,
		$isRowLevel,
		Compiler $compiler,
		array $controlOptions,
		Compiler\ChoiceBuilder $choices,
		array $rowOptions
	)
	{
		$indent = $compiler->indent();
		$rowOptionCode = "array(" . implode('', $rowOptions)  . "\n$indent)";
		$controlOptionCode = "array(" . implode('', $controlOptions) . "\n$indent)";
		$optionsCode = $choices->toInline();

		if ($isRowLevel)
		{
			return "{$compiler->templaterVariable}->form{$functionBaseName}Row($controlOptionCode, $optionsCode, $rowOptionCode)";
		}
		else
		{
			return "{$compiler->templaterVariable}->form{$functionBaseName}($controlOptionCode, $optionsCode)";
		}
	}

	public function getFinalInputElementCode(
		$functionBaseName,
		$isRowLevel,
		Compiler $compiler,
		array $controlOptions,
		array $rowOptions
	)
	{
		$indent = $compiler->indent();
		$rowOptionCode = "array(" . implode('', $rowOptions)  . "\n$indent)";
		$controlOptionCode = "array(" . implode('', $controlOptions) . "\n$indent)";

		if ($isRowLevel)
		{
			return "{$compiler->templaterVariable}->form{$functionBaseName}Row($controlOptionCode, $rowOptionCode)";
		}
		else
		{
			return "{$compiler->templaterVariable}->form{$functionBaseName}($controlOptionCode)";
		}
	}
}