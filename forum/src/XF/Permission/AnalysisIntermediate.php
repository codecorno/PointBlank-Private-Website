<?php

namespace XF\Permission;

class AnalysisIntermediate implements \ArrayAccess
{
	protected $contentId;
	protected $contentTitle;

	protected $value;
	protected $type;
	protected $typeId;

	public function __construct($value, $type, $typeId = null, $contentId = null, $contentTitle = null)
	{
		$this->value = $value;
		$this->type = $type;
		$this->typeId = $typeId;

		if ($contentId)
		{
			$this->setContent($contentId, $contentTitle);
		}
	}

	public function setContent($contentId, $contentTitle)
	{
		$this->contentId = $contentId;
		$this->contentTitle = $contentTitle;
	}

	public function __get($name)
	{
		if (!property_exists($this, $name))
		{
			throw new \InvalidArgumentException("Unknown offset '$name'");
		}

		return $this->{$name};
	}

	public function __isset($name)
	{
		return property_exists($this, $name);
	}

	public function offsetExists($offset)
	{
		return property_exists($this, $offset);
	}

	public function offsetGet($offset)
	{
		if (!property_exists($this, $offset))
		{
			throw new \InvalidArgumentException("Unknown offset '$offset'");
		}

		return $this->{$offset};
	}

	public function offsetSet($offset, $value)
	{
		throw new \LogicException("Cannot set");
	}

	public function offsetUnset($offset)
	{
		throw new \LogicException("Cannot unset");
	}
}