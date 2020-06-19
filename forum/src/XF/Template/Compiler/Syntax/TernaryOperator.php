<?php

namespace XF\Template\Compiler\Syntax;

use XF\Template\Compiler;

class TernaryOperator extends AbstractSyntax
{
	public $condition;
	public $true;
	public $false;

	public function __construct(AbstractSyntax $condition, AbstractSyntax $true, AbstractSyntax $false, $line)
	{
		$this->condition = $condition;
		$this->true = $true;
		$this->false = $false;
		$this->line = $line;
	}

	public function compile(Compiler $compiler, array $context, $inlineExpected)
	{
		$rawContext = $context;
		$rawContext['escape'] = false;

		$condition = $this->condition->compile($compiler, $rawContext, true);
		$true = $this->true->compile($compiler, $context, true);
		$false = $this->false->compile($compiler, $context, true);

		if (!$this->condition->isSimpleValue())
		{
			$condition = "($condition)";
		}
		if (!$this->true->isSimpleValue())
		{
			$true = "($true)";
		}
		if (!$this->false->isSimpleValue())
		{
			$false = "($false)";
		}

		return "($condition ? $true : $false)";
	}

	public function isSimpleValue()
	{
		return true;
	}
}