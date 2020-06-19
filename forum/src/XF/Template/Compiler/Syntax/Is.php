<?php

namespace XF\Template\Compiler\Syntax;

use XF\Template\Compiler;

class Is extends AbstractSyntax
{
	public $positiveCheck;
	public $lhs;
	public $check;
	public $arguments;

	public function __construct(AbstractSyntax $lhs, $positiveCheck, $check, array $arguments, $line)
	{
		$this->lhs = $lhs;
		$this->positiveCheck = $positiveCheck;
		$this->check = $check;
		$this->arguments = $arguments;
		$this->line = $line;
	}

	public function compile(Compiler $compiler, array $context, $inlineExpected)
	{
		$context['escape'] = false;

		$lhs = $this->lhs->compile($compiler, $context, true);
		$check = $compiler->getStringCode($this->check);

		$argCode = 'array(';
		foreach ($this->arguments AS $argument)
		{
			/** @var $argument AbstractSyntax */
			$argCode .= $argument->compile($compiler, $context, true) . ', ';
		}
		$argCode .= ')';

		$code = "{$compiler->templaterVariable}->test($lhs, $check, $argCode)";
		if (!$this->positiveCheck)
		{
			$code = "!$code";
		}

		return $code;
	}
}