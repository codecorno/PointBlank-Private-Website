<?php

namespace XF\Template\Compiler\Syntax;

use XF\Template\Compiler;

class NullValue extends AbstractSyntax
{
	public function __construct($line)
	{
		$this->line = $line;
	}

	public function compile(Compiler $compiler, array $context, $inlineExpected)
	{
		return 'null';
	}
}