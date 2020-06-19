<?php

namespace XF\Api\Docs\Annotation;

class IncompleteLine extends AbstractLine
{
	public function applyToBlock(AbstractBlock $block)
	{
		$block->incomplete = true;

		return true;
	}
}