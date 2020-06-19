<?php

namespace XF\Template\Compiler\Syntax;

use XF\Template\Compiler;

class Boolean extends AbstractSyntax
{
	public $bool;

	public function __construct($bool, $line)
	{
		$this->bool = (bool)$bool;
		$this->line = $line;
	}

	public function compile(Compiler $compiler, array $context, $inlineExpected)
	{
		return $this->bool ? 'true' : 'false';
	}
}