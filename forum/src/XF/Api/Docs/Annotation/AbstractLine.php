<?php

namespace XF\Api\Docs\Annotation;

abstract class AbstractLine
{
	abstract public function applyToBlock(AbstractBlock $block);
}