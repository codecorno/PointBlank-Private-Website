<?php

namespace XF\Api\Result;

class ArrayResult implements ResultInterface, \Countable
{
	/**
	 * @var array
	 */
	protected $result;

	public function __construct(array $result)
	{
		$this->result = $result;
	}

	public function getResult()
	{
		return $this->result;
	}

	public function setResult(array $result)
	{
		$this->result = $result;
	}

	public function render()
	{
		return $this->result;
	}

	public function count()
	{
		return count($this->result);
	}
}