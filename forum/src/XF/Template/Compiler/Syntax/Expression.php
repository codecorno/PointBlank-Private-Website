<?php

namespace XF\Template\Compiler\Syntax;

use XF\Template\Compiler;

class Expression extends AbstractSyntax
{
	/**
	 * @var AbstractSyntax
	 */
	public $expression;

	public function __construct(AbstractSyntax $expression, $line)
	{
		$this->expression = $expression;
		$this->line = $line;
	}

	public function compile(Compiler $compiler, array $context, $inlineExpected)
	{
		$value = $this->expression->compile($compiler, $context, true);
		if (!$this->expression->isSimpleValue())
		{
			$value = "($value)";
		}

		return $value;
	}

	public function isSimpleValue()
	{
		return $this->expression->isSimpleValue();
	}
}