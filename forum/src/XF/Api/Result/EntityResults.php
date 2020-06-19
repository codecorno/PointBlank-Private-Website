<?php

namespace XF\Api\Result;

use XF\Mvc\Entity\Entity;

class EntityResults implements EntityResultInterface, \Countable
{
	/**
	 * @var EntityResult[]
	 */
	protected $entityResults = [];

	public function __construct(array $entityResults)
	{
		foreach ($entityResults AS &$value)
		{
			if ($value instanceof Entity)
			{
				$value = $value->toApiResult();
			}

			if (!($value instanceof EntityResultInterface))
			{
				throw new \LogicException("All results must be instances of the EntityResultInterface");
			}
		}

		$this->entityResults = $entityResults;
	}

	public function skipColumn($column)
	{
		foreach ($this->entityResults AS $result)
		{
			$result->skipColumn($column);
		}
	}

	public function includeColumn($column)
	{
		foreach ($this->entityResults AS $result)
		{
			$result->includeColumn($column);
		}
	}

	public function includeGetter($getter)
	{
		foreach ($this->entityResults AS $result)
		{
			$result->includeGetter($getter);
		}
	}

	public function skipRelation($relation)
	{
		foreach ($this->entityResults AS $result)
		{
			$result->skipRelation($relation);
		}
	}

	public function includeRelation($relation, $verbosity = Entity::VERBOSITY_NORMAL, array $options = [])
	{
		foreach ($this->entityResults AS $result)
		{
			$result->includeRelation($relation, $verbosity, $options);
		}
	}

	public function includeExtra($k, $v = null)
	{
		foreach ($this->entityResults AS $result)
		{
			$result->includeExtra($k, $v);
		}
	}

	public function addCallback(callable $c)
	{
		foreach ($this->entityResults AS $result)
		{
			$result->addCallback($c);
		}
	}

	public function __set($k, $v)
	{
		foreach ($this->entityResults AS $result)
		{
			$result->$k = $v;
		}
	}

	public function getEntityResults()
	{
		return $this->entityResults;
	}

	public function getEntities()
	{
		$entities = [];

		foreach ($this->entityResults AS $k => $result)
		{
			$entities[$k] = $result->getEntity();
		}

		return $entities;
	}

	public function render()
	{
		$output = [];
		foreach ($this->entityResults AS $k => $result)
		{
			$output[$k] = $result->render();
		}

		return $output;
	}

	public function count()
	{
		return count($this->entityResults);
	}
}