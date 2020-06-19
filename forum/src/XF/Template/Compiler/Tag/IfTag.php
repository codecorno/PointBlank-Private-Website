<?php

namespace XF\Template\Compiler\Tag;

use XF\Template\Compiler\Syntax\Tag;
use XF\Template\Compiler;

class IfTag extends AbstractTag
{
	public function compile(Tag $tag, Compiler $compiler, array $context, $inlineExpected)
	{
		$callback = function($type, array $blocks) use ($compiler, $context)
		{
			$compiler->traverseBlockChildren($blocks, $context);
		};

		return $this->compileToCallback($tag, $compiler, $context, $callback, $inlineExpected);
	}

	public function compileToCallback(
		Tag $tag, Compiler $compiler, array $context, \Closure $blockHandler, $inlineExpected = false
	)
	{
		$attributes = $tag->attributes;

		if ($inlineExpected)
		{
			$compiler->pushTempVar();
		}

		$conditionContext = $context;
		$conditionContext['escape'] = false;

		$currentPart = 0;
		$parts = [
			$currentPart => [
				'condition' => !empty($attributes['is']) ?
					$compiler->compileForcedExpression($attributes['is'], $conditionContext) : null,
				'contentcheck' => !empty($attributes['contentcheck']) ? true : null,
				'children' => [],
				'tag' => $tag
			]
		];

		$hasElse = false;
		foreach ($tag->children AS $child)
		{
			if ($this->isNamedTag($child, 'elseif'))
			{
				/** @var $child Tag */
				if ($hasElse)
				{
					throw $child->exception(\XF::phrase('else_if_tag_found_after_else_tag'));
				}

				$child->assertEmpty();

				$currentPart++;
				$parts[$currentPart] = [
					'condition' => !empty($child->attributes['is']) ?
						$compiler->compileForcedExpression($child->attributes['is'], $conditionContext) : null,
					'contentcheck' => !empty($child->attributes['contentcheck']) ? true : null,
					'children' => [],
					'tag' => $child
				];
			}
			else if ($this->isNamedTag($child, 'else'))
			{
				/** @var $child Tag */
				$child->assertEmpty();

				if ($hasElse)
				{
					throw $child->exception(\XF::phrase('only_one_else_tag_is_allowed_per_if_tag'));
				}

				$currentPart++;
				$parts[$currentPart] = [
					'else' => true,
					'children' => [],
					'tag' => $child
				];

				$hasElse = true;
			}
			else if ($this->isNamedTag($child, 'contentcheck'))
			{
				/** @var $child Tag */
				if (empty($parts[$currentPart]['contentcheck']))
				{
					throw $child->exception(\XF::phrase('found_contentcheck_tag_without_contentcheck_based_if_tag'));
				}

				$compiler->pushTempVar();
				$blockHandler('contentcheck', $child->children);
				$var = $compiler->popVar();

				$parts[$currentPart]['condition'] = "strlen(trim($var)) > 0";
				$parts[$currentPart]['children'][] = new Compiler\Syntax\InlinePhp($var, $child->line);
			}
			else
			{
				$parts[$currentPart]['children'][] = $child;
			}
		}

		foreach ($parts AS $key => $part)
		{
			if (!empty($part['condition']))
			{
				if ($key === 0)
				{
					$compiler->write('if (' . $part['condition'] . ') {')->pushIndent();
					$blockHandler('if', $part['children']);
				}
				else
				{
					$compiler->popIndent()->write('} else if (' . $part['condition'] . ') {')->pushIndent();
					$blockHandler('elseif', $part['children']);
				}
			}
			else if (!empty($part['else']))
			{
				$compiler->popIndent()->write('} else {')->pushIndent();
				$blockHandler('else', $part['children']);
			}
			else
			{
				/** @var Tag $partTag */
				$partTag = $part['tag'];
				throw $partTag->exception(\XF::phrase('tag_must_be_valid_conditional_using_is_attribute_or_content_checking'));
			}
		}

		$compiler->popIndent()->write('}');

		if ($inlineExpected)
		{
			return $compiler->popVar();
		}
		else
		{
			return false;
		}
	}
}