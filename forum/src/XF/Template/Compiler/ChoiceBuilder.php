<?php

namespace XF\Template\Compiler;

use XF\Template\Compiler;

class ChoiceBuilder
{
	/**
	 * @var \XF\Template\Compiler
	 */
	protected $compiler;

	protected $pendingInline = [];

	protected $tempVariable = null;

	protected $currentOptGroupVariable = null;

	protected $statements = [];

	public function __construct(Compiler $compiler)
	{
		$this->compiler = $compiler;
	}

	public function handleOptionTag(array $choice)
	{
		$indent = $this->compiler->indent();
		$choice['_type'] = "\n$indent\t'_type' => 'option',";

		if (isset($choice['_dependent']))
		{
			$dependent = $choice['_dependent'];
			$choice['_dependent'] = "\n$indent\t'_dependent' => array(" . implode(', ', $dependent) . '),';
		}

		$code = "array(" . implode('', $choice) . "\n$indent)";

		if ($this->tempVariable)
		{
			if ($this->currentOptGroupVariable)
			{
				$this->compiler->write("{$this->tempVariable}[{$this->currentOptGroupVariable}]['options'][] = {$code};");
			}
			else
			{
				$this->compiler->write("{$this->tempVariable}[] = {$code};");
			}
		}
		else
		{
			$this->pendingInline[] = $code;
		}
	}

	public function handleOptionsTag($optionsCodeInline)
	{
		$compiler = $this->compiler;

		if (!$this->tempVariable && !$this->pendingInline)
		{
			// empty so just setup a temp var for writes and alias
			$this->tempVariable = $compiler->getTempVar();
			$compiler->write("{$this->tempVariable} = {$compiler->templaterVariable}->mergeChoiceOptions(array(), {$optionsCodeInline});");
		}
		else
		{
			// need to merge
			$targetVar = $this->forceTempVariable();
			if ($this->currentOptGroupVariable)
			{
				$targetVar = "{$targetVar}[{$this->currentOptGroupVariable}]['options']";
			}

			$compiler->write("{$targetVar} = {$compiler->templaterVariable}->mergeChoiceOptions({$targetVar}, {$optionsCodeInline});");
		}
	}

	public function startOptGroup(array $attributesCode)
	{
		if ($this->currentOptGroupVariable)
		{
			throw new \LogicException("Cannot have multiple optgroups nested");
		}

		$this->forceTempVariable();

		$compiler = $this->compiler;
		$indent = $compiler->indent();

		$attributesCode['_type'] = "\n$indent\t'_type' => 'optgroup',";
		$attributesCode['options'] = "\n$indent\t'options' => array(),";
		$code = "array(" . implode('', $attributesCode) . "\n$indent)";

		$this->currentOptGroupVariable = $this->compiler->getTempVar();

		$compiler->write("{$this->tempVariable}[] = {$code};");
		$compiler->write("end({$this->tempVariable}); {$this->currentOptGroupVariable} = key({$this->tempVariable});");

		return $this->currentOptGroupVariable;
	}

	public function inOptGroup()
	{
		return ($this->currentOptGroupVariable ? true : false);
	}

	public function endOptGroup()
	{
		if (!$this->currentOptGroupVariable)
		{
			throw new \LogicException("Tried to close an optgroup that was not open");
		}

		$this->currentOptGroupVariable = null;
	}

	public function toInline()
	{
		if ($this->tempVariable)
		{
			return $this->tempVariable;
		}
		else
		{
			return "array(" . implode(",\n" . $this->compiler->indent(), $this->pendingInline) . ")";
		}
	}

	public function forceTempVariable()
	{
		if (!$this->tempVariable)
		{
			$this->tempVariable = $this->compiler->getTempVar();

			$code = "array(" . implode("\n,", $this->pendingInline) . ")";
			$this->compiler->write("{$this->tempVariable} = $code;");

			$this->pendingInline = [];
		}

		return $this->tempVariable;
	}
}