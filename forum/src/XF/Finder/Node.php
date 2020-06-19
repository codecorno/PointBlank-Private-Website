<?php

namespace XF\Finder;

use XF\Mvc\Entity\Finder;

class Node extends Finder
{
	public function descendantOf(\XF\Entity\Node $node)
	{
		$this->where('lft', '>', $node->lft)
			->where('rgt', '<', $node->rgt);

		return $this;
	}

	public function listable()
	{
		$this->where('display_in_list', 1);

		return $this;
	}
}