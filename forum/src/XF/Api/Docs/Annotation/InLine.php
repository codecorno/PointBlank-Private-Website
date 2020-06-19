<?php

namespace XF\Api\Docs\Annotation;

class InLine extends AbstractValueLine
{
	public function applyToBlock(AbstractBlock $block)
	{
		if ($block instanceof RouteBlock)
		{
			$block->inputs[$this->name] = $this;
			return true;
		}

		return false;
	}
}