<?php

namespace XF\Widget;

use XF\Entity\Widget;

class WidgetCompiler
{
	/**
	 * @var \XF\Template\Compiler
	 */
	protected $templateCompiler;

	protected $compilerContext = ['escape' => false];

	protected $widgetVar = '$__widget';
	protected $optionsVar = '$__options';

	public function __construct(\XF\Template\Compiler $templateCompiler)
	{
		$this->templateCompiler = $templateCompiler;
	}

	public function compile(Widget $widget)
	{
		$widgetCode = '';

		$compiled = $this->compileEntry($widget);
		$widgetCode .= $compiled->generateWidgetCode($this->widgetVar, $this->optionsVar);

		return $this->wrapFinalCode($widgetCode);
	}

	public function compileEntry(Widget $widget)
	{
		$displayCondition = $widget->display_condition;

		$displayExpression = $this->compileExpressionValue($displayCondition, '');

		$compiled = new WidgetCompiledEntry($widget->widget_key);
		$compiled->applyCondition($displayExpression);

		return $compiled;
	}

	protected function wrapFinalCode($widgetCode)
	{
		$templaterVariable = $this->templateCompiler->templaterVariable;
		$variableContainer = $this->templateCompiler->variableContainer;

		return "return function({$templaterVariable}, array {$variableContainer}, array {$this->optionsVar} = [])
{
{$widgetCode}

	return {$this->widgetVar};
};";
	}

	public function initializeCompilation()
	{
		$this->templateCompiler->reset();
	}

	public function getIndenter()
	{
		return $this->templateCompiler->indent();
	}

	public function flushIntermediateCode()
	{
		$scope = $this->templateCompiler->getCodeScope();
		$output = $scope->getOutput();
		$scope->clearOutput();

		return implode("\n", $output);
	}

	public function compileExpressionValue($string, $defaultCode, $forceValid = true)
	{
		if (!strlen($string))
		{
			return $defaultCode;
		}

		try
		{
			$compiler = $this->templateCompiler;
			$ast = $compiler->compileToAst('{{ ' . $string . ' }}');
			return $compiler->compileInlineList($ast->children, $this->compilerContext);
		}
		catch (\XF\Template\Compiler\Exception $e)
		{
			if ($forceValid)
			{
				return $defaultCode;
			}
			else
			{
				throw $e;
			}
		}
	}
}