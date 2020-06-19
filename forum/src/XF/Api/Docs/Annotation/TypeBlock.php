<?php

namespace XF\Api\Docs\Annotation;

class TypeBlock extends AbstractBlock
{
	public $type;
	public $structure = [];

	public function applySeeBlock(AbstractBlock $see)
	{
		if (!($see instanceof TypeBlock))
		{
			throw new \LogicException("Can't apply see block of different type");
		}

		/** @var TypeBlock $see */

		$this->applyGenericSeeBlockParts($see);

		$this->type = $see->type ?: $this->type;
		$this->structure = array_replace($this->structure, $see->structure);

		return true;
	}
}