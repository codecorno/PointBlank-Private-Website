<?php

namespace XF\Template\Compiler\Syntax;

use XF\Template\Compiler;

class FilterChain extends AbstractSyntax
{
	/**
	 * @var AbstractSyntax
	 */
	public $value;

	public $filters = [];

	public function __construct(AbstractSyntax $value, array $filters, $line)
	{
		$this->value = $value;
		$this->filters = $filters;
		$this->line = $line;
	}

	public function compile(Compiler $compiler, array $context, $inlineExpected)
	{
		$originalEscape = $context['escape'];
		$context['escape'] = false;

		$value = $this->value->compile($compiler, $context, true);
		return $this->compileFromCodeValue($value, $compiler, $context, $originalEscape);
	}

	public function compileFromCodeValue($codeValue, Compiler $compiler, array $context, $escape)
	{
		$context['escape'] = false;

		$list = ['array('];
		foreach ($this->filters AS $filter)
		{
			$list[] = 'array(' . $compiler->getStringCode($filter[0]) . ', array(';
			foreach ($filter[1] AS $argument)
			{
				/** @var $argument AbstractSyntax */
				$list[] = $argument->compile($compiler, $context, true);
				$list[] = ', ';
			}
			$list[] = ')),';
		}

		$list[] = ')';

		return $compiler->templaterVariable . '->filter('
			. $codeValue . ', ' . implode('', $list) . ', ' . ($escape ? 'true' : 'false')
			. ')';
	}
}