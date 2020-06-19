<?php

namespace XF\Template\Compiler\Syntax;

use XF\Template\Compiler;

class ArrayExpression extends AbstractSyntax
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
		$context['escape'] = false;

		$code = ['array('];
		foreach ($this->parts AS $part)
		{
			$code[] = $part->compile($compiler, $context, true);
			$code[] = ', ';
		}
		$code[] = ')';

		return implode('', $code);
	}
}