<?php

namespace XF\CustomField;

class DefinitionSet implements \ArrayAccess, \IteratorAggregate, \Countable
{
	protected $filters = [];

	protected $fieldDefinitions;

	public function __construct(array $fieldDefinitions, array $filters = [])
	{
		$this->addDefaultFilters();
		$this->filters = array_merge($filters, $this->filters);

		$this->fieldDefinitions = $fieldDefinitions;
	}

	protected function addDefaultFilters()
	{
		$this->addFilter('group', function(array $field, $group)
		{
			if (is_array($group))
			{
				return in_array($field['display_group'], $group);
			}
			else
			{
				return ($field['display_group'] == $group);
			}
		});

		$this->addFilter('only', function(array $field, $onlyFields)
		{
			if (!is_array($onlyFields))
			{
				$onlyFields = [$onlyFields];
			}
			return in_array($field['field_id'], $onlyFields);
		});

		$this->addFilter('editable', function(array $field, Set $set, $editMode)
		{
			if (!$set)
			{
				return false;
			}

			$definition = $set->getField($field['field_id']);
			return ($definition && $definition->isEditable($set[$field['field_id']], $editMode));
		});

		$this->addFilter('value', function(array $field, Set $set)
		{
			if (!$set)
			{
				return false;
			}

			$definition = $set->getField($field['field_id']);
			return ($definition && $definition->hasValue($set[$field['field_id']]));
		});
	}

	public function addFilter($name, \Closure $filter)
	{
		$this->filters[$name] = $filter;

		return $this;
	}

	/**
	 * @param string|array $filters
	 * @param null|mixed $args
	 *
	 * @return DefinitionSet
	 */
	public function filter($filters, $args = null)
	{
		$filteredDefs = $this->fieldDefinitions;

		if (!is_array($filters))
		{
			if ($args !== null)
			{
				$filters = [$filters => $args];
			}
			else
			{
				$filters = [$filters];
			}
		}
		else if ($args !== null)
		{
			throw new \LogicException('Args must be null when the filters list is an array');
		}

		foreach ($filters AS $key => $filter)
		{
			$arg = null;

			if (is_string($key))
			{
				$arg = $filter;
				$filter = $key;
			}

			$filteredDefs = $this->executeFilter($filteredDefs, $filter, $arg);
		}

		$new = clone $this;
		$new->fieldDefinitions = $filteredDefs;

		return $new;
	}

	public function filterGroup($group)
	{
		return $this->filter('group', [$group]);
	}

	public function filterOnly($onlyInclude)
	{
		return $this->filter('only', [$onlyInclude]);
	}

	public function filterEditable(Set $set, $editMode)
	{
		return $this->filter('editable', [$set, $editMode]);
	}

	public function filterWithValue(Set $set)
	{
		return $this->filter('value', [$set]);
	}

	protected function executeFilter(array $filteredDefs, $filter, $arg)
	{
		if (isset($this->filters[$filter]) && is_callable($this->filters[$filter]))
		{
			$filteredDefs = \XF\Util\Arr::arrayFilterArgs(
				$filteredDefs,
				$this->filters[$filter],
				$arg
			);
		}

		return $filteredDefs;
	}

	/**
	 * @return Definition[]
	 */
	public function getFieldDefinitions()
	{
		$definitions = [];

		foreach ($this->fieldDefinitions AS $key => $def)
		{
			$definitions[$key] = $this->getDefinition($def);
		}

		return $definitions;
	}

	public function get($key)
	{
		return $this->offsetGet($key);
	}
	
	public function __get($key)
	{
		return $this->offsetGet($key);
	}

	public function __isset($key)
	{
		return $this->offsetExists($key);
	}

	public function offsetGet($offset)
	{
		if (!isset($this->fieldDefinitions[$offset]))
		{
			throw new \LogicException("Unknown field '$offset'");
		}

		return $this->getDefinition($this->fieldDefinitions[$offset]);
	}

	public function offsetSet($offset, $value)
	{
		throw new \BadMethodCallException("Cannot set offsets in definition set.");
	}

	public function offsetExists($offset)
	{
		return isset($this->fieldDefinitions[$offset]);
	}

	public function offsetUnset($offset)
	{
		throw new \BadMethodCallException("Cannot un-set offsets in definition set.");
	}

	public function getIterator()
	{
		return new \ArrayIterator($this->getFieldDefinitions());
	}

	public function count()
	{
		return count($this->fieldDefinitions);
	}

	/**
	 * @param array $definition
	 *
	 * @return Definition
	 */
	protected function getDefinition(array $definition)
	{
		$class = \XF::extendClass('XF\CustomField\Definition');
		return new $class($definition);
	}
}