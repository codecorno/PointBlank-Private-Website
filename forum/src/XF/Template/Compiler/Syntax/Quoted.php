<?php

namespace XF\Template\Compiler\Syntax;

use XF\Template\Compiler;

class Quoted extends AbstractSyntax
{
	/**
	 * @var AbstractSyntax[]
	 */
	public $parts = [];

	public function __construct(array $parts, $line)
	{
		$this->parts = $parts;
		$this->line = $line;
	}

	public function compile(Compiler $compiler, array $context, $inlineExpected)
	{
		return $compiler->compileInlineList($this->parts, $context);
	}

	public function isSimpleValue()
	{
		return false;
	}
}