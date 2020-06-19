<?php

namespace XF\Api\Docs\Annotation;

class RouteBlock extends AbstractBlock
{
	public $method;

	public $route;

	public $group;

	/**
	 * @var InLine[]
	 */
	public $inputs = [];

	/**
	 * @var OutLine[]
	 */
	public $outputs = [];

	public $errors = [];

	public function applySeeBlock(AbstractBlock $see)
	{
		if (!($see instanceof RouteBlock))
		{
			throw new \LogicException("Can't apply see block of different type");
		}

		/** @var RouteBlock $see */

		$this->applyGenericSeeBlockParts($see);

		$this->method = $see->method ?: $this->method;
		$this->route = $see->route ?: $this->route;
		$this->inputs = array_replace($this->inputs, $see->inputs);
		$this->outputs = array_replace($this->outputs, $see->outputs);
		$this->errors = array_replace($this->errors, $see->errors);

		return true;
	}
}