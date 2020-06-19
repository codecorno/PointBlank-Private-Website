<?php

namespace XF\Mvc\Entity;

class Finder implements \IteratorAggregate
{
	const ORDER_RANDOM = 'RAND()';

	/**
	 * @var Manager
	 */
	protected $em;

	/**
	 * @var \XF\Db\AbstractAdapter
	 */
	protected $db;

	/**
	 * @var Structure
	 */
	protected $structure;

	protected $conditions = [];
	protected $order = [];
	protected $defaultOrder = [];
	protected $joins = [];
	protected $aliasCounter = 1;

	protected $indexHints = [];

	/**
	 * If this is an array, conditions will be built into this variable as we are doing some sort of nested
	 * condition building. In normal operation, it will be null (which means don't use).
	 *
	 * @var null|array
	 */
	protected $conditionBuilding = null;

	/**
	 * @var \Closure|null
	 */
	protected $keyedBy;

	/**
	 * @var \Closure|null
	 */
	protected $pluckFrom;

	protected $limit = null;
	protected $offset = 0;

	protected $fetchProxied = false;

	/**
	 * @var Finder
	 */
	protected $parentFinder;
	protected $relationPath;

	protected $childFinders = [];

	public function __construct(Manager $em, Structure $structure)
	{
		$this->em = $em;
		$this->db = $em->getDb();
		$this->structure = $structure;
	}

	public function setParentFinder(Finder $parent, $relationPath)
	{
		if ($this->conditions || $this->order || $this->joins)
		{
			throw new \LogicException("Cannot setup a parent finder when criteria has been set");
		}

		$this->parentFinder = $parent;
		$this->relationPath = $relationPath;

		return $this;
	}

	public function getParentFinder()
	{
		return $this->parentFinder;
	}

	public function __get($relationName)
	{
		if (isset($this->childFinders[$relationName]))
		{
			return $this->childFinders[$relationName];
		}

		if (!isset($this->structure->relations[$relationName]))
		{
			$table = $this->structure->table;
			throw new \LogicException("Unknown relation $relationName accessed on $table");
		}

		$childFinder = $this->em->getFinder($this->structure->relations[$relationName]['entity'], false);
		$childFinder->setParentFinder($this, $relationName);

		$this->childFinders[$relationName] = $childFinder;

		return $childFinder;
	}

	protected function writeSqlCondition($condition)
	{
		if ($this->parentFinder)
		{
			$this->parentFinder->writeSqlCondition($condition);
		}
		else if (is_array($this->conditionBuilding))
		{
			$this->conditionBuilding[] = $condition;
		}
		else
		{
			$this->conditions[] = $condition;
		}
	}

	public function where($condition, $operator = null, $value = null)
	{
		$argCount = func_num_args();
		switch ($argCount)
		{
			case 1: $condition = $this->buildCondition($condition); break;
			case 2: $condition = $this->buildCondition($condition, $operator); break;
			case 3: $condition = $this->buildCondition($condition, $operator, $value); break;

			default: $condition = call_user_func_array([$this, 'buildCondition'], func_get_args());
		}

		$this->writeSqlCondition($condition);

		return $this;
	}

	public function whereImpossible()
	{
		$this->writeSqlCondition('1 = 0');

		return $this;
	}

	public function whereOr(array $conditionA, array $conditionB = null)
	{
		$args = $conditionB === null ? $conditionA : func_get_args();
		$conditions = [];

		if (!$args)
		{
			throw new \InvalidArgumentException("Where OR called with no conditions");
		}

		foreach ($args AS $k => $arg)
		{
			if ($arg instanceof FinderExpression)
			{
				$conditions[] = $arg->renderSql($this, true);
			}
			else if (is_array($arg) && $this->arrayRepresentsCondition($arg))
			{
				$conditions[] = $this->buildConditionFromArray($arg);
			}
			else if (is_array($arg))
			{
				$conditions[] = $this->buildCondition($arg);
			}
			else
			{
				throw new \InvalidArgumentException("Argument $k is not an array/FinderExpression");
			}
		}

		$this->writeSqlCondition("(" . implode(") OR (", $conditions) . ")");

		return $this;
	}

	public function buildCondition($condition, $operator = null, $value = null)
	{
		$argCount = func_num_args();

		if ($argCount == 1)
		{
			if ($condition instanceof FinderExpression)
			{
				return $condition->renderSql($this, true);
			}

			if ($condition instanceof \Closure)
			{
				$conditionBuilding = $this->conditionBuilding;
				$this->conditionBuilding = [];

				$result = $condition($this);
				if (!$result)
				{
					$result = $this->conditionBuilding;
				}

				$this->conditionBuilding = $conditionBuilding;

				if ($result)
				{
					return is_array($result) ? implode(' AND ', $result) : $result;
				}
				else
				{
					return '1';
				}
			}

			if ($condition === true)
			{
				return '1';
			}

			if ($condition === false)
			{
				return '0';
			}

			if (!is_array($condition))
			{
				throw new \InvalidArgumentException('Condition must be array if only 1 argument is provided');
			}

			$conditions = [];

			if ($this->arrayRepresentsCondition($condition))
			{
				$conditions[] = $this->buildConditionFromArray($condition);
			}
			else
			{
				foreach ($condition AS $name => $value)
				{
					if (is_int($name) && $value instanceof FinderExpression)
					{
						$conditions[] = $value->renderSql($this, true);
					}
					else if (is_int($name) && is_array($value))
					{
						$conditions[] = $this->buildConditionFromArray($value);
					}
					else
					{
						$conditions[] = $this->buildCondition($name, $value);
					}
				}
			}

			return $conditions ? implode(' AND ', $conditions) : "1";
		}

		if ($condition instanceof FinderExpression)
		{
			$lhs = $condition->renderSql($this, true);
		}
		else
		{
			$lhs = $this->columnSqlName($condition, true);
		}

		if ($argCount == 2)
		{
			// 2 args is implicit equals
			$value = $operator;
			$operator = '=';
		}

		$operator = strtoupper($operator);

		switch ($operator)
		{
			case '=':
			case '<>':
			case '!=':
			case '>':
			case '>=':
			case '<':
			case '<=':
			case 'LIKE':
			case 'NOT LIKE':
			case 'BETWEEN':
				break;

			default:
				throw new \InvalidArgumentException("Operator $operator is not valid");
		}

		$hasValue = true;

		if ($value === null)
		{
			switch ($operator)
			{
				case '=':
					$operator = 'IS NULL';
					$hasValue = false;
					break;

				case '<>':
				case '!=':
					$operator = 'IS NOT NULL';
					$hasValue = false;
					break;
			}
		}

		if (!$hasValue)
		{
			return "$lhs $operator";
		}

		$quoted = $this->db->quote($value);

		if (!is_array($value))
		{
			switch ($operator)
			{
				case 'BETWEEN':
					throw new \InvalidArgumentException("Between operators require array values");
					break;
			}

			return "$lhs $operator $quoted";
		}

		switch ($operator)
		{
			case '=':
				if (strlen($quoted))
				{
					$condition = "$lhs IN (" . $quoted . ')';
				}
				else
				{
					$condition = '0'; // can't match
				}
				break;

			case '<>':
			case '!=':
				if (strlen($quoted))
				{
					$condition = "$lhs NOT IN (" . $quoted . ')';
				}
				else
				{
					// otherwise ignore
					$condition = '1';
				}
				break;

			case 'LIKE':
			case 'NOT LIKE':
				$parts = [];
				foreach ($value AS $v)
				{
					if (strlen($v))
					{
						$parts[] = "$lhs $operator " . $this->db->quote($v);
					}
				}
				if ($parts)
				{
					// if you say NOT LIKE [a, b, c], you can't match any, so need AND
					$condition = implode($operator == 'LIKE' ? ' OR ' : ' AND ', $parts);
				}
				else
				{
					// otherwise ignore
					$condition = '1';
				}
				break;

			case 'BETWEEN';
				$min = $value[0];
				$max = $value[1];
				$condition = "$lhs BETWEEN " . $this->db->quote($min) . ' AND ' . $this->db->quote($max);
				break;

			default:
				throw new \InvalidArgumentException("Operator $operator is not valid with an array of values");
		}

		return $condition;
	}

	protected function buildConditionFromArray(array $value)
	{
		switch (count($value))
		{
			case 1: return $this->buildCondition($value[0]);
			case 2: return $this->buildCondition($value[0], $value[1]);
			case 3: return $this->buildCondition($value[0], $value[1], $value[2]);
			default: return call_user_func_array([$this, 'buildCondition'], $value);
		}
	}

	protected function arrayRepresentsCondition(array $array)
	{
		if (!isset($array[0]))
		{
			return false;
		}

		foreach ($array AS $k => $null)
		{
			if (!is_int($k))
			{
				return false;
			}
		}

		if (is_array($array[0]))
		{
			return false;
		}

		return true;
	}

	public function whereId($id)
	{
		$primaryKey = $this->structure->primaryKey;

		if (is_array($primaryKey) && count($primaryKey) === 1)
		{
			$primaryKey = reset($primaryKey);
		}

		if (is_array($primaryKey))
		{
			if (!is_array($id))
			{
				throw new \InvalidArgumentException("Primary key is compound but non array ID given");
			}
			foreach ($primaryKey AS $i => $key)
			{
				if (array_key_exists($key, $id))
				{
					$this->where($key, $id[$key]);
				}
				else if (array_key_exists($i, $id))
				{
					$this->where($key, $id[$i]);
				}
				else
				{
					throw new \InvalidArgumentException("Expected array key $key or $i to exist in ID");
				}
			}
		}
		else
		{
			$this->where($primaryKey, $id);
		}

		return $this;
	}

	public function whereIds(array $ids)
	{
		$primaryKey = $this->structure->primaryKey;

		if (is_array($primaryKey) && count($primaryKey) === 1)
		{
			$primaryKey = reset($primaryKey);
		}

		if (is_array($primaryKey))
		{
			$columns = [];
			foreach ($primaryKey AS $i => $key)
			{
				$columns[] = $this->columnSqlName($key, true);
			}

			$values = [];
			foreach ($ids AS $id)
			{
				$row = [];
				foreach ($primaryKey AS $i => $key)
				{
					if (array_key_exists($key, $id))
					{
						$row[] = $this->quote($id[$key]);
					}
					else if (array_key_exists($i, $id))
					{
						$row[] = $this->quote($id[$i]);
					}
					else
					{
						throw new \InvalidArgumentException("Expected array key $key or $i to exist in ID");
					}
				}

				$values[] = '(' . implode(', ', $row) . ')';
			}

			$this->whereSql('(' . implode(', ', $columns) . ') IN (' . implode(', ', $values) . ')');
		}
		else
		{
			$this->where($primaryKey, $ids);
		}

		return $this;
	}

	public function whereSql($sql)
	{
		$args = func_get_args();
		if (count($args) > 1)
		{
			array_shift($args);
			$args = array_map([$this->db, 'quote'], $args);
			$sql = vsprintf($sql, $args);
		}

		$this->writeSqlCondition($sql);

		return $this;
	}

	public function whereAddOnActive(array $options = [])
	{
		$options = array_replace([
			'column' => 'addon_id',
			'relation' => 'AddOn',
			'disableProcessing' => false
		], $options);

		$relation = $options['relation'];
		$column = $options['column'];

		if ($options['disableProcessing'])
		{
			$activeLimit = [
				["{$relation}.active", 1],
				["{$relation}.is_processing", 0]
			];
		}
		else
		{
			$activeLimit = ["{$relation}.active", 1];
		}

		$this->whereOr(
			$activeLimit,
			[$column, '']
		);

		return $this;
	}

	public function whereIf($condition, $true, $false)
	{
		$conditionSql = $this->buildCondition($condition);
		$trueSql = $this->buildCondition($true);
		$falseSql = $this->buildCondition($false);

		$this->writeSqlCondition("IF($conditionSql, $trueSql, $falseSql)");

		return $this;
	}

	public function getConditions()
	{
		return $this->conditions;
	}

	public function resetWhere()
	{
		if ($this->parentFinder)
		{
			throw new \LogicException("Cannot reset the where clause with a parent finder");
		}

		$this->conditions = [];

		return $this;
	}

	public function columnSqlName($column, $markFundamental = true)
	{
		list($table, $field) = $this->resolveFieldToTableAndColumn($column, $markFundamental);
		return "`$table`.`$field`";
	}

	public function expression($sqlExpression)
	{
		$args = func_get_args();
		array_shift($args);

		// passed the references in as an array, otherwise as separate args
		if (count($args) == 1 && is_array($args[0]))
		{
			$args = $args[0];
		}

		return new FinderExpression($sqlExpression, $args);
	}

	public function escapeExpression($expression)
	{
		return str_replace('%', '%%', $expression);
	}

	public function columnUtf8($column)
	{
		return $this->expression("CONVERT (%s USING {$this->db->getUtf8Type()})", $column);
	}

	public function caseInsensitive($column)
	{
		return $this->columnUtf8($column);
	}

	public function quote($value, $type = null)
	{
		return $this->db->quote($value, $type);
	}

	public function escapeLike($value, $format = null)
	{
		return $this->db->escapeLike($value, $format);
	}

	public function with($name, $mustExist = false)
	{
		if (is_array($name))
		{
			foreach ($name AS $join)
			{
				$this->join($join, true, false, $mustExist);
			}
		}
		else
		{
			$this->join($name, true, false, $mustExist);
		}

		return $this;
	}

	public function exists($name, $fetch = false)
	{
		if (is_array($name))
		{
			foreach ($name AS $join)
			{
				$this->join($join, $fetch, true, true);
			}
		}
		else
		{
			$this->join($name, $fetch, true, true);
		}

		return $this;
	}

	protected function join($name, $fetch = false, $fundamental = false, $mustExist = false)
	{
		if ($this->parentFinder)
		{
			return $this->parentFinder->join("$this->relationPath.$name", $fetch, $fundamental, $mustExist);
		}

		if ($mustExist)
		{
			$fundamental = true;
		}

		$parts = explode('.', $name);
		$partialName = '';
		$structure = $this->structure;
		$joinTable = $structure->table;
		$finalJoin = null;
		$autoWith = [];
		$isWithAlias = false;

		foreach ($parts AS $part)
		{
			if ($isWithAlias)
			{
				throw new \LogicException("A withAlias must be the last relation requested");
			}

			$hasRelationValue = explode('|', $part, 2);
			if (isset($hasRelationValue[1]))
			{
				$relationValue = $hasRelationValue[1];
				$relationName = $hasRelationValue[0];
			}
			else
			{
				$relationName = $part;
				$relationValue = null;
			}

			if (empty($structure->relations[$relationName]))
			{
				if (isset($structure->withAliases[$relationName]))
				{
					$isWithAlias = true;
					$withAliasPrefix = ($partialName ? $partialName . '.' : '');

					if ($relationValue)
					{
						$withAliasParams = explode('+', $relationValue);
						$withAliasParams = array_fill_keys($withAliasParams, true);
					}
					else
					{
						$withAliasParams = [];
					}

					foreach ($structure->withAliases[$relationName] AS $withAlias)
					{
						if ($withAlias instanceof \Closure)
						{
							$withAlias = $withAlias($withAliasParams, $this, $relationValue);
						}
						if (!$withAlias)
						{
							// closures may not return anything
							continue;
						}
						if (!is_array($withAlias))
						{
							$withAlias = [$withAlias];
						}

						foreach ($withAlias AS $w)
						{
							$this->join($withAliasPrefix . $w, $fetch, $fundamental, $mustExist);
						}
					}

					continue;
				}

				throw new \LogicException("Unknown relation or alias $relationName accessed on {$structure->table}");
			}

			$parentJoin = $partialName;
			$partialName = ($partialName ? $partialName . '.' : '') . $part;
			$relation = $structure->relations[$relationName];
			$relationStructure = $this->em->getEntityStructure($relation['entity']);

			if ($relationValue !== null)
			{
				if (empty($relation['key']))
				{
					throw new \LogicException("Attempting to get a specific value of a relation that doesn't support it");
				}

				// will only be getting one row
				$relation['type'] = Entity::TO_ONE;
			}

			if ($relation['type'] !== Entity::TO_ONE)
			{
				throw new \Exception("Joins only support TO_ONE relationships currently");
				// TODO: joins only work on TO_ONE relationships - need to run separate queries for TO_MANY
			}

			if (isset($this->joins[$partialName]))
			{
				$finalJoin = $this->joins[$partialName];
				$joinTable = $finalJoin['alias'];
				$structure = $relationStructure;
				if ($fetch)
				{
					if (!empty($relation['with']) && !$this->joins[$partialName]['fetch'])
					{
						foreach ((array)$relation['with'] AS $with)
						{
							$autoWith["$partialName.$with"] = true;
						}
					}

					$this->joins[$partialName]['fetch'] = true;
				}
				if ($fundamental)
				{
					$this->joins[$partialName]['fundamental'] = true;
				}
				if ($mustExist)
				{
					$this->joins[$partialName]['exists'] = true;
				}
				continue;
			}

			$alias = $relationStructure->table . '_' . $relationName . '_' . $this->aliasCounter++;

			$joinConditions = [];
			$conditions = $relation['conditions'];
			if (!is_array($conditions))
			{
				$conditions = [$conditions];
			}
			foreach ($conditions AS $condition)
			{
				if (is_string($condition))
				{
					$joinConditions[] = "`$alias`.`$condition` = `$joinTable`.`$condition`";
				}
				else
				{
					list($field, $operator, $value) = $condition;

					if (count($condition) > 3)
					{
						$readValue = [];
						foreach (array_slice($condition, 2) AS $v)
						{
							if ($v && $v[0] == '$')
							{
								$readValue[] = "`$joinTable`.`" . substr($v, 1) . '`';
							}
							else
							{
								$readValue[] = $this->db->quote($v);
							}
						}

						$value = 'CONCAT(' . implode(', ', $readValue) . ')';
					}
					else if ($value instanceof \Closure)
					{
						$value = $value('join', $joinTable);
					}
					else if (is_string($value) && $value && $value[0] == '$')
					{
						$value = "`$joinTable`.`" . substr($value, 1) . '`';
					}
					else if (is_array($value))
					{
						if (!$value)
						{
							throw new \LogicException("Array join conditions require a value");
						}

						switch ($operator)
						{
							case '=':
								$operator = 'IN';
								break;

							case '<>':
							case '!=':
								$operator = 'NOT IN';
								break;

							default:
								throw new \LogicException("Array join conditions only support equals and not equals");
						}

						$value = '(' . $this->db->quote($value) . ')';
					}
					else
					{
						$value = $this->db->quote($value);
					}

					if ($field[0] == '$')
					{
						$fromJoinAlias = "`$joinTable`.`" . substr($field, 1) . '`';
					}
					else
					{
						$fromJoinAlias = "`$alias`.`$field`";
					}

					$joinConditions[] = "$fromJoinAlias $operator $value";
				}
			}

			if ($relationValue !== null)
			{
				$relation['key'] = $this->getColumnAlias($relationStructure, $relation['key']);
				$joinConditions[] = "`$alias`.`$relation[key]` = " . $this->db->quote($relationValue);
			}

			$this->joins[$partialName] = [
				'table' => $relationStructure->table,
				'structure' => $relationStructure,
				'alias' => $alias,
				'parentAlias' => $joinTable,
				'condition' => implode(' AND ', $joinConditions),
				'fetch' => $fetch,
				'fundamental' => $fundamental,
				'exists' => $mustExist,
				'proxy' => !empty($relation['proxy']),

				'parentRelation' => $parentJoin,
				'relation' => $relationName,
				'relationValue' => $relationValue,
				'entity' => $relation['entity'],
			];

			if (!empty($relation['with']) && $fetch)
			{
				foreach ((array)$relation['with'] AS $with)
				{
					$autoWith["$partialName.$with"] = true;
				}
			}

			$joinTable = $alias;
			$structure = $relationStructure;
			$finalJoin = $this->joins[$partialName];
		}

		foreach (array_keys($autoWith) AS $extraWith)
		{
			$this->join($extraWith, true);
		}

		return $finalJoin;
	}

	protected function writeSqlOrder($order)
	{
		if ($this->parentFinder)
		{
			$this->parentFinder->writeSqlOrder($order);
		}
		else
		{
			$this->order[] = $order;
		}
	}

	/**
	 * @param $field
	 * @param string $direction
	 *
	 * @return Finder
	 */
	public function order($field, $direction = 'ASC')
	{
		$direction = $direction ? strtoupper($direction) : 'ASC';

		switch ($direction)
		{
			case self::ORDER_RANDOM:
			case 'ASC':
			case 'DESC':
				break;

			default:
				throw new \InvalidArgumentException("Unknown order by direction $direction");
		}

		if (is_array($field))
		{
			if (count($field) == 2 && isset($field[1]) && is_string($field[1]))
			{
				switch (strtoupper($field[1]))
				{
					case 'ASC':
					case 'DESC':
						// this is ['column', 'ASC'] format
						return $this->order($field[0], $field[1]);
				}
			}

			foreach ($field AS $entry)
			{
				if (is_array($entry))
				{
					$this->order($entry[0], isset($entry[1]) ? $entry[1] : $direction);
				}
				else
				{
					$this->order($entry);
				}
			}
		}
		else
		{
			if ($field == self::ORDER_RANDOM)
			{
				$this->writeSqlOrder(self::ORDER_RANDOM);
			}
			else
			{
				if ($field instanceof FinderExpression)
				{
					$order = $field->renderSql($this, true);
				}
				else
				{
					$order = $this->columnSqlName($field, true);
				}

				$this->writeSqlOrder("$order $direction");
			}
		}

		return $this;
	}

	public function resetOrder()
	{
		if ($this->parentFinder)
		{
			throw new \LogicException("Cannot reset the order clause with a parent finder");
		}

		$this->order = [];

		return $this;
	}

	public function setDefaultOrder($field, $direction = 'ASC')
	{
		if (is_array($field))
		{
			if (count($field) == 2 && isset($field[1]) && is_string($field[1]))
			{
				switch (strtoupper($field[1]))
				{
					case 'ASC':
					case 'DESC':
						// this is array('column', 'ASC') format
						$this->defaultOrder = [[$field[0], $field[1]]];
						return $this;
				}
			}

			$this->defaultOrder = [];

			foreach ($field AS $entry)
			{
				if (is_array($entry))
				{
					$direction = strtoupper(isset($entry[1]) ? $entry[1] : 'ASC');
					if (!$direction)
					{
						$direction = 'ASC';
					}

					switch ($direction)
					{
						case 'ASC':
						case 'DESC':
							break;

						default:
							throw new \InvalidArgumentException("Unknown order by direction $direction");
					}

					$this->defaultOrder[] = [$entry[0], $direction];
				}
				else
				{
					$this->defaultOrder[] = [$entry, 'ASC'];
				}
			}
		}
		else
		{
			$direction = strtoupper($direction);
			if (!$direction)
			{
				$direction = 'ASC';
			}

			switch ($direction)
			{
				case 'ASC':
				case 'DESC':
					break;

				default:
					throw new \InvalidArgumentException("Unknown order by direction $direction");
			}

			$this->defaultOrder = [[$field, $direction]];
		}

		return $this;
	}

	public function indexHint($hintType, $indexName)
	{
		$hintType = strtoupper($hintType);

		switch ($hintType)
		{
			case 'IGNORE':
			case 'USE':
			case 'FORCE':
				break;

			default:
				throw new \InvalidArgumentException("Index hint must be IGNORE, USE, OR FORCE");
		}

		$indexName = strtr($indexName, '`\\', '');
		$this->indexHints[] = "{$hintType} INDEX (`$indexName`)";

		return $this;
	}

	/**
	 * @param $page
	 * @param $perPage
	 * @param int $thisPageExtra
	 *
	 * @return Finder
	 */
	public function limitByPage($page, $perPage, $thisPageExtra = 0)
	{
		if ($this->parentFinder)
		{
			throw new \LogicException("Cannot apply a limit with a parent finder");
		}

		$page = intval($page);
		if ($page < 1)
		{
			$page = 1;
		}

		$perPage = intval($perPage);
		if ($perPage < 1)
		{
			$perPage = 1;
		}

		$thisPageExtra = intval($thisPageExtra);
		if ($thisPageExtra < 0)
		{
			$thisPageExtra = 0;
		}

		$this->offset = ($page - 1) * $perPage;
		$this->limit = $perPage + $thisPageExtra;

		return $this;
	}

	public function limit($limit, $offset = null)
	{
		if ($this->parentFinder)
		{
			throw new \LogicException("Cannot apply a limit with a parent finder");
		}

		$this->limit = $limit === null ? null : intval($limit);
		if ($offset !== null)
		{
			$this->offset = intval($offset);
		}

		return $this;
	}

	public function offset($offset)
	{
		if ($this->parentFinder)
		{
			throw new \LogicException("Cannot apply a limit with a parent finder");
		}

		$this->offset = intval($offset);

		return $this;
	}

	public function keyedBy($keyedBy)
	{
		if ($this->parentFinder)
		{
			throw new \LogicException("Cannot apply a key function with a parent finder");
		}

		if ($keyedBy && !($keyedBy instanceof \Closure))
		{
			$keyedBy = function ($e) use ($keyedBy) { return $e->{$keyedBy}; };
		}
		$this->keyedBy = $keyedBy;

		return $this;
	}

	/**
	 * @param $pluckFrom
	 * @param null $keyedBy
	 *
	 * @return Finder
	 */
	public function pluckFrom($pluckFrom, $keyedBy = null)
	{
		if ($this->parentFinder)
		{
			throw new \LogicException("Cannot apply a pluck function with a parent finder");
		}

		if ($pluckFrom && !($pluckFrom instanceof \Closure))
		{
			$pluckFrom = function ($e) use ($pluckFrom) { return $e->{$pluckFrom}; };
		}
		$this->pluckFrom = $pluckFrom;

		if ($keyedBy !== null)
		{
			$this->keyedBy($keyedBy);
		}

		return $this;
	}

	public function fetchProxied($value = true)
	{
		if ($this->parentFinder)
		{
			throw new \LogicException("Cannot apply a proxy fetching with a parent finder");
		}

		$this->fetchProxied = (bool)$value;
	}

	/**
	 * @return int
	 */
	public function total()
	{
		if ($this->parentFinder)
		{
			throw new \LogicException("Cannot execute with a parent finder");
		}

		return $this->db->fetchOne($this->getQuery(['countOnly' => true]));
	}

	/**
	 * @param int|null $offset
	 *
	 * @return null|Entity
	 */
	public function fetchOne($offset = null)
	{
		$row = $this->db->query($this->getQuery([
			'limit' => 1,
			'offset' => $offset
		]))->fetchAliasGrouped();
		if (!$row)
		{
			return null;
		}

		$entity = $this->em->hydrateFromGrouped($row, $this->getHydrationMap());

		$pluckFrom = $this->pluckFrom;
		if ($entity && $pluckFrom)
		{
			$entity = $pluckFrom($entity);
		}

		return $entity;
	}

	/**
	 * @param int|null $limit
	 * @param int|null $offset
	 *
	 * @return ArrayCollection[Entity]
	 */
	public function fetch($limit = null, $offset = null)
	{
		$output = [];
		$map = $this->getHydrationMap();
		$keyedBy = $this->keyedBy;
		$pluckFrom = $this->pluckFrom;

		$results = $this->db->query($this->getQuery([
			'limit' => $limit,
			'offset' => $offset
		]));
		while ($row = $results->fetchAliasGrouped())
		{
			$entity = $this->em->hydrateFromGrouped($row, $map);
			$id = $keyedBy ? $keyedBy($entity) : $entity->getIdentifier();
			if ($pluckFrom)
			{
				$entity = $pluckFrom($entity);
			}

			if ($id !== null)
			{
				$output[$id] = $entity;
			}
			else
			{
				$output[] = $entity;
			}
		}

		return $this->em->getBasicCollection($output);
	}

	public function fetchRawEntities($limit = null, $offset = null)
	{
		$output = [];
		$map = $this->getHydrationMap();

		$results = $this->db->query($this->getQuery([
			'limit' => $limit,
			'offset' => $offset
		]));
		while ($row = $results->fetchAliasGrouped())
		{
			$entity = $this->em->hydrateFromGrouped($row, $map);
			$output[] = $entity;
		}

		return $output;
	}

	public function fetchRaw(array $options = [])
	{
		$results = $this->db->query($this->getQuery($options));
		return $results->fetchAll();
	}

	public function fetchColumns($column)
	{
		if (is_array($column) && func_num_args() == 1)
		{
			$columns = $column;
		}
		else
		{
			$columns = func_get_args();
		}

		return $this->fetchRaw(['fetchOnly' => $columns]);
	}

	public function getQuery(array $options = [])
	{
		if ($this->parentFinder)
		{
			throw new \LogicException("Cannot get the query with a parent finder");
		}

		$options = array_merge([
			'limit' => null,
			'offset' => null,
			'countOnly' => false,
			'fetchOnly' => null
		], $options);

		$countOnly = $options['countOnly'];
		$fetchOnly = $options['fetchOnly'];

		$defaultOrderSql = [];
		if (!$this->order && $this->defaultOrder)
		{
			foreach ($this->defaultOrder AS $defaultOrder)
			{
				$defaultOrderCol = $defaultOrder[0];

				if ($defaultOrderCol instanceof FinderExpression)
				{
					$defaultOrderCol = $defaultOrderCol->renderSql($this, true);
				}
				else
				{
					$defaultOrderCol = $this->columnSqlName($defaultOrderCol, true);
				}

				$defaultOrderSql[] = "$defaultOrderCol $defaultOrder[1]";
			}
		}

		$fetch = [];
		$coreTable = $this->structure->table;
		$joins = [];

		if (is_array($fetchOnly))
		{
			if (!$fetchOnly)
			{
				throw new \InvalidArgumentException("Must specify one or more specific columns to fetch");
			}

			foreach ($fetchOnly AS $key => $fetchValue)
			{
				$fetchSql = $this->columnSqlName(is_int($key) ? $fetchValue : $key);
				$fetch[] = $fetchSql . (!is_int($key) ? " AS '$fetchValue'" : '');
			}
		}
		else
		{
			$fetch[] = '`' . $coreTable . '`.*';
		}

		if ($this->indexHints)
		{
			$indexHints = ' ' . implode(' ', $this->indexHints);
		}
		else
		{
			$indexHints = '';
		}

		foreach ($this->joins AS $join)
		{
			if ($countOnly && !$join['fundamental'])
			{
				continue;
			}

			$joinType = $join['exists'] ? 'INNER' : 'LEFT';

			$joins[] = "$joinType JOIN `$join[table]` AS `$join[alias]` ON ($join[condition])";
			if ($join['fetch'] && !is_array($fetchOnly))
			{
				$fetch[] = "`$join[alias]`.*";
			}
		}

		if ($this->conditions)
		{
			$where = 'WHERE (' . implode(') AND (', $this->conditions) . ')';
		}
		else
		{
			$where = '';
		}

		if ($countOnly)
		{
			return "
				SELECT COUNT(*)
				FROM `$coreTable`$indexHints
				" . implode("\n", $joins) . "
				$where
			";
		}

		if ($this->order)
		{
			$orderBy = 'ORDER BY ' . implode(', ', $this->order);
		}
		else if ($defaultOrderSql)
		{
			$orderBy = 'ORDER BY ' . implode(', ', $defaultOrderSql);
		}
		else
		{
			$orderBy = '';
		}

		$limit = $options['limit'];
		if ($limit === null)
		{
			$limit = $this->limit;
		}

		$offset = $options['offset'];
		if ($offset === null)
		{
			$offset = $this->offset;
		}

		$q = $this->db->limit("
			SELECT " . implode(', ', $fetch) . "
			FROM `$coreTable`$indexHints
			" . implode("\n", $joins) . "
			$where
			$orderBy
		", $limit, $offset);

		return $q;
	}

	/**
	 * @return ArrayCollection
	 */
	public function getIterator()
	{
		return $this->fetch();
	}

	public function getHydrationMap()
	{
		if ($this->parentFinder)
		{
			throw new \LogicException("Cannot get the hydration map with a parent finder");
		}

		$map = [];
		foreach ($this->joins AS $name => $join)
		{
			if (empty($join['fetch']))
			{
				continue;
			}

			$map[$name] = [
				'alias' => $join['alias'],
				'entity' => $join['entity'],
				'proxy' => $join['proxy'],
				'parentRelation' => $join['parentRelation'],
				'relation' => $join['relation'],
				'relationValue' => $join['relationValue']
			];
		}

		$map = array_reverse($map, true); // need to process more specific joins first
		$map[''] = [
			'alias' => $this->structure->table,
			'entity' => $this->structure->shortName,
			'proxy' => $this->fetchProxied,
			'parentRelation' => '',
			'relation' => '',
			'relationValue' => null
		];

		return $map;
	}

	public function isColumnValid($field)
	{
		try
		{
			$this->resolveFieldToTableAndColumn($field, false);
			return true;
		}
		catch (\InvalidArgumentException $e)
		{
			return false;
		}
	}

	public function resolveFieldToTableAndColumn($field, $markJoinFundamental = true)
	{
		if ($this->parentFinder)
		{
			return $this->parentFinder->resolveFieldToTableAndColumn("$this->relationPath.$field", $markJoinFundamental);
		}

		$parts = explode('.', $field);

		if (count($parts) == 1)
		{
			$field = $this->getColumnAlias($this->structure, $field);

			if (!isset($this->structure->columns[$field]))
			{
				throw new \InvalidArgumentException("Unknown column $field on {$this->structure->shortName}");
			}

			return [$this->structure->table, $field];
		}

		$column = array_pop($parts);
		$joinInfo = $this->join(implode('.', $parts), false, $markJoinFundamental);

		$joinStructure = $joinInfo['structure'];
		$column = $this->getColumnAlias($joinStructure, $column);
		if (!isset($joinStructure->columns[$column]))
		{
			throw new \InvalidArgumentException("Unknown column $column on relation $joinInfo[relation] ({$joinStructure->shortName})");
		}

		return [$joinInfo['alias'], $column];
	}

	protected function getColumnAlias(Structure $structure, $column)
	{
		if ($structure->columnAliases && isset($structure->columnAliases[$column]))
		{
			$column = $structure->columnAliases[$column];
		}

		return $column;
	}

	/**
	 * @return Structure
	 */
	public function getStructure()
	{
		return $this->structure;
	}

	/**
	 * @return \XF\App
	 */
	public function app()
	{
		return \XF::app();
	}

	public function __sleep()
	{
		throw new \LogicException('Instances of ' . __CLASS__ . ' cannot be serialized or unserialized');
	}

	public function __wakeup()
	{
		throw new \LogicException('Instances of ' . __CLASS__ . ' cannot be serialized or unserialized');
	}
}