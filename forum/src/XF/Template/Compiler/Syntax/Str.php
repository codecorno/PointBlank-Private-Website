<?php

namespace XF\Template\Compiler\Syntax;

use XF\Template\Compiler;

class Str extends AbstractSyntax
{
	public $content = '';

	public function __construct($content, $line)
	{
		$this->content = $content;
		$this->line = $line;
	}

	public function compile(Compiler $compiler, array $context, $inlineExpected)
	{
		return $compiler->getStringCode($this->content);
	}

	public function isEmpty()
	{
		return (trim($this->content) === '');
	}
}