<?php

namespace XF\Template\Compiler\Syntax;

use XF\Template\Compiler;

class InlinePhp extends AbstractSyntax
{
	public $php;

	public function __construct($php, $line)
	{
		$this->php = $php;
		$this->line = $line;
	}

	public function compile(Compiler $compiler, array $context, $inlineExpected)
	{
		return $this->php;
	}

	public function isSimpleValue()
	{
		return false;
	}
}