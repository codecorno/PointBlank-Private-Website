<?php

namespace XF\Api\Docs\Annotation;

class GroupLine extends AbstractLine
{
	public $group;

	public function __construct($group)
	{
		$this->group = $group;
	}

	public function applyToBlock(AbstractBlock $block)
	{
		if ($block instanceof RouteBlock)
		{
			$block->group = $this->group;
			return true;
		}

		return false;
	}
}