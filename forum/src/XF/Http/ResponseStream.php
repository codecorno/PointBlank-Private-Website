<?php

namespace XF\Http;

class ResponseStream
{
	protected $resource = null;
	protected $length = null;

	protected $contents = null;

	public function __construct($resource, $length = null)
	{
		if (!is_resource($resource))
		{
			throw new \InvalidArgumentException("Must pass valid resource in");
		}

		$this->resource = $resource;
		$this->length = $length;
	}

	public function __toString()
	{
		return $this->getContents();
	}

	public function output()
	{
		if ($this->contents === null)
		{
			fpassthru($this->resource);
		}
		else
		{
			echo $this->contents;
		}
	}

	public function getStream()
	{
		return $this->resource;
	}

	public function getLength()
	{
		return $this->contents === null ? $this->length : strlen($this->contents);
	}

	public function getContents()
	{
		if ($this->contents === null)
		{
			$this->contents = stream_get_contents($this->resource);
		}

		return $this->contents;
	}
}