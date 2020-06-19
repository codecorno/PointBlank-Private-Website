<?php

namespace XF\Template\Compiler\Syntax;

use XF\Template\Compiler;

class Func extends AbstractSyntax
{
	public $name = '';

	/**
	 * @var AbstractSyntax[]
	 */
	public $arguments = [];

	public function __construct($name, array $arguments, $line)
	{
		$this->name = $name;
		$this->arguments = $arguments;
		$this->line = $line;
	}

	public function compile(Compiler $compiler, array $context, $inlineExpected)
	{
		$specialFunction = $compiler->getFunction($this->name);
		if ($specialFunction)
		{
			return $specialFunction->compile($this, $compiler, $context);
		}

		$escape = $context['escape'];
		$context['escape'] = false;

		$code = [$compiler->templaterVariable . '->func(' . $compiler->getStringCode($this->name) . ', array('];
		foreach ($this->arguments AS $argument)
		{
			$code[] = $argument->compile($compiler, $context, true);
			$code[] = ', ';
		}
		$code[] = '), ' . ($escape ? 'true' : 'false') . ')';

		return implode('', $code);
	}

	public function compileFunctionPreEscaped(Compiler $compiler, array $context)
	{
		$context['escape'] = true;

		$code = [$compiler->templaterVariable . '->func(' . $compiler->getStringCode($this->name) . ', array('];
		foreach ($this->arguments AS $argument)
		{
			$code[] = $argument->compile($compiler, $context, true);
			$code[] = ', ';
		}
		$code[] = '), false)';

		return implode('', $code);
	}

	public function assertArgumentCount($min, $max = null)
	{
		if ($max !== null && $max < $min)
		{
			throw new \InvalidArgumentException("Max number of arguments must be greater than or equal to min");
		}

		$count = count($this->arguments);

		if ($max === null)
		{
			if ($count != $min)
			{
				throw $this->exception(\XF::phrase('function_x_expected_y_arguments', ['name' => $this->name, 'min' => $min]));
			}
		}
		else if ($count < $min || $count > $max)
		{
			throw $this->exception(\XF::phrase('function_x_expects_between_y_and_z_arguments', ['name' => $this->name, 'min' => $min, 'max' => $max]));
		}
	}
}