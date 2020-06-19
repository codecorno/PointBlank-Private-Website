<?php

namespace XF\Template\Compiler\Syntax;

use XF\Template\Compiler;

class Variable extends AbstractSyntax
{
	public $name = '';
	public $dimensions = [];
	public $filters = [];

	public function __construct($name, array $dimensions, array $filters, $line)
	{
		$this->name = $name;
		$this->dimensions = $dimensions;
		$this->filters = $filters;
		$this->line = $line;
	}

	public function compile(Compiler $compiler, array $context, $inlineExpected)
	{
		$childContext['escape'] = false;

		$prevIsFunction = false;
		$code = [$compiler->variableContainer . '[' . $compiler->getStringCode($this->name) . ']'];
		foreach ($this->dimensions AS $dimension)
		{
			/** @var AbstractSyntax $syntax */
			$syntax = $dimension[1];

			switch ($dimension[0])
			{
				case 'array':
					if ($prevIsFunction)
					{
						$var = implode('', $code);
						$code = [
							$compiler->templaterVariable . '->arrayKey(' . $var . ', ' . $syntax->compile($compiler, $childContext, true) . ')'
						];
						$prevIsFunction = true;
					}
					else
					{
						$code[] = '[' . $syntax->compile($compiler, $childContext, true) . ']';
					}
					break;

				case 'object':
					$code[] = '->{' . $syntax->compile($compiler, $childContext, true) . '}';
					break;

				case 'function':
					/** @var Func $syntax */
					$var = implode('', $code);

					if (!$this->isFunctionCallAllowed($syntax->name))
					{
						throw $this->exception(\XF::phrase('function_x_may_not_be_called_in_template', ['name' => $syntax->name]));
					}

					$name = $compiler->getStringCode($syntax->name);
					$code = [
						$compiler->templaterVariable . '->method(' . $var . ', ' . $name . ', array('
					];
					foreach ($syntax->arguments AS $argument)
					{
						$code[] = $argument->compile($compiler, $childContext, true);
						$code[] = ', ';
					}
					$code[] = '))';
					$prevIsFunction = true;
					break;

				default:
					throw new \InvalidArgumentException("Unexpected variable dimension type $dimension[0]");
			}
		}

		$var = implode('', $code);

		if ($this->filters)
		{
			$chain = new FilterChain($this, $this->filters, $this->line);
			return $chain->compileFromCodeValue($var, $compiler, $context, $context['escape']);
		}
		else
		{
			return $context['escape'] ? $compiler->templaterVariable . '->escape(' . $var . ')' : $var;
		}
	}

	protected function isFunctionCallAllowed($name)
	{
		return \XF\Util\Php::nameIndicatesReadOnly($name);
	}

	public function compileToVarContainer($targetVar, Compiler $compiler, array $context, $inlineExpected)
	{
		$temp = $compiler->variableContainer;
		$compiler->variableContainer = $targetVar;
		$output = $this->compile($compiler, $context, $inlineExpected);
		$compiler->variableContainer = $temp;

		return $output;
	}

	public function isSimple()
	{
		if ($this->filters)
		{
			return false;
		}

		foreach ($this->dimensions AS $dimension)
		{
			if ($dimension[0] != 'array')
			{
				return false;
			}
		}

		return true;
	}
}