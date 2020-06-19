<?php

namespace XF\Navigation;

class Compiler
{
	/**
	 * @var \XF\Template\Compiler
	 */
	protected $templateCompiler;

	protected $compilerContext = ['escape' => false];

	protected $treeVar = '$__tree';
	protected $flatVar = '$__flat';
	protected $selectedVar = '$__selectedNav';

	public function __construct(\XF\Template\Compiler $templateCompiler)
	{
		$this->templateCompiler = $templateCompiler;
	}

	public function getTreeVar()
	{
		return $this->treeVar;
	}

	public function getTemplaterVar()
	{
		return $this->templateCompiler->templaterVariable;
	}

	public function getVarContainer()
	{
		return $this->templateCompiler->variableContainer;
	}

	public function getSelectedVar()
	{
		return $this->selectedVar;
	}

	public function compileTree(\XF\Tree $navTree)
	{
		$treeCode = '';
		$globalSetup = [];

		foreach ($navTree AS $subTree)
		{
			$compiled = $this->compileSubTree($subTree);
			if ($compiled)
			{
				$treeCode .= $compiled->generateTreeCode($this->treeVar, $this->flatVar, $globalSetup);
			}
		}

		if ($globalSetup)
		{
			$globalSetupCode = "\t" . implode("\n\t", $globalSetup) . "\n";
		}
		else
		{
			$globalSetupCode = '';
		}

		return $this->wrapFinalCode($treeCode, $globalSetupCode);
	}

	protected function compileSubTree(\XF\SubTree $subTree)
	{
		/** @var \XF\Entity\Navigation $entry */
		$entry = $subTree->record;
		if (!$entry->enabled)
		{
			return null;
		}

		$compiled = $entry->getCompiledEntry();
		foreach ($subTree->children AS $childTree)
		{
			$child = $this->compileSubTree($childTree);
			if ($child)
			{
				$compiled->addChild($child);
			}
		}

		return $compiled;
	}

	protected function wrapFinalCode($treeCode, $globalSetupCode)
	{
		$templaterVar = $this->templateCompiler->templaterVariable;
		$variableContainer = $this->templateCompiler->variableContainer;
		$selectedVar = $this->selectedVar;

		return "return function({$templaterVar}, {$selectedVar}, array {$variableContainer})
{
	{$this->treeVar} = [];
	{$this->flatVar} = [];

{$globalSetupCode}
{$treeCode}

	return [
		'tree' => {$this->treeVar},
		'flat' => {$this->flatVar}
	];
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

	public function getStringCode($string)
	{
		return $this->templateCompiler->getStringCode($string);
	}

	public function getIntermediateCode()
	{
		return implode("\n", $this->templateCompiler->getOutput());
	}

	public function flushIntermediateCode()
	{
		$scope = $this->templateCompiler->getCodeScope();
		$output = $scope->getOutput();
		$scope->clearOutput();

		return implode("\n", $output);
	}

	public function compileStringValue($string, $forceValid = true)
	{
		if (!strlen($string))
		{
			return "''";
		}

		try
		{
			$compiler = $this->templateCompiler;
			$ast = $compiler->compileToAst($string);

			return $compiler->compileInlineList($ast->children, $this->compilerContext);
		}
		catch (\XF\Template\Compiler\Exception $e)
		{
			if ($forceValid)
			{
				return "''";
			}
			else
			{
				throw $e;
			}
		}
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

	public function compileArrayValue(array $elements, $forceValid = true)
	{
		if (!$elements)
		{
			return '[]';
		}

		$compiler = $this->templateCompiler;

		$output = "[\n";
		foreach ($elements AS $name => $string)
		{
			$name = $compiler->getStringCode($name);
			$value = $this->compileStringValue($string, $forceValid);
			$output .= $compiler->indent() . "\t\t{$name} => {$value},\n";
		}
		$output .= $compiler->indent() . "\t]";

		return $output;
	}

	public function validateStringValue($string, &$errorMessage = null)
	{
		$compiler = $this->templateCompiler;
		$compiler->reset();

		try
		{
			$this->compileStringValue($string, false);
			return true;
		}
		catch (\XF\Template\Compiler\Exception $e)
		{
			$errorMessage = $e->getMessage();
			return false;
		}
	}

	public function validateExpressionValue($expression, &$errorMessage = null)
	{
		$compiler = $this->templateCompiler;
		$compiler->reset();

		try
		{
			$this->compileExpressionValue($expression, 'true', false);
			return true;
		}
		catch (\XF\Template\Compiler\Exception $e)
		{
			$errorMessage = $e->getMessage();
			return false;
		}
	}

	public function validateArrayValue(array $array, &$errorMessage = null)
	{
		$compiler = $this->templateCompiler;
		$compiler->reset();

		try
		{
			$this->compileArrayValue($array, false);
			return true;
		}
		catch (\XF\Template\Compiler\Exception $e)
		{
			$errorMessage = $e->getMessage();
			return false;
		}
	}
}
