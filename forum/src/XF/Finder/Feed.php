<?php

namespace XF\Finder;

use XF\Mvc\Entity\Finder;

class Feed extends Finder
{
	public function isDue($time = null)
	{
		$expression = $this->expression('last_fetch + frequency');
		$this->where($expression, '<', $time ?: time());

		return $this;
	}
}