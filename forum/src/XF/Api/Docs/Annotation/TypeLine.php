<?php

namespace XF\Api\Docs\Annotation;

class TypeLine extends AbstractLine
{
	public $type;
	public $description;

	public function __construct($type, $description = '')
	{
		$this->type = $type;
		$this->description = $description;
	}

	public function applyToBlock(AbstractBlock $block)
	{
		if ($block instanceof TypeBlock)
		{
			$block->type = $this->type;

			if ($this->description)
			{
				$block->description = $this->description;
			}

			return true;
		}
		else
		{
			return false;
		}
	}
}