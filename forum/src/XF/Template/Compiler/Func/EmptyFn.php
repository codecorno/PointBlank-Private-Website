<?php

namespace XF\Template\Compiler\Func;

use XF\Template\Compiler\Syntax\AbstractSyntax;
use XF\Template\Compiler;
use XF\Template\Compiler\Syntax\Func;

class EmptyFn extends AbstractFn
{
	/**
	 * @param AbstractSyntax|Func $func
	 * @param Compiler       $compiler
	 * @param array          $context
	 *
	 * @return mixed|string
	 * @throws Compiler\Exception
	 */
	public function compile(AbstractSyntax $func, Compiler $compiler, array $context)
	{
		/** @var Func $func */
		$func->assertArgumentCount(1);

		$context['escape'] = false;

		$value = $func->arguments[0]->compile($compiler, $context, true);
		return "{$compiler->templaterVariable}->func('empty', array($value))";
	}
}