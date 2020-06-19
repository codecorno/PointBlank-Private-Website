<?php

namespace XF\Navigation;

class CompiledEntry
{
	public $id;

	public $dataExpression = '';
	public $dataSetup = '';

	public $conditionExpression = '';
	public $conditionSetup = '';

	public $globalSetup = '';

	/**
	 * @var CompiledEntry[]
	 */
	public $children = [];

	public function __construct($id, $dataExpression, $dataSetup = '')
	{
		$this->id = $id;
		$this->dataExpression = $dataExpression;
		$this->dataSetup = $dataSetup;
	}

	public function applyCondition($conditionExpression, $conditionSetup = '')
	{
		$this->conditionExpression = $conditionExpression;
		$this->conditionSetup = $conditionSetup;
	}

	public function setGlobalSetup($globalSetup)
	{
		$this->globalSetup = $globalSetup;
	}

	public function addChild(CompiledEntry $entry)
	{
		$this->children[] = $entry;
	}

	public function generateTreeCode($varPrefix, $idRefVar, array &$globalSetupParts, $depth = 1)
	{
		$output = '';
		$indent = str_repeat("\t", $depth);

		if ($this->globalSetup)
		{
			$globalSetupParts[$this->id] = $this->globalSetup;
		}

		if ($this->conditionExpression)
		{
			if ($this->conditionSetup)
			{
				$output .= $this->conditionSetup . "\n";
			}
			$output .= "{$indent}if ({$this->conditionExpression}) {\n";

			$depth++;
			$indent .= "\t";
		}

		if ($this->dataSetup)
		{
			$output .= $this->dataSetup . "\n";
		}

		$tempVar = '$__navTemp';

		$id = addcslashes($this->id, "\\'");
		$thisVar = "{$varPrefix}['" . $id . "']";

		$output .= "{$indent}{$tempVar} = {$this->dataExpression};\n"
			. "{$indent}if ({$tempVar}) {\n"
			. "{$indent}\t{$thisVar} = {$tempVar};\n"
			. "{$indent}\t{$idRefVar}['{$id}'] =& {$thisVar};\n";

		$childVar = "{$thisVar}['children']";
		$childOutput = '';
		foreach ($this->children AS $child)
		{
			$childOutput .= $child->generateTreeCode($childVar, $idRefVar, $globalSetupParts, $depth + 1);
		}

		if ($childOutput)
		{
			$output .= "{$indent}\tif (empty({$childVar})) { {$childVar} = []; }\n\n" . $childOutput;
		}

		$output .= "$indent}\n";

		if ($this->conditionExpression)
		{
			$depth--;
			$indent = str_repeat("\t", $depth);

			$output .= "{$indent}}\n";
		}

		$output .= "\n";

		return $output;
	}
}