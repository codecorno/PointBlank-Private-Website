<?php

namespace XF\Mvc\Entity;

use XF\Db\AbstractAdapter;
use XF\Extension;

class Manager
{
	/**
	 * @var AbstractAdapter
	 */
	protected $db;

	/**
	 * @var ValueFormatter
	 */
	protected $valueFormatter;

	/**
	 * @var Extension
	 */
	protected $extension;

	protected $entityClassNameMap = [];

	protected $entities = [];
	protected $structures = [];
	protected $repositories = [];

	protected $cascadeEntitySeen = [];
	protected $cascadeEventDepth = [];

	const INSTANTIATE_ALLOW_INVALID = 0x1;
	const INSTANTIATE_PROXIED = 0x2;

	public function __construct(AbstractAdapter $db, ValueFormatter $valueFormatter, Extension $extension)
	{
		$this->db = $db;
		$this->valueFormatter = $valueFormatter;
		$this->extension = $extension;
	}

	public function getEntityClassName($shortName)
	{
		if (!isset($this->entityClassNameMap[$shortName]))
		{
			$class = \XF::stringToClass($shortName, '%s\Entity\%s');
			if (!class_exists($class))
			{
				throw new \LogicException("Entity $shortName (class: $class) could not be found");
			}
			$class = $this->extension->extendClass($class);
			$this->entityClassNameMap[$shortName] = $class;
		}

		return $this->entityClassNameMap[$shortName];
	}

	/**
	 * @param string $shortName
	 *
	 * @return Structure
	 */
	public function getEntityStructure($shortName)
	{
		$className = $this->getEntityClassName($shortName);
		if (!isset($this->structures[$className]))
		{
			$structure = $className::getStructure(new Structure());
			$structure->shortName = $shortName;

			$extension = $this->extension;

			$rootClass = $extension->resolveExtendedClassToRoot($className);
			$extension->fire('entity_structure', [$this, &$structure], $rootClass);

			$this->structures[$className] = $structure;
		}

		return $this->structures[$className];
	}

	/**
	 * @param Entity $entity
	 * @param string|Entity $isA
	 * @return bool
	 */
	public function entityIsA(Entity $entity, $isA)
	{
		if (!is_object($isA))
		{
			$isA = $this->getEntityClassName($isA);
		}

		return ($entity instanceof $isA);
	}

	/**
	 * @param string $shortName
	 *
	 * @return Entity
	 */
	public function create($shortName)
	{
		return $this->instantiateEntity($shortName);
	}

	/**
	 * @param string $shortName
	 * @param mixed $id
	 * @param array|string $with
	 *
	 * @return null|Entity
	 */
	public function find($shortName, $id, $with = null)
	{
		if ($id === null || $id === false)
		{
			return null;
		}

		$className = $this->getEntityClassName($shortName);
		$lookup = $this->getEntityCacheLookupString((array)$id);
		if (isset($this->entities[$className][$lookup]))
		{
			return $this->entities[$className][$lookup];
		}
		else
		{
			$finder = $this->getFinder($shortName);
			if ($id === 0 || $id === '0')
			{
				$structure = $finder->getStructure();
				$pKey = $structure->primaryKey;
				if (is_string($pKey) && !empty($structure->columns[$pKey]['autoIncrement']))
				{
					// if we're trying to fetch a value of 0 from an auto increment field, we know it will fail
					// as 0 will be replaced with an auto increment value
					return null;
				}
			}

			$finder->whereId($id);
			if ($with)
			{
				$finder->with($with);
			}
			return $finder->fetchOne();
		}
	}

	/**
	 * @param string $shortName
	 * @param array $where
	 * @param array|string $with
	 *
	 * @return null|Entity
	 */
	public function findOne($shortName, array $where, $with = null)
	{
		$finder = $this->getFinder($shortName);
		$finder->where($where);
		if ($with)
		{
			$finder->with($with);
		}
		return $finder->fetchOne();
	}

	/**
	 * @param string $shortName
	 * @param mixed $id
	 *
	 * @return bool|Entity
	 */
	public function findCached($shortName, $id)
	{
		$className = $this->getEntityClassName($shortName);
		$lookup = $this->getEntityCacheLookupString((array)$id);
		if (isset($this->entities[$className][$lookup]))
		{
			return $this->entities[$className][$lookup];
		}
		else
		{
			return false;
		}
	}

	/**
	 * @param string $shortName
	 * @param array $ids
	 * @param array|string $with
	 *
	 * @return ArrayCollection[Entity]
	 */
	public function findByIds($shortName, array $ids, $with = null)
	{
		if (!$ids)
		{
			return $this->getEmptyCollection();
		}

		$finder = $this->getFinder($shortName);
		$finder->whereIds($ids);
		if ($with)
		{
			$finder->with($with);
		}

		return $finder->fetch();
	}

	/**
	 * @param string $shortName
	 * @param bool $includeDefaultWith
	 *
	 * @return Finder
	 */
	public function getFinder($shortName, $includeDefaultWith = true)
	{
		$structure = $this->getEntityStructure($shortName);

		$finderClass = \XF::stringToClass($shortName, '%s\Finder\%s');
		$finderClass = $this->extension->extendClass($finderClass, '\XF\Mvc\Entity\Finder');
		if (!$finderClass || !class_exists($finderClass))
		{
			$finderClass = '\XF\Mvc\Entity\Finder';
		}

		/** @var Finder $finder */
		$finder = new $finderClass($this, $structure);
		if ($includeDefaultWith && $structure->defaultWith)
		{
			$finder->with($structure->defaultWith);
		}

		return $finder;
	}

	/**
	 * @param string $identifier
	 *
	 * @return Repository
	 */
	public function getRepository($identifier)
	{
		if (isset($this->repositories[$identifier]))
		{
			return $this->repositories[$identifier];
		}

		$repositoryClass = \XF::stringToClass($identifier, '%s\Repository\%s');
		$repositoryClass = $this->extension->extendClass($repositoryClass, '\XF\Mvc\Entity\Repository');
		if (!$repositoryClass || !class_exists($repositoryClass))
		{
			throw new \LogicException("Could not find repository '$repositoryClass' for '$identifier'");
		}

		$repository = new $repositoryClass($this, $identifier);
		$this->repositories[$identifier] = $repository;

		return $repository;
	}

	/**
	 * @param array $relation
	 * @param Entity $entity
	 * @param string $fetchType
	 *
	 * @return null|Entity|Entity[]
	 *
	 * @throws \LogicException
	 */
	public function getRelation(array $relation, Entity $entity, $fetchType = 'current')
	{
		$conditions = $relation['conditions'];
		if (!is_array($conditions))
		{
			$conditions = [$conditions];
		}

		$method = $fetchType == 'current' ? 'getValue' : 'getExistingValue';

		if ($relation['type'] == Entity::TO_ONE && !empty($relation['primary']))
		{
			$key = [];
			foreach ($conditions AS $condition)
			{
				if (is_string($condition))
				{
					$value = $entity->$method($condition);
					if ($value === null)
					{
						return null;
					}

					$key[$condition] = $value;
				}
				else
				{
					list($field, $operator, $value) = $condition;

					if ($field[0] == '$')
					{
						throw new \LogicException("Cannot do a primary key lookup when the LHS of the condition refers to a value");
					}

					if ($operator !== '=')
					{
						throw new \LogicException("Cannot do a primary key lookup with a non-equality operator");
					}

					if (count($condition) > 3)
					{
						$readValue = '';
						foreach (array_slice($condition, 2) AS $v)
						{
							if ($v && $v[0] == '$')
							{
								$v = $entity->$method(substr($v, 1));
								if ($v === null)
								{
									return null;
								}
								$readValue .= $v;
							}
							else
							{
								$readValue .= $v;
							}
						}
						$key[$field] = $readValue;
					}
					else if (is_string($value) && $value[0] == '$')
					{
						$value = $entity->$method(substr($value, 1));
						if ($value === null)
						{
							return null;
						}
						$key[$field] = $value;
					}
					else if (is_array($value))
					{
						throw new \LogicException("Cannot do a primary key lookup when the relation has an array of values");
					}
					else
					{
						$key[$field] = $value;
					}
				}
			}

			$extraWith = (!empty($relation['with'])) ? $relation['with'] : [];
			return $this->find($relation['entity'], count($key) > 1 ? $key : reset($key), $extraWith);
		}

		$finder = $this->getRelationFinder($relation, $entity, $fetchType);

		if ($relation['type'] == Entity::TO_ONE)
		{
			$result = $finder->fetchOne();
			if (!$result)
			{
				$result = null;
			}
		}
		else
		{
			if (!empty($relation['key']))
			{
				$result = new FinderCollection($finder, $relation['key']);
			}
			else
			{
				$result = $finder->fetch();
			}
		}

		return $result;
	}

	/**
	 * @param array $relation
	 * @param Entity $entity
	 * @param string $fetchType
	 *
	 * @return Finder
	 */
	public function getRelationFinder(array $relation, Entity $entity, $fetchType = 'current')
	{
		$finder = $this->getFinder($relation['entity']);

		$conditions = $relation['conditions'];
		if (!is_array($conditions))
		{
			$conditions = [$conditions];
		}

		$method = $fetchType == 'current' ? 'getValue' : 'getExistingValue';

		foreach ($conditions AS $condition)
		{
			if (is_string($condition))
			{
				$finder->where($condition, '=', $entity->$method($condition));
			}
			else
			{
				list($field, $operator, $value) = $condition;

				if (is_string($field) && $field && $field[0] == '$')
				{
					$field = $finder->expression($this->db->quote($entity->$method(substr($field, 1))));
				}

				if (count($condition) > 3)
				{
					$readValue = '';
					foreach (array_slice($condition, 2) AS $v)
					{
						if ($v && $v[0] == '$')
						{
							$readValue .= $entity->$method(substr($v, 1));
						}
						else
						{
							$readValue .= $v;
						}
					}
					$finder->where($field, $operator, $readValue);
				}
				else if ($value instanceof \Closure)
				{
					$finder->where($field, $operator, $value('value', $entity));
				}
				else if (is_string($value) && $value && $value[0] == '$')
				{
					$finder->where($field, $operator, $entity->$method(substr($value, 1)));
				}
				else
				{
					// value can be an array here
					$finder->where($field, $operator, $value);
				}
			}
		}

		if (!empty($relation['with']))
		{
			foreach ((array)$relation['with'] AS $extraWith)
			{
				$finder->with($extraWith);
			}
		}

		if (!empty($relation['order']))
		{
			$finder->setDefaultOrder($relation['order']);
		}

		if (!empty($relation['key']))
		{
			$finder->keyedBy($relation['key']);
		}

		if (!empty($relation['proxy']))
		{
			$finder->fetchProxied();
		}

		return $finder;
	}

	/**
	 * @param Entity $entity
	 * @param array $behaviors
	 *
	 * @return Behavior[]
	 * @throws \Exception
	 */
	public function getBehaviors(Entity $entity, array $behaviors)
	{
		$output = [];
		foreach ($behaviors AS $behavior => $config)
		{
			if (is_int($behavior))
			{
				$behavior = $config;
				$config = [];
			}
			if (!is_array($config))
			{
				throw new \InvalidArgumentException("Behavior $behavior must provide config as an array");
			}

			$class = \XF::stringToClass($behavior, '%s\Behavior\%s');
			if (!class_exists($class))
			{
				throw new \LogicException("Behavior $behavior (class: $class) could not be found");
			}
			$class = $this->extension->extendClass($class);

			$output[$behavior] = new $class($entity, $config);
		}

		return $output;
	}

	/**
	 * @param array $entities
	 *
	 * @return ArrayCollection
	 */
	public function getBasicCollection(array $entities)
	{
		return new ArrayCollection($entities);
	}

	/**
	 * Gets an empty collections. Useful when short circuiting a result
	 * that returns a collection.
	 *
	 * @return ArrayCollection
	 */
	public function getEmptyCollection()
	{
		return new ArrayCollection([]);
	}

	/**
	 * @param \Closure $handler
	 * @param string $assignTime get/preSave/save. When the value will be locked into the final value.
	 *
	 * @return DeferredValue
	 */
	public function getDeferredValue(\Closure $handler, $assignTime = 'preSave')
	{
		return new DeferredValue($handler, $assignTime);
	}

	/**
	 * @param array $row
	 * @param array $map
	 *
	 * @return Entity
	 */
	public function hydrateFromGrouped(array $row, array $map)
	{
		$entityRelations = [];
		$finderRelations = [];

		foreach ($map AS $name => $info)
		{
			$data = $row[$info['alias']];
			$entity = $this->instantiateEntity(
				$info['entity'],
				$data,
				isset($entityRelations[$name]) ? $entityRelations[$name] : [],
				self::INSTANTIATE_ALLOW_INVALID | ($info['proxy'] ? self::INSTANTIATE_PROXIED : 0)
			);
			if ($entity && isset($finderRelations[$name]))
			{
				foreach ($finderRelations[$name] AS $relation => $relationData)
				{
					$entity->hydrateFinderRelation($relation, $relationData);
				}
			}

			if ($info['relationValue'] !== null)
			{
				$finderRelations[$info['parentRelation']][$info['relation']][$info['relationValue']] = $entity;
			}
			else
			{
				$entityRelations[$info['parentRelation']][$info['relation']] = $entity;
			}
		}

		return $entityRelations[''][''];
	}

	public function hydrateDefaultFromRelation(Entity $parent, array $relation)
	{
		if ($relation['type'] != Entity::TO_ONE)
		{
			throw new \LogicException("Cannot hydrate from a relation that is not to one");
		}

		$entity = $this->create($relation['entity']);

		$conditions = $relation['conditions'];
		if (!is_array($conditions))
		{
			$conditions = [$conditions];
		}

		foreach ($conditions AS $condition)
		{
			if (is_string($condition))
			{
				$entity->$condition = $this->getDeferredValue(
					function() use ($parent, $condition) { return $parent->getValue($condition); },
					'save'
				);
			}
			else
			{
				list($field, $operator, $value) = $condition;

				if ($field[0] == '$')
				{
					// doesn't make sense to populate a value from the parent entity
					continue;
				}

				if ($operator !== '=')
				{
					throw new \LogicException("Cannot hydrate from a relation with a non-equality operator");
				}

				if (count($condition) > 3)
				{
					$entity->$field = $this->getDeferredValue(
						function() use ($parent, $condition)
						{
							$readValue = '';
							foreach (array_slice($condition, 2) AS $v)
							{
								if ($v && $v[0] == '$')
								{
									$readValue .= $parent->getValue(substr($v, 1));
								}
								else
								{
									$readValue .= $v;
								}
							}
							return $readValue;
						},
						'save'
					);
				}
				else if ($value instanceof \Closure)
				{
					$entity->$field = $this->getDeferredValue(
						function() use ($entity, $value) { return $value('value', $entity); },
						'save'
					);
				}
				else if (is_string($value) && $value[0] == '$')
				{
					$entity->$field = $this->getDeferredValue(
						function() use ($parent, $value) { return $parent->getValue(substr($value, 1)); },
						'save'
					);
				}
				else if (is_array($value))
				{
					// Arrays represent multiple possible values--column IN (a, b)--so we can't set a value
					// based on this. Ignore it instead.
				}
				else
				{
					$entity->$field = $value;
				}
			}
		}

		return $entity;
	}

	/**
	 * Instantiates the named entity with the specified values and relations. This is a low level function and the
	 * arguments must be structured correctly.
	 *
	 * This function should only be called with values and relations in specific cases. In most situations, this
	 * function should not be used by XenForo application-level code.
	 *
	 * @param string $shortName
	 * @param array $values Values for the columns in the entity, in source encoded form
	 * @param array $relations
	 * @param int $options Bit field of the INSTANTIATE_* options
	 *
	 * @return null|Entity
	 *
	 * @throws \LogicException
	 */
	public function instantiateEntity($shortName, array $values = [], array $relations = [], $options = 0)
	{
		$className = $this->getEntityClassName($shortName);

		if ($options & self::INSTANTIATE_PROXIED)
		{
			if (!is_subclass_of($className, 'XF\Mvc\Entity\Proxyable'))
			{
				throw new \LogicException("Entity $shortName is not proxyable");
			}

			if ($values)
			{
				$className::instantiateProxied($values);
			}

			return null;
		}

		$structure = $this->getEntityStructure($shortName);

		/** @var Entity $entity */
		$entity = new $className($this, $structure, $values, $relations);
		if ($values)
		{
			$class = get_class($entity);
			$keys = $entity->getIdentifierValues();
			if (!$keys)
			{
				// must contain nulls, so not a valid entity
				if (!($options & self::INSTANTIATE_ALLOW_INVALID))
				{
					throw new \LogicException("Cannot instantiate $shortName ($className) without primary key values");
				}

				return null;
			}

			$primary = $this->getEntityCacheLookupString($keys);
			if (isset($this->entities[$class][$primary]))
			{
				$entity = $this->entities[$class][$primary];
				// TODO: how to handle relationships, at least if the existing entity has pending changes?
			}
			else
			{
				$this->entities[$class][$primary] = $entity;
			}
		}

		return $entity;
	}

	/**
	 * @return ValueFormatter
	 */
	public function getValueFormatter()
	{
		return $this->valueFormatter;
	}

	public function encodeValueForSource($type, $value)
	{
		return $this->valueFormatter->encodeValueForSource($type, $value);
	}

	public function decodeValueFromSource($type, $value)
	{
		return $this->valueFormatter->decodeValueFromSource($type, $value);
	}

	public function decodeValueFromSourceExtended($type, $value, array $columnOptions = [])
	{
		return $this->valueFormatter->decodeValueFromSourceExtended($type, $value, $columnOptions);
	}

	public function startCascadeEvent($event, Entity $entity)
	{
		$id = $entity->getUniqueEntityId();

		if (empty($this->cascadeEventDepth[$event]))
		{
			$this->cascadeEventDepth[$event] = 1;
		}
		else
		{
			$this->cascadeEventDepth[$event]++;
		}

		$this->cascadeEntitySeen[$event][$id] = true;
	}

	public function triggerCascadeAttempt($event, Entity $entity)
	{
		if (empty($this->cascadeEventDepth[$event]))
		{
			// no cascade logging has been started, can continue
			return true;
		}

		$id = $entity->getUniqueEntityId();

		if (isset($this->cascadeEntitySeen[$event][$id]))
		{
			// already seen, don't continue
			return false;
		}

		// first time we're seeing it, note and continue
		$this->cascadeEntitySeen[$event][$id] = true;
		return true;
	}

	public function finishCascadeEvent($event)
	{
		if (!empty($this->cascadeEventDepth[$event]))
		{
			$this->cascadeEventDepth[$event]--;
			if (!$this->cascadeEventDepth[$event])
			{
				// no more cascades running, reset
				unset($this->cascadeEventDepth[$event]);
				unset($this->cascadeEntitySeen[$event]);
			}
		}
	}

	public function attachEntity(Entity $entity)
	{
		$keys = $entity->getIdentifierValues();
		if (!$keys)
		{
			throw new \LogicException("Cannot attach an entity without a valid primary key");
		}

		$primary = $this->getEntityCacheLookupString($keys);
		$class = get_class($entity);

		$this->entities[$class][$primary] = $entity;
	}

	public function detachEntity(Entity $entity)
	{
		$keys = $entity->getIdentifierValues();
		if (!$keys)
		{
			// not attached
			return;
		}

		$primary = $this->getEntityCacheLookupString($keys);
		$class = get_class($entity);

		unset($this->entities[$class][$primary]);
	}

	public function clearEntityCache($shortName = null)
	{
		if ($shortName)
		{
			$class = $this->getEntityClassName($shortName);
			unset($this->entities[$class]);
		}
		else
		{
			$this->entities = [];
		}

		gc_collect_cycles();
	}

	protected function getEntityCacheLookupString(array $values)
	{
		return implode('\x1E', $values);
	}

	public function beginTransaction()
	{
		$this->db->beginTransaction();
	}

	public function commit()
	{
		$this->db->commit();
	}

	public function rollback()
	{
		$this->db->rollback();
	}

	/**
	 * @return AbstractAdapter
	 */
	public function getDb()
	{
		return $this->db;
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