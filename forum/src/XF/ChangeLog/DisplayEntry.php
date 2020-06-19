<?php

namespace XF\ChangeLog;

class DisplayEntry implements \ArrayAccess
{
	public $label;
	public $old;
	public $new;

	public $protected = false;

	public function __construct($label, $old, $new)
	{
		$this->label = $label;
		$this->old = $old;
		$this->new = $new;
	}

	public function setIsProtected($protected)
	{
		$this->protected = $protected;
	}

	public function offsetGet($offset)
	{
		switch ($offset)
		{
			case 'label': return $this->label;
			case 'old': return $this->old;
			case 'new': return $this->new;
			case 'protected': return $this->protected;

			default:
				trigger_error("Unknown offset '$offset'", E_USER_NOTICE);
		}
	}

	public function offsetExists($offset)
	{
		switch ($offset)
		{
			case 'label':
			case 'old':
			case 'new':
			case 'protected':
				return true;

			default:
				return false;
		}
	}

	public function offsetSet($offset, $value)
	{
		throw new \LogicException("Cannot call offsetSet");
	}

	public function offsetUnset($offset)
	{
		throw new \LogicException("Cannot call offsetUnset");
	}
}