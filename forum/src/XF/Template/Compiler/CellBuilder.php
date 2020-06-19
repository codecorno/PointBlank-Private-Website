<?php

namespace XF\Template\Compiler;

use XF\Template\Compiler;

class CellBuilder
{
	/**
	 * @var \XF\Template\Compiler
	 */
	protected $compiler;

	protected $pendingInline = [];

	protected $tempVariable = null;

	protected $statements = [];

	public function __construct(Compiler $compiler)
	{
		$this->compiler = $compiler;
	}

	public function handleCell($type, $htmlCompiled, array $cell)
	{
		$indent = $this->compiler->indent();
		$cell['_type'] = "\n{$indent}\t'_type' => '{$type}',";
		$cell['html'] = "\n{$indent}\t'html' => {$htmlCompiled},";

		$code = "array(" . implode('', $cell) . "\n$indent)";

		if ($this->tempVariable)
		{
			$this->compiler->write("{$this->tempVariable}[] = {$code};");
		}
		else
		{
			$this->pendingInline[] = $code;
		}
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