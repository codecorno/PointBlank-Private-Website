<?php

namespace XF\Template\Compiler\Func;

use XF\Template\Compiler\Syntax\AbstractSyntax;
use XF\Template\Compiler\Syntax\Func;
use XF\Template\Compiler;

abstract class AbstractFn
{
	public $name;

	public function __construct($name)
	{
		$this->name = $name;
	}

	/**
	 * @param AbstractSyntax|Func $func
	 * @param Compiler       $compiler
	 * @param array          $context
	 *
	 * @return mixed|string
	 * @throws Compiler\Exception
	 */
	abstract public function compile(AbstractSyntax $func, Compiler $compiler, array $context);

	public function reset()
	{

	}
}