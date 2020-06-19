<?php

namespace XF\Template\Compiler\Syntax;

use XF\Template\Compiler;

class TernaryShortOperator extends AbstractSyntax
{
	public $condition;
	public $false;

	public function __construct(AbstractSyntax $condition, AbstractSyntax $false, $line)
	{
		$this->condition = $condition;
		$this->false = $false;
		$this->line = $line;
	}

	public function compile(Compiler $compiler, array $context, $inlineExpected)
	{
		$condition = $this->condition->compile($compiler, $context, true);
		$false = $this->false->compile($compiler, $context, true);

		if (!$this->condition->isSimpleValue())
		{
			$condition = "($condition)";
		}
		if (!$this->false->isSimpleValue())
		{
			$false = "($false)";
		}

		return "($condition ?: $false)";
	}

	public function isSimpleValue()
	{
		return true;
	}
}