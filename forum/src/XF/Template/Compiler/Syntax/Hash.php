<?php

namespace XF\Template\Compiler\Syntax;

use XF\Template\Compiler;

class Hash extends AbstractSyntax
{
	/**
	 * @var array
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
			$code[] = $part[0]->compile($compiler, $context, true);
			$code[] = ' => ';
			$code[] = $part[1]->compile($compiler, $context, true);
			$code[] = ', ';
		}
		$code[] = ')';

		return implode('', $code);
	}
}