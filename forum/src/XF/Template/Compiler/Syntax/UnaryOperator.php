<?php

namespace XF\Template\Compiler\Syntax;

use XF\Template\Compiler;

class UnaryOperator extends AbstractSyntax
{
	public $operator;
	public $syntax;

	public $map = [
		Compiler\Parser::T_OP_BANG => '!',
		Compiler\Parser::T_OP_MINUS => '-',
	];

	public function __construct($operator, AbstractSyntax $syntax, $line)
	{
		$this->operator = $operator;
		$this->syntax = $syntax;
		$this->line = $line;
	}

	public function compile(Compiler $compiler, array $context, $inlineExpected)
	{
		$context['escape'] = false;
		$code = $this->syntax->compile($compiler, $context, true);

		if (isset($this->map[$this->operator]))
		{
			$operator = $this->map[$this->operator];
			return "$operator$code";
		}
		else
		{
			throw new \InvalidArgumentException("Unexpected unary operator $this->operator");
		}
	}

	public function isSimpleValue()
	{
		return false;
	}
}