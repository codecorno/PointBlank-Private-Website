<?php

namespace XF;

class Tree implements \ArrayAccess, \IteratorAggregate, \Countable
{
	protected $data;
	protected $parentMap;
	protected $parentIdKey;
	protected $root;

	public function __construct($data, $parentIdKey = 'parent_id', $root = 0)
	{
		$this->data = $data;
		$this->parentIdKey = $parentIdKey;
		$this->root = $root;

		$map = [];
		foreach ($data AS $id => $record)
		{
			$parentId = $record[$parentIdKey];
			if (!is_int($parentId) && !is_string($parentId))
			{
				$parentId = $root;
			}
			$map[$parentId][$id] = $id;
		}

		$this->parentMap = $map;
	}

	public function children($id = null)
	{
		if ($id === null)
		{
			$id = $this->root;
		}

		if (empty($this->parentMap[$id]))
		{
			return [];
		}

		$output = [];
		foreach ($this->parentMap[$id] AS $childId)
		{
			$output[$childId] = $this->createSubTree($childId);
		}

		return $output;
	}

	public function childIds($id = null)
	{
		if ($id === null)
		{
			$id = $this->root;
		}

		if (empty($this->parentMap[$id]))
		{
			return [];
		}

		return $this->parentMap[$id];
	}

	public function countChildren($id = null)
	{
		if ($id === null)
		{
			$id = $this->root;
		}

		if (empty($this->parentMap[$id]))
		{
			return 0;
		}

		return count($this->parentMap[$this->root]);
	}

	public function getData($id)
	{
		if (!isset($this->data[$id]))
		{
			return null;
		}

		return $this->data[$id];
	}

	public function getAllData()
	{
		return $this->data;
	}

	public function getRoot()
	{
		return $this->root;
	}

	public function getParentMap()
	{
		return $this->parentMap;
	}

	public function getParentMapSimplified()
	{
		$map = $this->parentMap;

		foreach ($map AS &$childIds)
		{
			$childIds = array_values($childIds);
		}

		return $map;
	}

	protected function createSubTree($id)
	{
		if (!isset($this->data[$id]))
		{
			return null;
		}

		return new SubTree($id, $this->data[$id], $this);
	}

	public function parent($id)
	{
		if (!isset($this->data[$id]))
		{
			return null;
		}

		return $this->createSubTree($this->data[$id][$this->parentIdKey]);
	}

	public function getPathTo($id, $withSelf = false)
	{
		if (!isset($this->data[$id]))
		{
			return null;
		}

		$matches = [];

		if ($withSelf)
		{
			$matches[$id] = $this->data[$id];
		}

		$parentId = $this->data[$id][$this->parentIdKey];

		while ($parentId !== $this->root && isset($this->data[$parentId]))
		{
			$matches[$parentId] = $this->data[$parentId];
			$parentId = $this->data[$parentId][$this->parentIdKey];
		}

		// since we go up the tree, the array needs to be reversed to be a top-down path
		$matches = array_reverse($matches, true);

		return $matches;
	}

	public function isNewParentValid($id, $newParentId)
	{
		if ($newParentId == $id)
		{
			// can't parent self
			return false;
		}
		if ($newParentId === $this->root)
		{
			// root is always ok
			return true;
		}

		if (!isset($this->data[$newParentId]))
		{
			return false;
		}
		if (!isset($this->data[$id]))
		{
			return true;
		}

		$walkId = $newParentId;
		do
		{
			if ($walkId == $id)
			{
				// found what would create a cycle
				return false;
			}

			if (!isset($this->data[$walkId]))
			{
				// it appears that we're trying to attach to something
				// that doesn't connect to the root
				return false;
			}

			$walkId = $this->data[$walkId][$this->parentIdKey];
		}
		while ($walkId !== $this->root);

		return true;
	}

	public function getFlattened($depth = 0, $rootId = null)
	{
		$output = [];

		$this->traverse(function($id, $record, $depth) use (&$output)
		{
			$output[$id] = [
				'record' => $record,
				'depth' => $depth
			];

			return true;
		}, $depth, $rootId);

		return $output;
	}

	public function getDescendants($rootId = null)
	{
		$output = [];

		$this->traverse(function($id, $record, $depth) use (&$output)
		{
			$output[$id] = $record;

			return true;
		}, 0, $rootId);

		return $output;
	}

	public function traverse(\Closure $callback, $depth = 0, $rootId = null)
	{
		if ($rootId === null)
		{
			$rootId = $this->root;
		}

		$f = function ($rootId, $depth) use (&$f, $callback)
		{
			foreach ($this->children($rootId) AS $id => $child)
			{
				$ret = $callback($id, $child['record'], $depth, $this);
				if ($ret !== false)
				{
					$f($id, $depth + 1);
				}
			}
		};

		$f($rootId, $depth);
	}

	/**
	 * @param \Closure|null $preOrder
	 * @param \Closure|null $postOrder
	 *
	 * @return Tree
	 */
	public function filter(\Closure $preOrder = null, \Closure $postOrder = null)
	{
		$f = function ($rootId, $depth) use (&$f, $preOrder, $postOrder)
		{
			$output = [];

			foreach ($this->children($rootId) AS $id => $child)
			{
				if ($preOrder)
				{
					$ret = $preOrder($id, $child['record'], $depth, $this);
					if (!$ret)
					{
						continue;
					}
				}

				$childOutput = $f($id, $depth + 1);

				if ($postOrder)
				{
					$ret = $postOrder($id, $child['record'], $depth, $childOutput, $this);
					if (!$ret)
					{
						continue;
					}
				}

				$output[$id] = $child['record'];
				$output += $childOutput;
			}

			return $output;
		};

		$output = $f($this->root, 0);
		return new self($output, $this->parentIdKey, $this->root);
	}

	public function getOrphans()
	{
		$orphans = $this->data;
		$this->traverse(function($id) use (&$orphans)
		{
			unset($orphans[$id]);
		});

		return $orphans;
	}

	public function offsetGet($offset)
	{
		return $this->createSubTree($offset);
	}

	public function offsetSet($offset, $value)
	{
		throw new \BadMethodCallException("Cannot set offsets in sub-tree");
	}

	public function offsetExists($offset)
	{
		return isset($this->data[$offset]);
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
		return $this->countChildren();
	}
}