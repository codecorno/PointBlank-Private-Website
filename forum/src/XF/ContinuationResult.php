<?php

namespace XF;

class ContinuationResult
{
	private $complete = false;
	private $continueData = null;

	public function __construct($complete, $continueData = null)
	{
		$this->complete = (bool)$complete;
		$this->continueData = $continueData;
	}

	public function isCompleted()
	{
		return $this->complete;
	}

	public function getContinueData()
	{
		return $this->continueData;
	}

	public static function completed()
	{
		return new self(true);
	}

	public static function continued($continueData = null)
	{
		return new self(false, $continueData);
	}
}