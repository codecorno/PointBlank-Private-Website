<?php

namespace XF;

class StringBuilder implements \JsonSerializable
{
	protected $parts = [];

	public function __construct(array $parts = [])
	{
		$this->parts = $parts;
	}

	public function append($string)
	{
		$this->parts[] = $string;
		return $this;
	}

	public function __toString()
	{
		return implode('', $this->parts);
	}

	public function jsonSerialize()
	{
		return implode('', $this->parts);
	}
}