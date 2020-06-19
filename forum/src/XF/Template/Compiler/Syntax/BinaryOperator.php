<?php

namespace XF\Template\Compiler\Syntax;

use XF\Template\Compiler;

class BinaryOperator extends AbstractSyntax
{
	public $operator;
	public $lhs;
	public $rhs;

	public $map = [
		Compiler\Parser::T_OP_AND => 'AND',
		Compiler\Parser::T_OP_CONCAT => '.',
		Compiler\Parser::T_OP_DIVIDE => '/',
		Compiler\Parser::T_OP_EQ => '==',
		Compiler\Parser::T_OP_GT => '>',
		Compiler\Parser::T_OP_GTEQ => '>=',
		Compiler\Parser::T_OP_ID => '===',
		Compiler\Parser::T_OP_LT => '<',
		Compiler\Parser::T_OP_LTEQ => '<=',
		Compiler\Parser::T_OP_MINUS => '-',
		Compiler\Parser::T_OP_MULTIPLY => '*',
		Compiler\Parser::T_OP_MOD => '%',
		Compiler\Parser::T_OP_NE => '!=',
		Compiler\Parser::T_OP_NID => '!==',
		Compiler\Parser::T_OP_OR => 'OR',
		Compiler\Parser::T_OP_PLUS => '+'
	];

	public function __construct($operator, AbstractSyntax $lhs, AbstractSyntax $rhs, $line)
	{
		$this->operator = $operator;
		$this->lhs = $lhs;
		$this->rhs = $rhs;
		$this->line = $line;
	}

	public function compile(Compiler $compiler, array $context, $inlineExpected)
	{
		if ($this->operator !== Compiler\Parser::T_OP_CONCAT)
		{
			$context['escape'] = false;
		}

		$lhs = $this->lhs->compile($compiler, $context, true);
		$rhs = $this->rhs->compile($compiler, $context, true);

		if ($this->operator === Compiler\Parser::T_OP_INSTANCEOF)
		{
			return "{$compiler->templaterVariable}->isA($lhs, $rhs)";
		}

		if (isset($this->map[$this->operator]))
		{
			if (!$this->lhs->isSimpleValue())
			{
				$lhs = "($lhs)";
			}
			if (!$this->rhs->isSimpleValue())
			{
				$rhs = "($rhs)";
			}

			$operator = $this->map[$this->operator];
			return "$lhs $operator $rhs";
		}

		throw new \InvalidArgumentException("Unexpected binary operator $this->operator");
	}

	public function isSimpleValue()
	{
		return false;
	}
}