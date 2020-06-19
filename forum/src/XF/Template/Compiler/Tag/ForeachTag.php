<?php

namespace XF\Template\Compiler\Tag;

use XF\Template\Compiler\Syntax\Tag;
use XF\Template\Compiler;

class ForeachTag extends AbstractTag
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

		$tag->assertAttribute('loop');
		$tag->assertAttribute('value');

		if ($inlineExpected)
		{
			$compiler->pushTempVar();
		}

		$loopContext = $context;
		$loopContext['escape'] = false;

		$loopExpression = $compiler->forceToExpression($attributes['loop']);
		if ($loopExpression instanceof Compiler\Syntax\Variable && $loopExpression->isSimple())
		{
			$loop = $loopExpression->compile($compiler, $loopContext, true);
		}
		else
		{
			$loop = $compiler->getTempVar();
			$loopValue = $loopExpression->compile($compiler, $loopContext, true);
			$compiler->write("$loop = $loopValue;");
		}

		$value = $compiler->compileSimpleVariable($attributes['value'], $loopContext);

		if (!empty($attributes['key']))
		{
			$key = $compiler->compileSimpleVariable($attributes['key'], $loopContext);
		}
		else
		{
			$key = null;
		}

		if (!empty($attributes['i']))
		{
			$i = $compiler->compileSimpleVariable($attributes['i'], $loopContext);
		}
		else
		{
			$i = null;
		}

		if (!empty($attributes['if']))
		{
			$if = $compiler->compileForcedExpression($attributes['if'], $loopContext);
		}
		else
		{
			$if = null;
		}

		$hasElse = false;

		$parts = [
			'foreach' => [],
			'else' => []
		];
		$partId = 'foreach';

		foreach ($tag->children AS $child)
		{
			if ($this->isNamedTag($child, 'else'))
			{
				/** @var $child Tag */

				if ($hasElse)
				{
					throw $child->exception(\XF::phrase('only_one_else_tag_is_allowed_per_foreach_tag'));
				}

				$hasElse = true;
				$partId = 'else';
			}
			else
			{
				$parts[$partId][] = $child;
			}
		}

		if ($hasElse)
		{
			$elseTriggerVar = $compiler->getTempVar();
			$compiler->write("$elseTriggerVar = true;");
		}
		else
		{
			$elseTriggerVar = null;
		}

		if ($i)
		{
			if (!empty($attributes['istart']))
			{
				$iStartExpression = $compiler->forceToExpression($attributes['istart']);
				if ($iStartExpression instanceof Compiler\Syntax\Variable && $iStartExpression->isSimple())
				{
					$iStart = $iStartExpression->compile($compiler, $loopContext, true);
				}
				else
				{
					$iStart = $compiler->getTempVar();
					$iStartValue = $iStartExpression->compile($compiler, $loopContext, true);
					$compiler->write("$iStart = $iStartValue + 0;");
				}
			}
			else
			{
				$iStart = 0;
			}

			$compiler->write("$i = $iStart;");
		}

		$compiler
			->write("if ({$compiler->templaterVariable}->isTraversable({$loop})) {")->pushIndent()
			->write("foreach ($loop AS " . ($key ? "$key => " : '') . "$value) {")->pushIndent();

		if ($if)
		{
			$compiler->write("if ($if) {")->pushIndent();
		}

		if ($hasElse)
		{
			$compiler->write("$elseTriggerVar = false;");
		}
		if ($i)
		{
			$compiler->write("{$i}++;");
		}

		$blockHandler('foreach', $parts['foreach']);

		if ($if)
		{
			$compiler->popIndent()->write('}');
		}

		$compiler
			->popIndent()->write('}') // close the foreach
			->popIndent()->write('}'); // close the outer if


		if ($hasElse)
		{
			$compiler->write("if ($elseTriggerVar) {")->pushIndent();
			$blockHandler('else', $parts['else']);
			$compiler->popIndent()->write('}'); // close the if
		}

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