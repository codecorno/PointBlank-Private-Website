<?php

namespace XF\Api\Result;

use XF\Mvc\Entity\AbstractCollection;
use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Structure;

class EntityResult implements EntityResultInterface
{
	/**
	 * @var Entity
	 */
	protected $entity;

	protected $skipColumns = [];

	protected $includeColumns = [];

	protected $includeGetters = [];

	protected $skipRelations = [];

	protected $includeRelations = [];

	protected $extra = [];

	/**
	 * @var callable[]
	 */
	protected $callbacks = [];

	public function __construct(Entity $entity)
	{
		$this->entity = $entity;
	}

	public function skipColumn($column)
	{
		foreach ((array)$column AS $k)
		{
			$this->skipColumns[$k] = true;
		}

		return $this;
	}

	public function includeColumn($column)
	{
		foreach ((array)$column AS $k)
		{
			$this->includeColumns[$k] = true;
		}

		return $this;
	}

	public function includeGetter($getter)
	{
		foreach ((array)$getter AS $k)
		{
			$this->includeGetters[$k] = true;
		}

		return $this;
	}

	public function skipRelation($relation)
	{
		foreach ((array)$relation AS $k)
		{
			$this->skipRelations[$k] = true;
		}
	}

	public function includeRelation($relation, $verbosity = Entity::VERBOSITY_NORMAL, array $options = [])
	{
		foreach ((array)$relation AS $k)
		{
			$this->includeRelations[$k] = [$verbosity, $options];
		}

		return $this;
	}

	public function includeExtra($k, $v = null)
	{
		if (is_array($k))
		{
			$pairs = $k;
		}
		else
		{
			$pairs = [$k => $v];
		}

		foreach ($pairs AS $k => $v)
		{
			$this->extra[$k] = $v;
		}

		return $this;
	}

	public function addCallback(callable $c)
	{
		$this->callbacks[] = $c;

		return $this;
	}

	public function __set($k, $v)
	{
		$this->extra[$k] = $v;
	}

	public function getEntity()
	{
		return $this->entity;
	}

	public function render()
	{
		$result = [];

		$entity = $this->entity;
		$structure = $entity->structure();
		foreach ($structure->columns AS $key => $column)
		{
			if ($this->isColumnIncluded($key, $column, $structure))
			{
				$result[$key] = $entity->getValue($key);
			}
		}

		foreach ($this->includeGetters AS $getter => $null)
		{
			$result[$getter] = $entity->get($getter);
		}

		foreach ($structure->relations AS $key => $relation)
		{
			if (!$this->isRelationIncluded($key, $relation, $structure))
			{
				continue;
			}

			if (isset($this->includeRelations[$key]))
			{
				$relationVerbosity = $this->includeRelations[$key][0];
				$relationOptions = $this->includeRelations[$key][1];
			}
			else
			{
				$relationVerbosity = Entity::VERBOSITY_NORMAL;
				$relationOptions = [];
			}

			$relationResult = $entity->getRelation($key);
			$result[$key] = $this->castToFinalValue($relationResult, $relationVerbosity, $relationOptions);
		}

		foreach ($this->extra AS $k => $v)
		{
			$result[$k] = $this->castToFinalValue($v);
		}

		foreach ($this->callbacks AS $c)
		{
			$pairs = $c($entity);
			if (is_array($pairs))
			{
				foreach ($pairs AS $k => $v)
				{
					$result[$k] = $this->castToFinalValue($v);
				}
			}
		}

		ksort($result, SORT_STRING | SORT_FLAG_CASE);

		// casting to an object for the rare case of [] being returned here
		return (object)$result;
	}

	protected function castToFinalValue($value, $verbosity = Entity::VERBOSITY_NORMAL, array $options = [])
	{
		if ($value instanceof Entity)
		{
			$value = $value->toApiResult($verbosity, $options);
		}
		else if ($value instanceof AbstractCollection)
		{
			$value = $value->toApiResults($verbosity, $options);
		}

		if ($value instanceof ResultInterface)
		{
			$value = $value->render();
		}

		return $value;
	}

	protected function isColumnIncluded($key, array $column, Structure $structure)
	{
		if (!empty($this->skipColumns[$key]))
		{
			// skip has highest priority
			return false;
		}

		if (!empty($this->includeColumns[$key]) || !empty($column['api']) || !empty($column['autoIncrement']))
		{
			// specifically included in this request, named as api column, or an autoInc column means included
			return true;
		}

		// otherwise, default to false
		return false;
	}

	protected function isRelationIncluded($key, array $relation, Structure $structure)
	{
		if (!empty($this->skipRelations[$key]))
		{
			return false;
		}

		if (!empty($this->includeRelations[$key]) || !empty($relation['api']))
		{
			return true;
		}

		return false;
	}
}