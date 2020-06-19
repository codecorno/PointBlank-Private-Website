<?php

namespace XF;

class SubTree implements \ArrayAccess, \IteratorAggregate, \Countable
{
	public $id;
	public $record;
	protected $containingTree;

	public function __construct($id, $record, Tree $containingTree)
	{
		$this->id = $id;
		$this->record = $record;
		$this->containingTree = $containingTree;
	}

	public function children()
	{
		return $this->containingTree->children($this->id);
	}

	public function parent()
	{
		return $this->containingTree->parent($this->id);
	}

	public function __get($key)
	{
		return $this->offsetGet($key);
	}

	public function offsetGet($offset)
	{
		switch ($offset)
		{
			case 'children': return $this->children();
			case 'count': return $this->count();
			case 'parent': return $this->parent();
			case 'id': return $this->id;
			case 'record': return $this->record;

			default:
				throw new \InvalidArgumentException("Unknown sub-tree offset '$offset'");
		}
	}

	public function offsetSet($offset, $value)
	{
		throw new \BadMethodCallException("Cannot set offsets in sub-tree");
	}

	public function offsetExists($offset)
	{
		switch ($offset)
		{
			case 'children':
			case 'count':
			case 'parent':
			case 'id':
			case 'record';
				return true;

			default:
				return false;
		}
	}

	public function offsetUnset($offset)
	{
		throw new \BadMethodCallException("Cannot unset offsets in sub-tree");
	}

	public function getIterator()
	{
		return new \ArrayIterator($this->children());
	}

	public function count()
	{
		return $this->containingTree->countChildren($this->id);
	}
}