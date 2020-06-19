<?php

namespace XF\Api\Docs\Annotation;

class ErrorLine extends AbstractLine
{
	public $error;
	public $description;

	public function __construct($error, $description = '')
	{
		$this->error = $error;
		$this->description = $description;
	}

	public function applyToBlock(AbstractBlock $block)
	{
		if ($block instanceof RouteBlock)
		{
			$block->errors[$this->error] = $this->description;

			return true;
		}
		else
		{
			return false;
		}
	}
}