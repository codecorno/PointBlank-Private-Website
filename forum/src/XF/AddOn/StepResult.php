<?php

namespace XF\AddOn;

class StepResult
{
	public $complete;
	public $params;
	public $step;
	public $version; // only valid for upgrades

	public function __construct($complete, array $params = [], $step = null, $version = null)
	{
		$this->complete = (bool)$complete;
		$this->params = $params;
		$this->step = $step;
		$this->version = $version;
	}
}