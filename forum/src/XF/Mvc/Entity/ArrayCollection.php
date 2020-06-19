<?php

namespace XF\Mvc\Entity;

class ArrayCollection extends AbstractCollection
{
	public function __construct(array $entities)
	{
		$this->entities = $entities;
		$this->populated = true;
	}

	protected function populateInternal()
	{
	}
}