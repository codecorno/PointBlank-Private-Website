<?php

namespace XF\Api\Docs\Annotation;

class SeeLine extends AbstractLine
{
	/**
	 * @var AbstractBlock
	 */
	public $see;

	public function __construct(AbstractBlock $see)
	{
		$this->see = $see;
	}

	public function applyToBlock(AbstractBlock $block)
	{
		return $block->applySeeBlock($this->see);
	}
}