<?php

namespace XF\Api\Docs\Annotation;

abstract class AbstractBlock
{
	public $description;
	public $unknownLine = [];
	public $incomplete = false;

	abstract public function applySeeBlock(AbstractBlock $see);

	public function addUnknownLine($line)
	{
		$this->unknownLine[] = $line;
	}

	protected function applyGenericSeeBlockParts(AbstractBlock $see)
	{
		$this->description = $see->description ?: $this->description;

		foreach ($see->unknownLine AS $line)
		{
			$this->unknownLine[] = $line;
		}
	}
}