<?php

namespace XF\Api\Docs\Annotation;

class DescriptionLine extends AbstractLine
{
	public $description;

	public function __construct($description)
	{
		$this->description = $description;
	}

	public function applyToBlock(AbstractBlock $block)
	{
		$block->description = $this->description;

		return true;
	}
}