<?php

namespace XF;

class PreEscaped implements \JsonSerializable
{
	public $value;
	public $escapeType;

	public function __construct($value, $escapeType = 'html')
	{
		$this->value = strval($value);
		$this->escapeType = $escapeType;
	}

	public function __toString()
	{
		return $this->value;
	}

	public function jsonSerialize()
	{
		return $this->value;
	}
}