<?php

namespace XF\Api\Docs\Annotation;

class OutLine extends AbstractValueLine
{
	public function applyToBlock(AbstractBlock $block)
	{
		if ($block instanceof RouteBlock)
		{
			$block->outputs[$this->name] = $this;
			return true;
		}

		if ($block instanceof TypeBlock)
		{
			$block->structure[$this->name] = $this;
			return true;
		}

		return false;
	}
}