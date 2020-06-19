<?php

namespace XF\Template\Compiler\Func;

use XF\Template\Compiler\Syntax\AbstractSyntax;
use XF\Template\Compiler\Syntax\Func;
use XF\Template\Compiler;

class IncludeFn extends AbstractFn
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
		$func->assertArgumentCount(1);

		$context['escape'] = false;
		$template = $func->arguments[0]->compile($compiler, $context, true);
		$varContainer = $compiler->variableContainer;

		return "{$compiler->templaterVariable}->includeTemplate({$template}, {$varContainer})";
	}
}