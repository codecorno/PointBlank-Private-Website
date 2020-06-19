<?php

namespace XF;

class SimpleCache implements \ArrayAccess
{
	/**
	 * @var array
	 */
	protected $data;

	public function __construct(array $simpleCacheData)
	{
		$this->data = $simpleCacheData;
	}

	public function getSet($addOnId)
	{
		return new \XF\SimpleCacheSet($this, $addOnId);
	}

	public function getRawSet($addOnId)
	{
		return isset($this->data[$addOnId]) ? $this->data[$addOnId] : [];
	}

	public function getValue($addOnId, $key)
	{
		return $this->keyExists($addOnId, $key) ? $this->data[$addOnId][$key] : null;
	}

	public function keyExists($addOnId, $key)
	{
		return isset($this->data[$addOnId][$key]);
	}

	public function setValue($addOnId, $key, $value)
	{
		$this->data[$addOnId][$key] = $value;
		$this->save();
	}

	public function deleteValue($addOnId, $key)
	{
		unset($this->data[$addOnId][$key]);
		$this->save();
	}

	public function deleteSet($addOnId)
	{
		unset($this->data[$addOnId]);
		$this->save();
	}

	protected function save()
	{
		\XF::app()->registry()->set('simpleCache', $this->data);
	}

	public function offsetExists($addOnId)
	{
		return isset($this->data[$addOnId]);
	}

	public function offsetGet($addOnId)
	{
		return $this->getSet($addOnId);
	}

	public function __get($addOnId)
	{
		return $this->getSet($addOnId);
	}

	public function offsetSet($offset, $value)
	{
		throw new \LogicException('Values should only be set using a SimpleCacheSet object.');
	}

	function __set($name, $value)
	{
		$this->offsetSet($name, $value);
	}

	public function offsetUnset($addOnId)
	{
		$this->deleteSet($addOnId);
	}
}