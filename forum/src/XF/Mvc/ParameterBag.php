<?php

namespace XF\Mvc;

class ParameterBag implements \ArrayAccess
{
	protected $params;

	public function __construct(array $params = [])
	{
		$this->params = $params;
	}

	public function offsetGet($key)
	{
		return isset($this->params[$key]) ? $this->params[$key] : null;
	}

	public function __get($key)
	{
		return $this->offsetGet($key);
	}

	public function get($key, $fallback = null)
	{
		return array_key_exists($key, $this->params) ? $this->params[$key] : $fallback;
	}

	public function offsetSet($key, $value)
	{
		$this->params[$key] = $value;
	}

	public function __set($key, $value)
	{
		$this->offsetSet($key, $value);
	}

	public function offsetExists($key)
	{
		return array_key_exists($key, $this->params);
	}

	public function offsetUnset($key)
	{
		unset($this->params[$key]);
	}

	public function params()
	{
		return $this->params;
	}
}