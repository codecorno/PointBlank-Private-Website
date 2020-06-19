<?php

namespace XF\Finder;

use XF\Mvc\Entity\Finder;

class RouteFilter extends Finder
{
	public function orderLength($field, $direction = 'DESC')
	{
		$expression = $this->expression('LENGTH(%s)', $field);
		$this->order($expression, $direction);

		return $this;
	}
} 