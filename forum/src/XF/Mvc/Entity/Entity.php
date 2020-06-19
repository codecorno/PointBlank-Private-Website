<?php

namespace XF\Mvc\Entity;

abstract class Entity implements \ArrayAccess
{
	const REQUIRES_DECODING = 0x10000;

	const INT                   =  0x0001;
	const UINT                  =  0x0002;
	const FLOAT                 =  0x0003;
	const BOOL                  = 0x10004;
	const STR                   =  0x0005;
	const BINARY                =  0x0006;
	const SERIALIZED            = 0x10007;
	const JSON                  = 0x10009;
	const JSON_ARRAY            = 0x10010;
	const LIST_LINES            = 0x10011;
	const LIST_COMMA            = 0x10012;

	/** @deprecated - use JSON_ARRAY instead  */
	const SERIALIZED_ARRAY      = 0x10008;

	const TO_ONE  = 1;
	const TO_MANY = 2;

	const VERBOSITY_QUIET = 0;
	const VERBOSITY_NORMAL = 1;
	const VERBOSITY_VERBOSE = 2;

	// Note that all the variables and protected methods in this class are prefixed with _ to avoid
	// any possible conflicts with entries in the concrete methods. This is not the general practice.

	private static $_entityCounter = 1;
	private $_uniqueEntityId;

	protected $rootClass;

	// if true, pass arguments to db->insert to use REPLACE INTO instead of INSERT INTO
	protected $_useReplaceInto = false;

	protected $_newValues = [];
	protected $_values = [];
	protected $_relations = [];
	protected $_getterCache = [];
	protected $_valueCache = [];
	protected $_previousValues = [];

	protected $_options = [];

	protected $_deleted = false;

	protected $_readOnly = false;
	protected $_writePending = false;
	protected $_writeRunning = false;
	protected $_errors = [];

	/**
	 * @var Entity[]
	 */
	protected $_cascadeSave = [];

	/**
	 * @var null|Behavior[]
	 */
	protected $_behaviors = null;

	/**
	 * @var Structure
	 */
	protected $_structure;

	/**
	 * @var Manager
	 */
	protected $_em;

	public function __construct(Manager $em, Structure $structure, array $values = [], array $relations = [])
	{
		$this->_uniqueEntityId = self::$_entityCounter++;
		$this->_em = $em;
		$this->_structure = $structure;
		$this->_values = $values;
		$this->_relations = $relations;
		$this->rootClass = \XF::extension()->resolveExtendedClassToRoot($this);

		if (!$values)
		{
			$this->_setupDefaults();

			\XF::fire('entity_defaults', [$this], $this->rootClass);
		}
	}

	protected function _setupDefaults()
	{
	}

	public function __get($key)
	{
		return $this->get($key);
	}

	public function offsetGet($key)
	{
		return $this->get($key);
	}

	public function get($key)
	{
		$structure = $this->_structure;
		$originalKey = $key;

		if (substr($key, -1) == '_')
		{
			$key = substr($key, 0, -1);
			$useGetter = false;
		}
		else
		{
			$useGetter = true;
		}

		if ($useGetter && isset($structure->getters[$key]))
		{
			$getterVal = $structure->getters[$key];
			if (is_array($getterVal))
			{
				$cache = isset($getterVal['cache']) ? $getterVal['cache'] : true;
				$getter = $getterVal['getter'];
			}
			else
			{
				$cache = $getterVal; // is a boolean indicating cacheability
				$getter = true; // key indicates getter name so build that below
			}

			if ($cache && array_key_exists($key, $this->_getterCache))
			{
				return $this->_getterCache[$key];
			}

			if ($getter === true)
			{
				$getter = 'get' . \XF\Util\Php::camelCase($key);
			}

			$result = $this->$getter();
			if ($cache)
			{
				$this->_getterCache[$key] = $result;
			}

			return $result;
		}

		if (!empty($structure->columns[$key]))
		{
			if ($useGetter && !empty($structure->columns[$key]['censor']))
			{
				if (!array_key_exists($key, $this->_getterCache))
				{
					$v = $this->getValue($key);
					$this->_getterCache[$key] = $this->app()->stringFormatter()->censorText($v);
				}

				return $this->_getterCache[$key];
			}
			else
			{
				return $this->getValue($key);
			}
		}

		if (!empty($structure->relations[$key]))
		{
			return $this->getRelation($key);
		}

		if (!empty($structure->columnAliases[$key]))
		{
			$alias = $structure->columnAliases[$key] . ($useGetter ? '' : '_');
			return $this->get($alias);
		}

		if (\XF::$debugMode)
		{
			// Note: this is intentionally triggering a warning rather than an exception. This will commonly
			// trigger in templates and we will still be able to render in that case.
			trigger_error("Accessed unknown getter '$originalKey' on " . $this->__toString(), E_USER_WARNING);
		}

		\XF::logException(
			new \InvalidArgumentException("Accessed unknown getter '$originalKey' on " . $this->__toString())
		);

		return null;
	}

	public function getValue($key)
	{
		$columns = $this->_structure->columns;

		if (empty($columns[$key]))
		{
			throw new \InvalidArgumentException("Unknown column $key");
		}

		$column = $columns[$key];

		if (array_key_exists($key, $this->_newValues))
		{
			$value = $this->_newValues[$key];
			if ($value instanceof DeferredValue)
			{
				$value = $this->_resolveDeferredValue($key, $value, 'get');
			}
		}
		else if (array_key_exists($key, $this->_values))
		{
			if ($column['type'] & self::REQUIRES_DECODING)
			{
				if (!array_key_exists($key, $this->_valueCache))
				{
					$this->_valueCache[$key] = $this->_em->decodeValueFromSourceExtended($column['type'], $this->_values[$key], $column);
				}
				$value = $this->_valueCache[$key];
			}
			else
			{
				$value = $this->_values[$key];
			}
		}
		else if (array_key_exists('default', $column))
		{
			$value = $column['default'];
		}
		else
		{
			$value = null;
		}

		return $value;
	}

	public function getValueSourceEncoded($key)
	{
		$columns = $this->_structure->columns;

		if (empty($columns[$key]))
		{
			throw new \InvalidArgumentException("Unknown column $key");
		}

		$column = $columns[$key];

		if (array_key_exists($key, $this->_newValues))
		{
			$value = $this->_newValues[$key];
			if ($value instanceof DeferredValue)
			{
				$value = $this->_resolveDeferredValue($key, $value, 'get');
			}
		}
		else if (array_key_exists($key, $this->_values))
		{
			// already encoded
			return $this->_values[$key];
		}
		else if (array_key_exists('default', $column))
		{
			$value = $column['default'];
		}
		else
		{
			$value = null;
		}

		return $this->_em->encodeValueForSource($column['type'], $value);
	}

	public function getExistingValue($key)
	{
		$columns = $this->_structure->columns;

		if (empty($columns[$key]))
		{
			throw new \InvalidArgumentException("Unknown column $key");
		}

		$column = $columns[$key];

		if (array_key_exists($key, $this->_values))
		{
			if ($column['type'] & self::REQUIRES_DECODING)
			{
				if (!array_key_exists($key, $this->_valueCache))
				{
					$this->_valueCache[$key] = $this->_em->decodeValueFromSourceExtended($column['type'], $this->_values[$key], $column);
				}
				$value = $this->_valueCache[$key];
			}
			else
			{
				$value = $this->_values[$key];
			}
		}
		else if (array_key_exists('default', $column))
		{
			$value = $column['default'];
		}
		else
		{
			$value = null;
		}

		return $value;
	}

	public function getPreviousValue($key)
	{
		$columns = $this->_structure->columns;

		if (empty($columns[$key]))
		{
			throw new \InvalidArgumentException("Unknown column $key");
		}

		$column = $columns[$key];

		if (array_key_exists($key, $this->_previousValues))
		{
			$v = $this->_previousValues[$key];
		}
		else if (array_key_exists($key, $this->_values))
		{
			$v = $this->_values[$key];
		}
		else if (array_key_exists('default', $column))
		{
			return $column['default'];
		}
		else
		{
			return null;
		}

		if ($column['type'] & self::REQUIRES_DECODING)
		{
			return $this->_em->decodeValueFromSourceExtended($column['type'], $v, $column);
		}
		else
		{
			return $v;
		}
	}

	public function getNewValues()
	{
		return $this->_newValues;
	}

	public function getPreviousValues()
	{
		$values = [];
		foreach (array_keys($this->_structure->columns) AS $k)
		{
			$values[$k] = $this->getPreviousValue($k);
		}

		return $values;
	}

	public function getRelation($key)
	{
		$relations = $this->_structure->relations;

		if (empty($relations[$key]))
		{
			throw new \InvalidArgumentException("Unknown relation $key");
		}

		if (!array_key_exists($key, $this->_relations))
		{
			$this->_relations[$key] = $this->_em->getRelation($relations[$key], $this);
		}

		return $this->_relations[$key];
	}

	public function getRelationOrDefault($key, $cascadeSave = true)
	{
		$relations = $this->_structure->relations;

		if (empty($relations[$key]))
		{
			throw new \InvalidArgumentException("Unknown relation $key");
		}

		$relation = $relations[$key];

		if (empty($this->_relations[$key]))
		{
			$data = $this->_em->getRelation($relation, $this);
			if (!$data)
			{
				$data = $this->_em->hydrateDefaultFromRelation($this, $relation);
			}

			$this->_relations[$key] = $data;
		}

		if ($cascadeSave)
		{
			$this->addCascadedSave($this->_relations[$key]);
		}

		return $this->_relations[$key];
	}

	public function hydrateFinderRelation($key, array $entities)
	{
		$relations = $this->_structure->relations;

		if (!isset($relations[$key]))
		{
			throw new \InvalidArgumentException("Unknown relation $key");
		}

		$relation = $relations[$key];

		if (empty($relation['key']) || $relation['type'] != self::TO_MANY)
		{
			throw new \InvalidArgumentException("Relation $key does not support finder hydration");
		}

		$falseEntities = [];
		foreach ($entities AS $entityKey => $value)
		{
			if (!$value)
			{
				$falseEntities[$entityKey] = true;
				unset($entities[$entityKey]);
			}
		}

		$finder = $this->_em->getRelationFinder($relation, $this);
		$this->_relations[$key] = new FinderCollection($finder, $relation['key'], $entities, $falseEntities);
	}

	public function hydrateRelation($key, $value)
	{
		$relations = $this->_structure->relations;

		if (!isset($relations[$key]))
		{
			throw new \InvalidArgumentException("Unknown relation $key");
		}

		$relation = $relations[$key];

		if ($relation['type'] == self::TO_MANY)
		{
			if (!($value instanceof AbstractCollection))
			{
				throw new \InvalidArgumentException("To many relations must be hydrated with collections");
			}
		}
		else
		{
			if ($value !== null && !($value instanceof Entity))
			{
				throw new \InvalidArgumentException("To one relations must be hydrated with entities or null");
			}
		}

		$this->_relations[$key] = $value;
	}

	public function getExistingRelation($key)
	{
		$relations = $this->_structure->relations;

		if (empty($relations[$key]))
		{
			throw new \InvalidArgumentException("Unknown relation $key");
		}

		return $this->_em->getRelation($relations[$key], $this, 'existing');
	}

	public function getRelationFinder($key, $type = 'current')
	{
		$relations = $this->_structure->relations;

		if (empty($relations[$key]))
		{
			throw new \InvalidArgumentException("Unknown relation $key");
		}

		return $this->_em->getRelationFinder($relations[$key], $this, $type);
	}

	public function toArray($allowGetters = true)
	{
		$output = [];
		foreach ($this->_structure->columns AS $key => $null)
		{
			$output[$key] = $allowGetters ? $this->get($key) : $this->getValue($key);
		}

		return $output;
	}

	public final function toApiResult($verbosity = self::VERBOSITY_NORMAL, array $options = [])
	{
		$result = new \XF\Api\Result\EntityResult($this);
		$subResult = $this->setupApiResultData($result, $verbosity, $options);

		if ($subResult instanceof \XF\Api\Result\EntityResultInterface)
		{
			return $subResult;
		}
		else
		{
			return $result;
		}
	}

	/**
	 * This method must be overridden to enable API access to this entity.
	 *
	 * @param \XF\Api\Result\EntityResult $result
	 * @param int $verbosity
	 * @param array $options
	 */
	protected function setupApiResultData(
		\XF\Api\Result\EntityResult $result, $verbosity = self::VERBOSITY_NORMAL, array $options = []
	)
	{
		throw new \LogicException(
			"API result rules not defined by " . $this->_structure->shortName . '. Override setupApiResultData().'
		);
	}

	public function __set($key, $value)
	{
		$this->set($key, $value);
	}

	public function offsetSet($key, $value)
	{
		$this->set($key, $value);
	}

	public function set($key, $value, array $options = [])
	{
		if ($this->_readOnly)
		{
			throw new \LogicException("Entity is read only");
		}

		if ($this->_deleted)
		{
			throw new \LogicException("Attempted to set '$key' on a deleted entity");
		}

		if ($this->_writePending == 'delete')
		{
			throw new \LogicException("Attempted to set '$key' while a deletion was pending");
		}

		if ($this->_writePending && empty($options['forceSet']))
		{
			throw new \LogicException("Attempted to set '$key' while a save was pending without forceSet");
		}

		if (!isset($this->_structure->columns[$key]))
		{
			if (empty($options['skipInvalid']))
			{
				throw new \InvalidArgumentException("Column '$key' is unknown");
			}
			return false;
		}

		$column = $this->_structure->columns[$key];

		if ((!empty($column['readOnly']) || !empty($column['autoIncrement'])) && empty($options['forceSet']))
		{
			if (empty($options['skipInvalid']))
			{
				throw new \InvalidArgumentException("Column '$key' is read only, can only be set with forceSet");
			}
			return false;
		}

		if (!empty($column['writeOnce']) && $this->isUpdate() && empty($options['forceSet']))
		{
			if (empty($options['skipInvalid']))
			{
				throw new \InvalidArgumentException("Column '$key' can only be written on insert or set with forceSet");
			}
			return false;
		}

		if ($value instanceof DeferredValue)
		{
			$this->_setInternal($key, $value);
			return true;
		}

		if (!$this->_verifyValueCustom($value, $key, $column['type'], $column))
		{
			return false;
		}

		$value = $this->_castValueToType($value, $key, $column['type'], $column);

		if (!$this->_em->getValueFormatter()->applyValueConstraints(
			$value, $column['type'], $column, $constraintError, !empty($options['forceConstraint'])
		))
		{
			if ($constraintError)
			{
				$this->error($constraintError, $key, false);
			}

			return false;
		}

		if ($this->_columnValueIsDifferent($key, $value, $column) && $value !== null)
		{
			if (!empty($column['unique']))
			{
				if (!$this->_verifyUniqueValue($value, $key, $column['unique']))
				{
					return false;
				}
			}
			else if (!empty($column['autoIncrement']) || $this->_structure->primaryKey === $key)
			{
				if (!$this->_verifyUniqueValue($value, $key, true))
				{
					return false;
				}
			}
		}

		$this->_setInternal($key, $value);
		return true;
	}

	public function setTrusted($key, $value)
	{
		if (!isset($this->_structure->columns[$key]))
		{
			throw new \InvalidArgumentException("Column '$key' is unknown");
		}

		$column = $this->_structure->columns[$key];
		$value = $this->_castValueToType($value, $key, $column['type'], $column);

		$this->_setInternal($key, $value);
		return true;
	}

	public function setFromEncoded($key, $value, array $options = [])
	{
		if (!isset($this->_structure->columns[$key]))
		{
			throw new \InvalidArgumentException("Column '$key' is unknown");
		}

		$column = $this->_structure->columns[$key];

		$value = $this->_em->decodeValueFromSourceExtended($column['type'], $value, $column);
		return $this->set($key, $value, $options);
	}

	public function setAsSaved($key, $value)
	{
		if (!$this->exists() && !$this->_writeRunning)
		{
			throw new \LogicException("Can only set an already saved value on a saved entity");
		}

		if (!isset($this->_structure->columns[$key]))
		{
			throw new \InvalidArgumentException("Column '$key' is unknown");
		}

		$column = $this->_structure->columns[$key];
		$value = $this->_castValueToType($value, $key, $column['type'], $column);

		$sourceValue = $this->_em->encodeValueForSource($column['type'], $value);

		if ($this->_writeRunning)
		{
			// If a write is currently happening, we can't write into $_values as it may cause isInsert and similar
			// to fail or it may potentially be overwritten. Treat it like a new value and it'll be pushed
			$this->_setInternal($key, $value);
		}
		else
		{
			$this->_values[$key] = $sourceValue;
			$this->_invalidateCachesOnChange($key);
		}

		unset($this->_valueCache[$key]);

		return $sourceValue;
	}

	public function bulkSet(array $values, array $options = [])
	{
		$results = [];
		foreach ($values AS $key => $value)
		{
			$results[$key] = $this->set($key, $value, $options);
		}

		return $results;
	}

	public function bulkSetIgnore(array $values, array $options = [])
	{
		$options['skipInvalid'] = true;
		return $this->bulkSet($values, $options);
	}

	protected function _castValueToType($value, $key, $type, array $columnOptions = [])
	{
		try
		{
			return $this->_em->getValueFormatter()->castValueToType($value, $type, $columnOptions);
		}
		catch (\Exception $e)
		{
			throw new \InvalidArgumentException($e->getMessage() . " [$key]", $e->getCode(), $e);
		}
	}

	protected function _verifyValueCustom(&$value, $key, $type, array $columnOptions)
	{
		$success = true;
		if (!empty($columnOptions['verify']))
		{
			$verifier = $columnOptions['verify'];
			if (is_array($verifier) && $verifier[0] == '$this')
			{
				$verifier[0] = $this;
			}
			else if (is_string($verifier))
			{
				$verifier = [$this, $verifier];
			}

			$success = call_user_func_array($verifier, [
				&$value, $key, $type, $columnOptions, $this
			]);
		}
		else
		{
			$verifyMethod = 'verify' . \XF\Util\Php::camelCase($key);
			if (method_exists($this, $verifyMethod))
			{
				$success = $this->$verifyMethod($value, $key, $type, $columnOptions);
			}
		}

		if ($success !== true && $success !== false)
		{
			throw new \LogicException("Verification method of $key did not return a valid indicator (true/false)");
		}

		return $success;
	}

	protected function _verifyUniqueValue($value, $key, $error = true)
	{
		if ($value === null)
		{
			return true;
		}

		$match = $this->_em->getFinder($this->_structure->shortName)->where($key, '=', $value)->fetchOne();
		if (!$match || $match === $this)
		{
			return true;
		}

		if ($error === true)
		{
			$this->error(\XF::phrase('all_x_values_must_be_unique', ['key' => $key]), $key);
		}
		else if (is_string($error))
		{
			$this->error(\XF::phrase($error), $key);
		}
		else if ($error instanceof \Closure)
		{
			$error($key, $this);
		}

		return false;
	}

	/**
	 * This determines if the new value for a column is different from the *stored version*.
	 * This distinction is significant for multiple calls to set(), when the second call reverts back to the stored
	 * value (meaning this function will return false).
	 *
	 * @param string $key
	 * @param mixed $value
	 * @param array $column
	 *
	 * @return bool
	 */
	protected function _columnValueIsDifferent($key, $value, array $column)
	{
		return (
			!array_key_exists($key, $this->_values)
			|| $value !== $this->_em->decodeValueFromSourceExtended($column['type'], $this->_values[$key], $column)
		);
	}

	protected function _setInternal($key, $value)
	{
		if (!isset($this->_structure->columns[$key]))
		{
			throw new \InvalidArgumentException("Column $key is unknown");
		}

		$column = $this->_structure->columns[$key];

		$skipInvalidate = ($this->isInsert() && !empty($column['autoIncrement']) && $value === null);

		if ($this->_columnValueIsDifferent($key, $value, $column))
		{
			// this means the value is different from the stored value
			$this->_newValues[$key] = $value;

			if (!$skipInvalidate)
			{
				$this->_invalidateCachesOnChange($key);
			}

			return true;
		}
		else if (array_key_exists($key, $this->_newValues))
		{
			// value is different from the new value but the same as the old value
			unset($this->_newValues[$key]);

			if (!$skipInvalidate)
			{
				$this->_invalidateCachesOnChange($key);
			}

			return true;
		}

		return false;
	}

	protected function _invalidateCachesOnChange($key)
	{
		// TODO: need allow getters to choose when to invalidate themselves
		unset($this->_getterCache[$key]);
		unset($this->_relations[$key]);

		foreach ($this->_structure->relations AS $relationName => $relation)
		{
			$conditions = $relation['conditions'];
			if (!is_array($conditions))
			{
				$conditions = [$conditions];
			}

			foreach ($conditions AS $condition)
			{
				if (is_string($condition))
				{
					if ($condition == $key)
					{
						unset($this->_relations[$relationName]);
					}
				}
				else if (count($condition) > 3)
				{
					foreach (array_slice($condition, 2) AS $v)
					{
						if ($v && $v[0] == '$' && substr($v, 1) == $key)
						{
							unset($this->_relations[$relationName]);
							break;
						}
					}
				}
				else if (is_string($condition[2]) && $condition[2][0] == '$' && substr($condition[2], 1) == $key)
				{
					unset($this->_relations[$relationName]);
				}

				if (is_array($condition) && is_string($condition[0]) && $condition[0][0] == '$' && substr($condition[0], 1) == $key)
				{
					unset($this->_relations[$relationName]);
				}
			}
		}
	}

	public function clearCache($key)
	{
		$this->_invalidateCachesOnChange($key);
	}

	public function updateVersionId($versionIdField = 'version_id', $versionStringField = 'version_string', $addOnIdField = 'addon_id')
	{
		$addOnId = $this->getValue($addOnIdField);
		if ($addOnId)
		{
			$addOn = $this->_em->find('XF:AddOn', $addOnId);
			if (!$addOn)
			{
				$this->set($addOnIdField, ''); // no add-on found, make it custom

				$versionId = 0;
				$versionString = '';
			}
			else
			{
				$versionId = $addOn->version_id;
				$versionString = $addOn->version_string;
			}
		}
		else
		{
			$versionId = 0;
			$versionString = '';
		}

		$this->set($versionIdField, $versionId);
		$this->set($versionStringField, $versionString);
		return $versionId;
	}

	public function setOption($name, $value)
	{
		if (!array_key_exists($name, $this->_structure->options))
		{
			throw new \InvalidArgumentException("Unknown entity option: $name");
		}

		$this->_options[$name] = $value;
	}

	public function setOptions(array $options)
	{
		foreach ($options AS $name => $value)
		{
			$this->setOption($name, $value);
		}
	}

	public function resetOption($name)
	{
		if (!array_key_exists($name, $this->_structure->options))
		{
			throw new \InvalidArgumentException("Unknown entity option: $name");
		}

		unset($this->_options[$name]);
	}

	public function hasOption($name)
	{
		return array_key_exists($name, $this->_structure->options);
	}

	/**
	 * @param string $name
	 *
	 * @return mixed
	 */
	public function getOption($name)
	{
		if (array_key_exists($name, $this->_options))
		{
			return $this->_options[$name];
		}
		else if (array_key_exists($name, $this->_structure->options))
		{
			return $this->_structure->options[$name];
		}
		else
		{
			throw new \InvalidArgumentException("Unknown entity option: $name");
		}
	}

	public function isUpdate()
	{
		return $this->exists();
	}

	public function isInsert()
	{
		if ($this->_deleted)
		{
			return false;
		}

		return $this->_values ? false : true;
	}

	public function exists()
	{
		if ($this->_deleted)
		{
			return false;
		}

		return $this->_values ? true : false;
	}

	public function isChanged($key)
	{
		if (is_array($key))
		{
			foreach ($key AS $subKey)
			{
				if ($this->isChanged($subKey))
				{
					return true;
				}
			}

			return false;
		}

		$columns = $this->_structure->columns;

		if (empty($columns[$key]))
		{
			return false;
		}

		return array_key_exists($key, $this->_newValues);
	}

	public function hasChanges()
	{
		return ($this->isInsert() || $this->_newValues);
	}

	public function isStateChanged($key, $state)
	{
		if (!$this->isChanged($key))
		{
			return false;
		}
		else if ($this->getValue($key) == $state)
		{
			return 'enter';
		}
		else if ($this->isUpdate() && $this->getExistingValue($key) == $state)
		{
			return 'leave';
		}
		else
		{
			return false;
		}
	}

	/**
	 * @return null|Behavior[]
	 */
	public function getBehaviors()
	{
		if ($this->_behaviors === null)
		{
			$this->_behaviors = $this->_em->getBehaviors($this, $this->_structure->behaviors);
			foreach ($this->_behaviors AS $behavior)
			{
				$behavior->onSetup();
			}
		}

		return $this->_behaviors;
	}

	public function getBehavior($behavior)
	{
		$behaviors = $this->getBehaviors();
		if (!isset($behaviors[$behavior]))
		{
			throw new \InvalidArgumentException("Unknown behavior '$behavior'");
		}

		return $behaviors[$behavior];
	}

	public function hasBehavior($behavior)
	{
		$behaviors = $this->getBehaviors();
		return isset($behaviors[$behavior]);
	}

	public function useReplaceInto($useReplaceInto)
	{
		$this->_useReplaceInto = $useReplaceInto ? true : false;
	}

	public function addCascadedSave(Entity $entity)
	{
		if ($entity === $this)
		{
			return;
		}

		$id = $entity->getUniqueEntityId();
		$this->_cascadeSave[$id] = $entity;
	}

	public function removeCascadedSave(Entity $entity)
	{
		if ($entity === $this)
		{
			return;
		}

		$id = $entity->getUniqueEntityId();
		unset($this->_cascadeSave[$id]);
	}

	public final function save($throw = true, $newTransaction = true)
	{
		if ($this->_readOnly)
		{
			throw new \LogicException("Entity is read only");
		}
		if ($this->_deleted)
		{
			throw new \LogicException("Cannot save a deleted entity");
		}

		if (!$this->preSave())
		{
			if ($throw)
			{
				throw new \XF\PrintableException($this->_errors);
			}
			return false;
		}
		if ($this->_fillDeferredValues('save'))
		{
			// this could cause a required field to be invalid
			$this->_validateRequirements();
			if ($this->_errors)
			{
				if ($throw)
				{
					throw new \XF\PrintableException($this->_errors);
				}
				return false;
			}
		}

		$db = $this->db();
		$isInsert = $this->isInsert();

		if ($newTransaction)
		{
			$db->beginTransaction();
		}

		$this->_writeRunning = true;

		try
		{
			$this->_saveToSource();

			if ($isInsert)
			{
				$this->_em->attachEntity($this);
			}

			if ($this->_cascadeSave)
			{
				$this->_em->startCascadeEvent('save', $this);

				foreach ($this->_cascadeSave AS $save)
				{
					if (!$this->_em->triggerCascadeAttempt('save', $save))
					{
						continue;
					}

					$save->save($throw, false);
				}

				$this->_em->finishCascadeEvent('save');
			}

			$this->_postSave();
			foreach ($this->getBehaviors() AS $behaviorId => $behavior)
			{
				$behavior->postSave();
			}

			\XF::fire('entity_post_save', [$this], $this->rootClass);
		}
		catch (\Exception $e)
		{
			if ($newTransaction)
			{
				$db->rollback();
			}

			throw $e;
		}

		if ($newTransaction)
		{
			$db->commit();
		}

		// Need to calculate this again after any post-save behaviors as _newValues could've possibly been changed
		// by calls to things like fastUpdate or setAsSaved.
		$newDbValues = $this->_newValues;
		$columns = $this->_structure->columns;
		foreach ($newDbValues AS $column => $value)
		{
			$newDbValues[$column] = $this->_em->encodeValueForSource($columns[$column]['type'], $value);
		}

		$this->_saveCleanUp($newDbValues);

		return true;
	}

	public final function saveIfChanged(&$saved = null, $throw = true, $newTransaction = true)
	{
		if (!$this->_newValues && $this->isUpdate())
		{
			$saved = false;
			return true;
		}

		$saved = true;
		return $this->save($throw, $newTransaction);
	}

	public function fastUpdate($key, $value = null)
	{
		if (!$this->exists() && !$this->_writeRunning)
		{
			throw new \LogicException("Cannot call fastUpdate until the entity is saved");
		}

		if (is_array($key))
		{
			$fields = $key;
		}
		else
		{
			$fields = [$key => $value];
		}

		if (!$fields)
		{
			return;
		}

		// Note: while the save is running, the new values reflect what's in the DB.
		// Also make sure we do this before the setAsSaved call in case we're update the primary key.
		$condition = $this->_getUpdateCondition($this->_writeRunning);

		$dbUpdate = [];
		foreach ($fields AS $key => $value)
		{
			$dbUpdate[$key] = $this->setAsSaved($key, $value);
		}

		$this->db()->update($this->_structure->table, $dbUpdate, $condition);
	}

	public final function preSave()
	{
		// write will be pending after calling this; this means call it only once
		if ($this->_writePending != 'save')
		{
			$this->_fillDeferredValues('preSave');
			$this->_preSave();

			foreach ($this->getBehaviors() AS $behavior)
			{
				$behavior->preSave();
			}

			\XF::fire('entity_pre_save', [$this], $this->rootClass);

			if ($this->_cascadeSave)
			{
				$this->_em->startCascadeEvent('preSave', $this);

				foreach ($this->_cascadeSave AS $childObjectId => $save)
				{
					if (!$this->_em->triggerCascadeAttempt('preSave', $save))
					{
						continue;
					}

					if (!$save->preSave())
					{
						foreach ($save->getErrors() AS $key => $error)
						{
							$this->error($error, is_int($key) ? null : $key, false);
						}
					}
				}

				$this->_em->finishCascadeEvent('preSave');
			}

			if ($this->isInsert())
			{
				$this->_fillInsertDefaults();
			}
			$this->_validateRequirements();

			$this->_writePending = 'save';
		}

		return count($this->_errors) == 0;
	}

	protected function _preSave() {}

	protected function _fillDeferredValues($context)
	{
		$keys = [];
		foreach ($this->_newValues AS $key => $value)
		{
			if ($value instanceof DeferredValue)
			{
				$this->_resolveDeferredValue($key, $value, $context);
				$keys[] = $key;
			}
		}

		return $keys;
	}

	protected function _resolveDeferredValue($key, DeferredValue $deferred, $context)
	{
		$result = $deferred($this, $context);
		if ($deferred->isAssignableAt($context))
		{
			$this->set($key, $result, ['forceSet' => true]);
		}

		return $result;
	}

	protected function _fillInsertDefaults()
	{
		foreach ($this->_structure->columns AS $key => $column)
		{
			if (array_key_exists($key, $this->_newValues))
			{
				continue;
			}

			if (array_key_exists('default', $column))
			{
				$this->_setInternal($key, $column['default']);
			}
			else if (!empty($column['nullable']))
			{
				$this->_setInternal($key, null);
			}
		}
	}

	protected function _validateRequirements()
	{
		foreach ($this->_structure->columns AS $key => $column)
		{
			if (empty($column['required']))
			{
				continue;
			}

			if (isset($this->_newValues[$key]) && $this->_newValues[$key] instanceof DeferredValue)
			{
				// this will be resolved later
				continue;
			}

			if ($this->isUpdate() && !array_key_exists($key, $this->_newValues))
			{
				continue;
			}

			$value = $this->getValue($key);
			$exists = array_key_exists($key, $this->_newValues) || array_key_exists($key, $this->_values);

			if (!empty($column['nullable']) && $value === null && $exists)
			{
				continue;
			}

			if (!$exists || $value === '' || $value === [] || $value === null)
			{
				if (is_string($column['required']))
				{
					$this->error(\XF::phrase($column['required']), $key, false);
				}
				else
				{
					$this->error(\XF::phrase('please_enter_value_for_required_field_x', ['field' => $key]), $key, false);
				}
			}
		}
	}

	protected function _saveToSource()
	{
		$db = $this->db();
		$structure = $this->_structure;
		$columns = $structure->columns;

		$save = $this->_newValues;
		foreach ($save AS $column => $value)
		{
			if (!isset($columns[$column]))
			{
				throw new \LogicException("Unknown column $column was found in data to be saved");
			}

			$save[$column] = $this->_em->encodeValueForSource($columns[$column]['type'], $value);
		}

		if ($save)
		{
			if ($this->isInsert())
			{
				$db->insert($structure->table, $save, $this->_useReplaceInto);
				$this->_fillAutoIncrement($db->lastInsertId(), $save);
			}
			else
			{
				$db->update($structure->table, $save, $this->_getUpdateCondition());
			}
		}

		return $save;
	}

	protected function _fillAutoIncrement($value, array &$newSourceValues)
	{
		foreach ($this->_structure->columns AS $key => $column)
		{
			if (!empty($column['autoIncrement']))
			{
				$this->_setInternal($key, $value);
				$newSourceValues[$key] = $value;
				return true;
			}
		}

		return false;
	}

	protected function _postSave() {}

	protected function _saveCleanUp(array $newDbValues)
	{
		$this->_writePending = false;
		$this->_writeRunning = false;
		$this->_previousValues = $this->_values;
		$this->_values = array_merge($this->_values, $newDbValues);
		$this->_newValues = [];
		$this->_errors = [];

		// need to wipe out this cache as we've overridden
		foreach ($newDbValues As $key => $null)
		{
			unset($this->_valueCache[$key]);
		}
	}

	public function reset()
	{
		$this->_writePending = false;
		$this->_newValues = [];
		$this->_errors = [];
		$this->_getterCache = [];
		$this->_valueCache = [];
		$this->_options = [];
	}

	public final function delete($throw = true, $newTransaction = true)
	{
		if ($this->_deleted)
		{
			return true;
		}
		if (!$this->exists())
		{
			throw new \LogicException("Cannot delete a non-saved entity");
		}
		if ($this->_newValues)
		{
			throw new \LogicException("Cannot delete an entity that has been partially updated");
		}
		if ($this->_readOnly)
		{
			throw new \LogicException("Entity is read only");
		}

		if (!$this->preDelete())
		{
			if ($throw)
			{
				throw new \XF\PrintableException($this->_errors);
			}
			return false;
		}

		$db = $this->db();

		if ($newTransaction)
		{
			$db->beginTransaction();
		}

		$this->_writeRunning = true;
		$this->_deleted = true;

		$rowAffected = $db->delete($this->_structure->table, $this->_getUpdateCondition());

		$this->_em->startCascadeEvent('delete', $this);

		foreach ($this->_structure->relations AS $relationId => $definition)
		{
			if (!empty($definition['cascadeDelete']) && $relation = $this->getRelation($relationId))
			{
				if ($relation instanceof Entity)
				{
					if (!$this->_em->triggerCascadeAttempt('delete', $relation))
					{
						continue;
					}

					$relation->delete($throw, false);
				}
				else
				{
					/** @var $child Entity */
					foreach ($relation AS $child)
					{
						if (!$this->_em->triggerCascadeAttempt('delete', $child))
						{
							continue;
						}

						$child->delete($throw, false);
					}
				}
			}
		}

		$this->_em->finishCascadeEvent('delete');

		// note: only perform the following actions if the actual delete affected a row
		// this ensures there cannot be any unintended consequences by calling them repeatedly
		if ($rowAffected)
		{
			$this->_postDelete();

			foreach ($this->getBehaviors() AS $behavior)
			{
				$behavior->postDelete();
			}

			\XF::fire('entity_post_delete', [$this], $this->rootClass);
		}

		if ($newTransaction)
		{
			$db->commit();
		}

		$this->_em->detachEntity($this);
		$this->_writePending = false;
		$this->_writeRunning = false;

		return true;
	}

	public final function preDelete()
	{
		if ($this->_deleted)
		{
			return true;
		}

		if ($this->_writePending != 'delete')
		{
			$this->_preDelete();

			foreach ($this->getBehaviors() AS $behavior)
			{
				$behavior->preDelete();
			}

			\XF::fire('entity_pre_delete', [$this], $this->rootClass);

			$this->_em->startCascadeEvent('preDelete', $this);

			foreach ($this->_structure->relations AS $relationId => $definition)
			{
				if (!empty($definition['cascadeDelete']) && $relation = $this->getRelation($relationId))
				{
					if ($relation instanceof Entity)
					{
						if (!$this->_em->triggerCascadeAttempt('preDelete', $relation))
						{
							continue;
						}

						if (!$relation->preDelete())
						{
							foreach ($relation->getErrors() AS $key => $error)
							{
								$this->error($error, is_int($key) ? null : $key, false);
							}
						}
					}
					else
					{
						/** @var $child Entity */
						foreach ($relation AS $child)
						{
							if (!$this->_em->triggerCascadeAttempt('preDelete', $child))
							{
								continue;
							}

							if (!$child->preDelete())
							{
								foreach ($child->getErrors() AS $key => $error)
								{
									$this->error($error, is_int($key) ? null : $key, false);
								}
							}
						}
					}
				}
			}

			$this->_em->finishCascadeEvent('preDelete');
			$this->_writePending = 'delete';
		}

		return count($this->_errors) == 0;
	}

	public function isDeleted()
	{
		return $this->_deleted;
	}

	protected function _preDelete() {}

	protected function _postDelete() {}

	protected function _getUpdateCondition($current = false)
	{
		if (!$this->_values)
		{
			if (!$current || !$this->_writeRunning)
			{
				throw new \LogicException("Cannot get the update condition for a non-existing entity");
			}
		}

		$conditions = [];
		$db = $this->db();
		foreach ((array)$this->_structure->primaryKey AS $key)
		{
			$value = $current ? $this->getValue($key) : $this->getExistingValue($key);
			if ($value === null)
			{
				throw new \LogicException("Found null in primary key for entity. Was this called before saving?");
			}
			$conditions[] = "`$key` = " . $db->quote($value);
		}

		if (!$conditions)
		{
			throw new \LogicException("No primary key defined for entity " . get_class($this));
		}

		return implode(' AND ', $conditions);
	}

	public function getIdentifierValues()
	{
		$values = [];
		foreach ((array)$this->_structure->primaryKey AS $key)
		{
			$value = $this->getValue($key);
			if ($value === null)
			{
				return null; // primary keys cannot be null (after being saved at least)
			}
			$values[$key] = $value;
		}

		if (!$values)
		{
			throw new \LogicException("No primary key defined for entity " . get_class($this));
		}

		return $values;
	}

	public function getIdentifier()
	{
		$keys = $this->getIdentifierValues();
		return $keys ? implode('-', $keys) : null;
	}

	/**
	 * @return string|int
	 */
	public function getEntityId()
	{
		$this->assertSimpleEntityId();

		$key = $this->_structure->primaryKey;
		return $this->getValue($key);
	}

	public function getExistingEntityId()
	{
		$this->assertSimpleEntityId();

		$key = $this->_structure->primaryKey;
		return $this->getExistingValue($key);
	}

	protected function assertSimpleEntityId()
	{
		if (is_array($this->_structure->primaryKey))
		{
			throw new \LogicException("Cannot get a simple ID from the entity " . $this->_structure->shortName);
		}
	}

	public function getUniqueEntityId()
	{
		return $this->_uniqueEntityId;
	}

	public function getEntityContentType()
	{
		return $this->_structure->contentType;
	}

	public function error($message, $key = null, $specificError = true)
	{
		if ($key)
		{
			if ($specificError || !isset($this->_errors[$key]))
			{
				$this->_errors[$key] = $message;
			}
		}
		else
		{
			$this->_errors[] = $message;
		}
	}

	public function getErrors()
	{
		return $this->_errors;
	}

	public function hasErrors()
	{
		return count($this->_errors) > 0;
	}

	public function setReadOnly($readOnly)
	{
		$this->_readOnly = (bool)$readOnly;
	}

	public function getReadOnly()
	{
		return $this->_readOnly;
	}

	public function __isset($key)
	{
		$structure = $this->_structure;

		if (substr($key, -1) == '_')
		{
			$key = substr($key, 0, -1);
			$useGetter = false;
		}
		else
		{
			$useGetter = true;
		}

		if ($useGetter && isset($structure->getters[$key]))
		{
			return true;
		}

		return (
			isset($structure->columns[$key])
			|| isset($structure->relations[$key])
		);
	}

	public function offsetExists($key)
	{
		return $this->__isset($key);
	}

	public function offsetUnset($key)
	{
		throw new \LogicException('Entity offsets may not be unset');
	}

	public function isValidColumn($key)
	{
		return isset($this->_structure->columns[$key]);
	}

	public function isValidRelation($key)
	{
		return isset($this->_structure->relations[$key]);
	}

	public function isValidGetter($key)
	{
		return isset($this->_structure->getters[$key]);
	}

	public function isValidKey($key)
	{
		return $this->__isset($key);
	}

	protected function _getDeferredValue(\Closure $handler, $assignTime = 'preSave')
	{
		return $this->_em->getDeferredValue($handler, $assignTime);
	}

	/**
	 * @param string $identifier
	 *
	 * @return Finder
	 */
	public function finder($identifier)
	{
		return $this->_em->getFinder($identifier);
	}

	/**
	 * @param string $identifier
	 *
	 * @return Repository
	 */
	public function repository($identifier)
	{
		return $this->_em->getRepository($identifier);
	}

	/**
	 * @return \XF\Db\AbstractAdapter
	 */
	public function db()
	{
		return $this->_em->getDb();
	}

	/**
	 * @return Manager
	 */
	public function em()
	{
		return $this->_em;
	}

	/**
	 * @return Structure
	 */
	public function structure()
	{
		return $this->_structure;
	}

	/**
	 * @return \XF\App
	 */
	public function app()
	{
		return \XF::app();
	}

	public function __toString()
	{
		$key = $this->getIdentifierValues();
		if (!$key)
		{
			$key = '[unsaved]';
		}
		else
		{
			$key = '[' . implode(', ', $key) . ']';
		}

		return $this->_structure->shortName . $key;
	}

	public function __debugInfo()
	{
		$dump = (array)$this;
		unset($dump["\0*\0_getterCache"], $dump["\0*\0_valueCache"], $dump["\0*\0_structure"], $dump["\0*\0_em"]);

		return $dump;
	}

	/**
	 * @param Structure $structure
	 * @return Structure
	 * @throws \LogicException
	 */
	public static function getStructure(Structure $structure)
	{
		throw new \LogicException(get_called_class() . '::getStructure() must be overridden');
	}

	public function getMaxLength($fieldName)
	{
		if (isset($this->_structure->columns[$fieldName]['maxLength']))
		{
			return $this->_structure->columns[$fieldName]['maxLength'];
		}
		else
		{
			return null;
		}
	}

	public function __sleep()
	{
		throw new \LogicException('Entities cannot be serialized or unserialized');
	}

	public function __wakeup()
	{
		throw new \LogicException('Entities cannot be serialized or unserialized');
	}
}
