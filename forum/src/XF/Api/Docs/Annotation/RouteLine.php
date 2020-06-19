<?php

namespace XF\Api\Docs\Annotation;

class RouteLine extends AbstractLine
{
	public $method;
	public $route;

	public function __construct($method, $route)
	{
		$this->method = $method;
		$this->route = $route;
	}

	public function applyToBlock(AbstractBlock $block)
	{
		if ($block instanceof RouteBlock)
		{
			if ($this->method)
			{
				$block->method = $this->method;
			}
			if ($this->route)
			{
				$block->route = $this->route;
			}

			return true;
		}
		else
		{
			return false;
		}
	}
}