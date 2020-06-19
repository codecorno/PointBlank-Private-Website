<?php

namespace XF\Template\Compiler;

use XF\Template\Compiler;

class CodeScope
{
	protected $tempVarFormat = '$__compilerTemp';
	protected $tempVarId = 1;

	/**
	 * @var \XF\Template\Compiler
	 */
	protected $compiler;

	protected $output = [];
	protected $varStack = [];
	protected $inlinePending = [];
	protected $indent = 1;

	public function __construct($initialVar, Compiler $compiler)
	{
		$this->varStack[] = $initialVar;
		$this->compiler = $compiler;
	}

	public function getOutput()
	{
		$this->flushPending();
		return $this->output;
	}

	public function clearOutput()
	{
		$this->flushPending();
		$this->output = [];
	}

	public function write($code)
	{
		$this->flushPending();
		$this->output[] = $this->indent() . $code;

		return $this;
	}

	public function inline($code)
	{
		if (is_string($code) && $code != '')
		{
			$this->inlinePending[] = $code;
		}

		return $this;
	}

	public function flushPending()
	{
		if ($this->inlinePending)
		{
			$code = $this->compiler->simplifyInlineCode(implode(' . ', $this->inlinePending));
			$this->output[] = $this->indent() . $this->currentVar() . ' .= ' . $code . ';';
			$this->inlinePending = [];
		}

		return $this;
	}

	public function currentVar()
	{
		return $this->varStack[0];
	}

	public function getTempVar()
	{
		$name = $this->tempVarFormat . $this->tempVarId;
		$this->tempVarId++;
		return $name;
	}

	public function pushTempVar($init = true)
	{
		$var = $this->getTempVar();
		$this->pushVar($var);
		if ($init)
		{
			$this->write("$var = '';");
		}

		return $var;
	}

	public function pushVar($var)
	{
		$this->flushPending();

		array_unshift($this->varStack, $var);
		return $this;
	}

	public function popVar()
	{
		$this->flushPending();

		return array_shift($this->varStack);
	}

	public function indent()
	{
		return str_repeat("\t", $this->indent);
	}

	public function pushIndent()
	{
		$this->flushPending();

		$this->indent++;

		return $this;
	}

	public function popIndent()
	{
		$this->flushPending();

		$this->indent--;
		if ($this->indent < 1)
		{
			$this->indent = 1;
		}

		return $this;
	}
}