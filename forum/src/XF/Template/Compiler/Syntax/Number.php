<?php

namespace XF\Template\Compiler\Syntax;

use XF\Template\Compiler;

class Number extends AbstractSyntax
{
	public $number;

	public function __construct($number, $line)
	{
		$this->number = $number + 0;
		$this->line = $line;
	}

	public function compile(Compiler $compiler, array $context, $inlineExpected)
	{
		return $this->number + 0;
	}
}