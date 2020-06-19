<?php

namespace XF\Legacy;

use XF\Legacy\DataWriter\Phrase as XenForo_DataWriter_Phrase;

abstract class DataWriter
{
	/**
	* Constant for error handling. Use this to trigger an exception when an error occurs.
	*
	* @var integer
	*/
	const ERROR_EXCEPTION = 1;

	/**
	* Constant for error handling. Use this to push errors onto an array. If you try
	* to save while there are errors, an exception will be thrown.
	*
	* @var integer
	*/
	const ERROR_ARRAY = 2;

	/**
	* Constant for error handling. Use this to push errors onto an array. If you try
	* to save while there are errors, the save will silently fail. Use this in places
	* where you aren't interested in handling errors.
	*
	* @var integer
	*/
	const ERROR_SILENT = 3;

	/**
	 * Constant for data fields. Use this for 0/1 boolean integer fields.
	 *
	 * @var string
	 */
	const TYPE_BOOLEAN = 'boolean';

	/**
	* Constant for data fields. Use this for string fields. String fields are assumed
	* to be UTF-8 and limits will refer to characters.
	*
	* @var string
	*/
	const TYPE_STRING = 'string';

	/**
	* Constant for data fields. Use this for binary or ASCII-only fields. Limits
	* will refer to bytes.
	*
	* @var string
	*/
	const TYPE_BINARY = 'binary';

	/**
	* Constant for data fields. Use this for integer fields. Limits can be applied
	* on the range of valid values.
	*
	* @var string
	*/
	const TYPE_INT = 'int';

	/**
	* Constant for data fields. Use this for unsigned integer fields. Negative values
	* will always fail to be valid. Limits can be applied on the range of valid values.
	*
	* @var string
	*/
	const TYPE_UINT = 'uint';

	/**
	 * Constant for data fields. Use this for unsigned integer fields. This differs from
	 * TYPE_UINT in that negative values will be silently cast to 0. Limits can be
	 * applied on the range of valid values.
	 *
	 * @var string
	 */
	const TYPE_UINT_FORCED = 'uint_forced';

	/**
	* Constant for data fields. Use this for float fields. Limits can be applied
	* on the range of valid values.
	*
	* @var string
	*/
	const TYPE_FLOAT = 'float';

	/**
	 * Data is serialized. Ensures that if the data is not a string, it is serialized to one.
	 *
	 * @var string
	 */
	const TYPE_SERIALIZED = 'serialized';

	/**
	 * Data is serialized to JSON.
	 *
	 * @var string
	 */
	const TYPE_JSON = 'json';

	/**
	 * Constant for data fields. Use this for fields that have a type that cannot be
	 * known statically. Use this sparingly, as you must write code to ensure that
	 * the value is a scalar before it is inserted into the DB. The behavior if you
	 * don't do this is not defined!
	 *
	 * @var string
	 */
	const TYPE_UNKNOWN = 'unknown';

	/**
	* Database object
	*
	* @var \XF\Db\AbstractAdapter
	*/
	protected $_db = null;

	/**
	* Array of valid fields in the table. See {@link _getFields()} for more info.
	*
	* @var array
	*/
	protected $_fields = [];

	/**
	* Options that change the behavior of the data writer. All options that a
	* data writer supports must be documented in this array, preferably
	* by the use of constants.
	*
	* @var array
	*/
	protected $_options = [];

	/**
	* Extra data that the data writer can use. The DW should usually function without
	* any data in this array. Any data that DW supports should be documented in
	* this array, preferably by the use of constants.
	*
	* Required data (for example, a related phrase) can be included here if necessary.
	*
	* @var array
	*/
	protected $_extraData = [];

	/**
	* Data that has just been set an is about to be saved.
	*
	* @var array
	*/
	protected $_newData = [];

	/**
	* Existing data in the database. This is only populated when updating a record.
	*
	* @var array
	*/
	protected $_existingData = [];

	/**
	 * When enabled, preSave, postSave, and verification functions are disabled. To be used
	 * when using the DW for importing data in bulk. Note that you are responsible for manually
	 * replicating the post save effects.
	 *
	 * @var boolean
	 */
	protected $_importMode = false;

	/**
	* Type of error handler. See {@link ERROR_EXCEPTION} and related.
	*
	* @var integer
	*/
	protected $_errorHandler = 0;

	/**
	* Array of errors. This is always populated when an error occurs, though
	* an exception may be thrown as well.
	*
	* @var array
	*/
	protected $_errors = [];

	/**
	* Tracks whether {@link _preSave()} was called. It can only be called once.
	*
	* @var boolean
	*/
	protected $_preSaveCalled = false;

	/**
	* Tracks whether {@link _preDelete()} was called. It can only be called once.
	*
	* @var boolean
	*/
	protected $_preDeleteCalled = false;

	/**
	* Options that can be passed into {@link set()}. Available options are:
	* (default value is listed in parentheses)
	*	* ignoreInvalidFields (false) - when true, an invalid field is simply ignored; otherwise an error is triggered
	*	* replaceInvalidWithDefault (false) - when true, invalid values are replaced with the default value (if there is one)
	*	* runVerificationCallback (true) - when true, verification callbacks are run when setting
	*	* setAfterPreSave (false) - when true, you may set data after preSave is called. This has very specific uses.
	*
	* @var array
	*/
	protected $_setOptions = [
		'ignoreInvalidFields' => false,
		'replaceInvalidWithDefault' => false,
		'runVerificationCallback' => true,
		'setAfterPreSave' => false
	];

	/**
	 * Title of the phrase that will be created when a call to set the
	 * existing data fails (when the data doesn't exist).
	 *
	 * @var string
	 */
	protected $_existingDataErrorPhrase = 'existing_data_required_data_writer_not_found';

	/**
	 * Standard approach to caching model objects for the lifetime of the data writer.
	 * This is now a static cache to allow data to be reused between data writers, as they
	 * are often used in bulk.
	 *
	 * @var array
	 */
	protected static $_modelCache = [];

	/**
	 * If true, a language rebuild is triggered at the very end of a save/delete process.
	 *
	 * @var boolean
	 */
	protected $_triggerLanguageRebuild = false;

	/**
	* Constructor.
	*
	* @param int $errorHandler Error handler. See {@link ERROR_EXCEPTION} and related.
	* @param array|null $inject Dependency injector. Array keys available: db, cache.
	*/
	public function __construct($errorHandler = self::ERROR_ARRAY, array $inject = null)
	{
		$this->_db = \XF::db();

		$this->setErrorHandler($errorHandler);

		$fields = $this->_getFields();
		if (is_array($fields))
		{
			$this->_fields = $fields;
		}

		$options = $this->_getDefaultOptions();
		if (is_array($options))
		{
			$this->_options = $options;
		}
	}

	/**
	* Gets the fields that are defined for the table. This should return an array with
	* each key being a table name and a further array with each key being a field in database.
	* The value of each entry should be an array, which
	* may have the following keys:
	*	* type - one of the TYPE_* constants, this controls the type of data in the field
	*	* autoIncrement - set to true when the field is an auto_increment field. Used to populate this field after an insert
	*	* required - if set, inserts will be prevented if this field is not set or an empty string (0 is accepted)
	*	* default - the default value of the field; used if the field isn't set or if the set value is outside the constraints (if a {@link $_setOption} is set)
	*	* maxLength - for string/binary types only, the maximum length of the data. For strings, in characters; for binary, in bytes.
	*	* min - for numeric types only, the minimum value allowed (inclusive)
	*	* max - for numeric types only, the maximum value allowed (inclusive)
	*	* allowedValues - an array of allowed values for this field (commonly for enums)
	*	* verification - a callback to do more advanced verification. Callback function will take params for $value and $this.
	*
	* @return array
	*/
	abstract protected function _getFields();

	public function getFields()
	{
		return $this->_getFields();
	}

	public function getFieldNames($tableName = null)
	{
		$tables = $this->_getFields();

		if (!empty($tableName))
		{
			if (empty($tables[$tableName]))
			{
				$this->error("No fields are defined for table '{$tableName}'.");
			}

			return array_keys($tables[$tableName]);
		}

		$fieldNames = [];

		foreach ($tables AS $fields)
		{
			foreach ($fields AS $fieldName => $fieldInfo)
			{
				$fieldNames[] = $fieldName;
			}
		}

		return array_unique($fieldNames);
	}

	/**
	* Gets the actual existing data out of data that was passed in. This data
	* may be a scalar or an array. If it's a scalar, assume that it is the primary
	* key (if there is one); if it is an array, attempt to extract the primary key
	* (or some other unique identifier). Then fetch the correct data from a model.
	*
	* @param mixed $data Data that can uniquely ID this item
	*
	* @return array|false
	*/
	abstract protected function _getExistingData($data);

	/**
	* Gets SQL condition to update the existing record. Should read from {@link _existingData}.
	*
	* @param string $tableName Table name
	*
	* @return string
	*/
	abstract protected function _getUpdateCondition($tableName);

	/**
	* Get the primary key from either an array of data or just the scalar value
	*
	* @param mixed $data Array of data containing the primary field or a scalar ID
	* @param string $primaryKeyField Primary key field, if data is an array
	* @param string $tableName Table name, if empty the first table defined in fields is used
	*
	* @return string|false
	*/
	protected function _getExistingPrimaryKey($data, $primaryKeyField = '', $tableName = '')
	{
		if (!$tableName)
		{
			$tableName = $this->_getPrimaryTable();
		}

		if (!$primaryKeyField)
		{
			$primaryKeyField = $this->_getAutoIncrementField($tableName);
		}

		if (!isset($this->_fields[$tableName][$primaryKeyField]))
		{
			return false;
		}

		if (is_array($data))
		{
			if (array_key_exists($primaryKeyField, $data))
			{
				return $data[$primaryKeyField];
			}
			else
			{
				return false;
			}
		}
		else
		{
			return $data;
		}
	}

	/**
	 * Splits a (complete) data record array into its constituent tables using the _fields array for use in _getExistingData
	 *
	 * In order for this to work, the following must be true:
	 * 1) The input array must provide all data that this datawriter requires
	 * 2) There can be no overlapping field names in the tables that define this data, unless those fields all store the same value
	 * 3) IMPORTANT: If the input array does *not* include all the required data fields, it is your responsibility to provide it after this function returns.
	 *
	 * @param array $dataArray Complete data record
	 *
	 * @return array
	 */
	public function getTablesDataFromArray(array $dataArray)
	{
		$data = [];

		/**
		 * there are no dupe fieldnames between the tables,
		 * so we might as well populate the existing data using the existing _fields array
		 */
		foreach ($this->_fields AS $tableName => $field)
		{
			$data[$tableName] = [];

			foreach ($field AS $fieldName => $fieldInfo)
			{
				/**
				 * don't attempt to set fields that are not provided by $dataArray,
				 * it is your own responsibility to resolve this afterwards the function returns
				 */
				if (isset($dataArray[$fieldName]))
				{
					$data[$tableName][$fieldName] = $dataArray[$fieldName];
				}
			}
		}

		return $data;
	}

	/**
	* Gets the default set of options for this data writer. This is automatically
	* called in the constructor. If you wish to set options in your data writer,
	* override this function. As this is handled at run time rather than compile time,
	* you may use dynamic behaviors to set the option defaults.
	*
	* @return array
	*/
	protected function _getDefaultOptions()
	{
		return [];
	}

	/**
	* Sets the default options for {@link set()}. See {@link $_setOptions} for
	* the available options.
	*
	* @param array
	*/
	public function setDefaultSetOptions(array $setOptions)
	{
		$this->_setOptions = array_merge($this->_setOptions, $setOptions);
	}

	/**
	* Sets an option. The meaning of this option is completely domain specific.
	* If an unknown option specified, an exception will be triggered.
	*
	* @param string $name Name of the option to set
	* @param mixed  $value Value of the option
	*/
	public function setOption($name, $value)
	{
		if (!array_key_exists($name, $this->_options))
		{
			throw new \InvalidArgumentException('Cannot set unknown option');
		}

		$this->_options[$name] = $value;
	}

	/**
	* Gets an option. The meaning of this option is completely domain specific.
	* If an unknown option specified, an exception will be triggered.
	*
	* @param string $name Name of the option to get
	*
	* @return mixed  Value of the option
	*/
	public function getOption($name)
	{
		if (!array_key_exists($name, $this->_options))
		{
			throw new \InvalidArgumentException('Cannot get unknown option ' . $name);
		}

		return $this->_options[$name];
	}

	/**
	* Sets extra data that the DW can use to help it. The DW should be able
	* to function without this data.
	*
	* @param string $name Name of the data
	* @param mixed  $value Value of the data
	*/
	public function setExtraData($name, $value)
	{
		$this->_extraData[$name] = $value;
	}

	/**
	 * Gets the named piece of extra data.
	 *
	 * @param string $name
	 *
	 * @return mixed
	 */
	public function getExtraData($name)
	{
		return $this->isExtraDataSet($name) ? $this->_extraData[$name] : null;
	}

	/**
	 * Determines whether or not the specified extra data field has been set at all
	 *
	 * @param string $name
	 *
	 * @return boolean
	 */
	public function isExtraDataSet($name)
	{
		return array_key_exists($name, $this->_extraData);
	}

	/**
	 * Sets the import mode. When enabled, preSave, postSave, and verification functions are disabled.
	 *
	 * @param boolean$mode
	 */
	public function setImportMode($mode)
	{
		$this->_importMode = $mode;
	}

	/**
	* Sets the existing data. This causes the system to do an update instead of an insert.
	* This function triggers an error if no data can be fetched from the provided data.
	*
	* @param mixed   $data Data that can uniquely ID this item
	* @param boolean $trustInputAsPrimary If true, trust the passed data to be based on data in DB; if false, the data is used as is (if it's an array)
	*
	* @return boolean
	*/
	public function setExistingData($data, $trustInputAsPrimary = false)
	{
		if ($trustInputAsPrimary && is_array($data) && sizeof(array_keys($this->_fields)) == 1)
		{
			// don't force standardization and given an array, so use it as is
			$existing = [$this->_getPrimaryTable() => $data];
		}
		else
		{
			$existing = $this->_getExistingData($data);
			if (is_array($existing) && !array_key_exists($this->_getPrimaryTable(), $existing))
			{
				throw new \InvalidArgumentException('_getExistingData returned an array but did not include data for the primary table');
			}
		}

		// data is only valid if the data is an array and every entry (table) is an array as well
		$validData = true;
		if (!is_array($existing))
		{
			$validData = false;
		}
		else
		{
			foreach ($existing AS $tableData)
			{
				if (!is_array($tableData))
				{
					$validData = false;
					break;
				}
			}
		}

		if ($validData)
		{
			$this->_existingData = $existing;
			return true;
		}
		else
		{
			$this->_triggerInvalidExistingDataError();
			return false;
		}
	}

	/**
	 * Triggers the error for when invalid existing data was requested. This can (and generally
	 * should) be extended by concrete DWs to give an error that is more specific to
	 * their content type.
	 */
	protected function _triggerInvalidExistingDataError()
	{
		$this->error(\XF::phrase($this->_existingDataErrorPhrase));
	}

	/**
	* Sets a field. This value will be updated when save is called.
	*
	* @param string $field Name of field to update
	* @param string $value Value to update with
	* @param string $tableName Table name, if empty then all tables with that column
	* @param array  $options Options. See {@link $_setOptions).
	*
	* @return boolean
	*/
	public function set($field, $value, $tableName = '', array $options = null)
	{
		$options = (is_array($options) ? array_merge($this->_setOptions, $options) : $this->_setOptions);

		if ($this->_preSaveCalled && empty($options['setAfterPreSave']))
		{
			throw new \LogicException('Set cannot be called after preSave has been called.');
		}

		$validField = false;
		foreach ($this->_fields AS $table => $fields)
		{
			if ($tableName && $tableName != $table)
			{
				continue;
			}

			if (isset($fields[$field]) && is_array($fields[$field]))
			{
				$validField = true;
				$newValue = $value;

				if ($this->_isFieldValueValid($field, $fields[$field], $newValue, $options))
				{
					$this->_setInternal($table, $field, $newValue);
				}
			}
		}

		if (!$validField && empty($options['ignoreInvalidFields']))
		{
			$this->error("The field '$field' was not recognised.", $field, false);
		}

		return $validField;
	}

	/**
	 * Internal function to set a field without any validation or type casting.
	 * Use only when you're sure validation, etc isn't needed. The field will only
	 * be set if the value has changed.
	 *
	 * @param string $table Table the field belongs to
	 * @param string $field Name of the field
	 * @param mixed $newValue Value for the field
	 * @param boolean $forceSet If true, the set always goes through
	 */
	protected function _setInternal($table, $field, $newValue, $forceSet = false)
	{
		$existingValue = $this->get($field, $table);
		if ($forceSet
			|| $existingValue === null
			|| !is_scalar($newValue)
			|| !is_scalar($existingValue)
			|| strval($newValue) !== strval($existingValue)
		)
		{
			if ($newValue === $this->getExisting($field, $table))
			{
				unset($this->_newData[$table][$field]);
			}
			else
			{
				$this->_newData[$table][$field] = $newValue;
			}
		}
	}

	/**
	 * Internal helper for calling set after save and not triggering an error.
	 *
	 * @param string $field Name of field to update
	 * @param string $newValue Value to update with
	 * @param string $tableName Table name, if empty then all tables with that column
	 * @param array  $options Options. See {@link $_setOptions).
	 *
	 * @return boolean
	 */
	protected function _setPostSave($field, $newValue, $tableName = '', array $options = [])
	{
		$options['setAfterPreSave'] = true;

		return $this->set($field, $newValue, $tableName, $options);
	}

	/**
	* Determines if the provided value for a field is valid. The value may be
	* modified based on type, contraints, or other verification methods.
	*
	* @param string $fieldName Name of the field.
	* @param array  $fieldData Data about the field (includes type, contraints, callback, etc)
	* @param mixed  $value Value for the field
	* @param array  $options Options. Uses {@link $_setOptions}.
	*
	* @return boolean
	*/
	protected function _isFieldValueValid($fieldName, array $fieldData, &$value, array $options = [])
	{
		$fieldType = isset($fieldData['type']) ? $fieldData['type'] : self::TYPE_BINARY;
		$value = $this->_castValueToType($fieldType, $value, $fieldName, $fieldData);

		if (!empty($options['runVerificationCallback']) && !empty($fieldData['verification']))
		{
			if (!$this->_runVerificationCallback($fieldData['verification'], $value, $fieldData, $fieldName))
			{
				// verification callbacks are responsible for throwing errors
				return false;
			}
		}

		$checkLimits = $this->_applyFieldValueLimits($fieldType, $value, $fieldData);

		if ($checkLimits !== true)
		{
			if (empty($options['replaceInvalidWithDefault']) || !array_key_exists('default', $fieldData))
			{
				$this->error($checkLimits, $fieldName, false);
				return false;
			}
			else
			{
				$value = $fieldData['default'];
			}
		}

		return true;
	}

	/**
	 * Casts the field value based on the specified type (TYPE_* constants).
	 *
	 * @param string $fieldType Type to cast to
	 * @param mixed $value Value to cast
	 * @param string $fieldName Name of the field being cast
	 * @param array $fieldData Array of all field data information, for extra options
	 *
	 * @return mixed
	 */
	protected function _castValueToType($fieldType, $value, $fieldName, array $fieldData)
	{
		switch ($fieldType)
		{
			case self::TYPE_STRING:
				if (isset($fieldData['noTrim']))
				{
					return strval($value);
				}
				else
				{
					return trim(strval($value));
				}

			case self::TYPE_BINARY:
				return strval($value);

			case self::TYPE_UINT_FORCED:
				$value = intval($value);
				return ($value < 0 ? 0 : $value);

			case self::TYPE_UINT:
			case self::TYPE_INT:
				return intval($value);

			case self::TYPE_FLOAT:
				return strval($value) + 0;

			case self::TYPE_BOOLEAN:
				return ($value ? 1 : 0);

			case self::TYPE_SERIALIZED:
				if (!is_string($value))
				{
					return serialize($value);
				}

				if (@unserialize($value) === false && $value != serialize(false))
				{
					throw new \InvalidArgumentException('Value is not unserializable');
				}

				return $value;

			case self::TYPE_JSON:
				if (!is_string($value))
				{
					return json_encode($value);
				}

				if (json_decode($value) === null)
				{
					throw new \InvalidArgumentException('Value cannot be JSON decoded');
				}

				return $value;

			case self::TYPE_UNKNOWN:
				return $value; // unmodified

			default:
				throw new \InvalidArgumentException((
					($fieldName === false)
					? "There is no field type '$fieldType'."
					: "The field type specified for '$fieldName' is not valid ($fieldType)."
				));
		}
	}

	/**
	* Applies value limits to a field based on type and other constraints.
	* Returns true if the field meets the constraints. The passed in value will
	* be modified by reference.
	*
	* @param string $fieldType Type of the field. See the TYPE_* constants.
	* @param mixed  $value Value for the field.
	* @param array  $extraLimits Extra constraints
	*
	* @return boolean|string Either TRUE or an error message
	*/
	protected function _applyFieldValueLimits($fieldType, &$value, array $extraLimits = [])
	{
		// constraints
		switch ($fieldType)
		{
			case self::TYPE_STRING:
			case self::TYPE_BINARY:
				$strlenFunc = ($fieldType == self::TYPE_STRING ? 'utf8_strlen' : 'strlen');

				if (isset($extraLimits['maxLength']) && $strlenFunc($value) > $extraLimits['maxLength'])
				{
					if ($this->_importMode)
					{
						if ($strlenFunc == 'utf8_strlen')
						{
							$value = utf8_substr($value, 0, $extraLimits['maxLength']);
						}
						else
						{
							$value = substr($value, 0, $extraLimits['maxLength']);
						}
					}
					else
					{
						return \XF::phrase('please_enter_value_using_x_characters_or_fewer', ['count' => $extraLimits['maxLength']]);
					}
				}
				break;

			case self::TYPE_UINT_FORCED:
			case self::TYPE_UINT:
				if ($value < 0)
				{
					if ($this->_importMode)
					{
						$value = 0;
					}
					else
					{
						return \XF::phrase('please_enter_positive_whole_number');
					}
				}
				else if ($this->_importMode && $value > 4294967295)
				{
					$value = 4294967295;
				}
				if (!array_key_exists('max', $extraLimits))
				{
					$extraLimits['max'] = 4294967295;
				}
				break;

			case self::TYPE_INT:
				if ($this->_importMode)
				{
					if ($value > 2147483647)
					{
						$value = 2147483647;
					}
					else if ($value < -2147483648)
					{
						$value = -2147483648;
					}
				}
				if (!array_key_exists('min', $extraLimits))
				{
					$extraLimits['min'] = -2147483648;
				}
				if (!array_key_exists('max', $extraLimits))
				{
					$extraLimits['max'] = 2147483647;
				}
				break;
		}

		switch ($fieldType)
		{
			case self::TYPE_UINT_FORCED:
			case self::TYPE_UINT:
			case self::TYPE_INT:
			case self::TYPE_FLOAT:
				if (isset($extraLimits['min']) && $value < $extraLimits['min'])
				{
					if ($this->_importMode)
					{
						$value = $extraLimits['min'];
					}
					else
					{
						return \XF::phrase('please_enter_number_that_is_at_least_x', ['min' => $extraLimits['min']]);
					}
				}

				if (isset($extraLimits['max']) && $value > $extraLimits['max'])
				{
					if ($this->_importMode)
					{
						$value = $extraLimits['max'];
					}
					else
					{
						return \XF::phrase('please_enter_number_that_is_no_more_than_x', ['max' => $extraLimits['max']]);
					}
				}
				break;
		}

		if (isset($extraLimits['allowedValues']) && is_array($extraLimits['allowedValues']) && !in_array($value, $extraLimits['allowedValues']))
		{
			return \XF::phrase('please_enter_valid_value');
		}

		return true;
	}

	/**
	* Runs the verification callback. This callback may modify the value if it chooses to.
	* Callback receives 2 params: the value and this object. The callback must return true
	* if the value was valid.
	*
	* Returns true if the verification was successful.
	*
	* @param callback $callback Callback to run. Use an array with a string '$this' to callback to this object.
	* @param mixed    $value Value to verify
	* @param array    $fieldData Information about the field, including all constraints to be applied
	* @param string $fieldName
	*
	* @return boolean
	*/
	protected function _runVerificationCallback($callback, &$value, array $fieldData, $fieldName = false)
	{
		if ($this->_importMode)
		{
			return true;
		}

		if (is_array($callback) && isset($callback[0]) && $callback[0] == '$this')
		{
			$callback[0] = $this;
		}

		return (boolean)call_user_func_array($callback,
			[&$value, $this, $fieldName, $fieldData]
		);
	}

	/**
	* Helper method to bulk set values from an array.
	*
	* @param array $fields Key-value pairs of fields and values.
	* @param array $options Options to pass into {@link set()}. See {@link $_setOptions}.
	*/
	public function bulkSet(array $fields, array $options = null)
	{
		foreach ($fields AS $field => $value)
		{
			$this->set($field, $value, '', $options);
		}
	}

	/**
	 * Sets the error handler type.
	 *
	 * @param integer $errorHandler
	 */
	public function setErrorHandler($errorHandler)
	{
		$this->_errorHandler = intval($errorHandler);
	}

	/**
	* Gets data related to this object regardless of where it is defined (new or old).
	*
	* @param string $field Field name
	* @param string $tableName Table name, if empty loops through tables until first match
	*
	* @return mixed Returns null if the specified field could not be found.
	*/
	public function get($field, $tableName = '')
	{
		$tables = $this->_getTableList($tableName);

		foreach ($tables AS $tableName)
		{
			if (isset($this->_newData[$tableName]) && array_key_exists($field, $this->_newData[$tableName]))
			{
				return $this->_newData[$tableName][$field];
			}
			else if (isset($this->_existingData[$tableName]) && array_key_exists($field, $this->_existingData[$tableName]))
			{
				return $this->_existingData[$tableName][$field];
			}
		}

		return null;
	}

	/**
	* Explictly gets data from the new data array. Returns null if not set.
	*
	* @param string $field Field name
	* @param string $tableName Table name, if empty loops through tables until first match
	* @param boolean $exists
	*
	* @return mixed
	*/
	public function getNew($field, $tableName = '', &$exists = null)
	{
		$tables = $this->_getTableList($tableName);

		foreach ($tables AS $tableName)
		{
			if (isset($this->_newData[$tableName]) && array_key_exists($field, $this->_newData[$tableName]))
			{
				$exists = true;
				return $this->_newData[$tableName][$field];
			}
		}

		$exists = false;
		return null;
	}

	/**
	 * Returns true if changes have been made to this data.
	 *
	 * @return boolean
	 */
	public function hasChanges()
	{
		return (!empty($this->_newData));
	}

	/**
	* Determines whether the named field has been changed/set.
	*
	* @param string $field Field name
	* @param string $tableName Table name, if empty loops through tables until first match
	*
	* @return boolean
	*/
	public function isChanged($field, $tableName = '')
	{
		$this->getNew($field, $tableName, $exists);
		return $exists;
	}

	/**
	* Explictly gets data from the existing data array. Returns null if not set.
	*
	* @param string $field Field name
	* @param string $tableName Table name, if empty loops through tables until first match
	* @param boolean $exists
	*
	* @return mixed
	*/
	public function getExisting($field, $tableName = '', &$exists = null)
	{
		$tables = $this->_getTableList($tableName);

		foreach ($tables AS $tableName)
		{
			if (isset($this->_existingData[$tableName]) && array_key_exists($field, $this->_existingData[$tableName]))
			{
				$exists = true;
				return $this->_existingData[$tableName][$field];
			}
		}

		$exists = false;
		return null;
	}

	/**
	* Merges the new and existing data to show a "final" view of the data. This will
	* generally reflect what is in the database.
	*
	* If no table is specified, all data will be flattened into one array. New data
	* takes priority over existing data, and earlier tables in the list take priority
	* over later tables.
	*
	* @param string $tableName
	*
	* @return array
	*/
	public function getMergedData($tableName = '')
	{
		$tables = $this->_getTableList($tableName);

		$output = [];

		// loop through all tables and use the first value that comes up for a field.
		// this assumes that the more "primary" tables come first
		foreach ($tables AS $tableName)
		{
			if (isset($this->_newData[$tableName]))
			{
				$output += $this->_newData[$tableName];
			}

			if (isset($this->_existingData[$tableName]))
			{
				$output += $this->_existingData[$tableName];
			}
		}

		return $output;
	}

	/**
	 * Gets the existing data only into a flat view of the data.
	 *
	 * @param string $tableName
	 *
	 * @return array
	 */
	public function getMergedExistingData($tableName = '')
	{
		$tables = $this->_getTableList($tableName);

		$output = [];

		// loop through all tables and use the first value that comes up for a field.
		// this assumes that the more "primary" tables come first
		foreach ($tables AS $tableName)
		{
			if (isset($this->_existingData[$tableName]))
			{
				$output += $this->_existingData[$tableName];
			}
		}

		return $output;
	}

	/**
	* Trigger an error with the specified string (or phrase object). Depending on the
	* type of error handler chosen, this may throw an exception.
	*
	* @param string|\XF\Phrase $error Error message
	* @param string|false $errorKey Unique key for the error. Used to prevent multiple errors from the same field being displayed.
	* @param boolean $specificError If true and error key specified, overwrites an existing error with this name
	*/
	public function error($error, $errorKey = false, $specificError = true)
	{
		if ($errorKey !== false)
		{
			if ($specificError || !isset($this->_errors[strval($errorKey)]))
			{
				$this->_errors[strval($errorKey)] = $error;
			}
		}
		else
		{
			$this->_errors[] = $error;
		}

		if ($this->_errorHandler == self::ERROR_EXCEPTION)
		{
			throw new \XF\PrintableException($error);
		}
	}

	/**
	 * Merges a set of errors into this DW.
	 *
	 * @param array $errors
	 */
	public function mergeErrors(array $errors)
	{
		foreach ($errors AS $errorKey => $error)
		{
			if (is_integer($errorKey))
			{
				$errorKey = false;
			}
			$this->error($error, $errorKey);
		}
	}

	/**
	* Gets all errors that have been triggered. If {@link preSave()} has been
	* called and this returns an empty array, a {@link save()} should go through.
	*
	* @return array
	*/
	public function getErrors()
	{
		return $this->_errors;
	}

	/**
	 * Determines if this DW has errors.
	 *
	 * @return boolean
	 */
	public function hasErrors()
	{
		return (count($this->_errors) > 0);
	}

	/**
	 * Gets the specific named error if reported.
	 *
	 * @param string $errorKey
	 *
	 * @return string
	 */
	public function getError($errorKey)
	{
		return (isset($this->_errors[$errorKey]) ? $this->_errors[$errorKey] : false);
	}

	/**
	 * Gets all new data that has been set to date
	 *
	 * @return array
	 */
	public function getNewData()
	{
		return $this->_newData;
	}

	/**
	 * Gets all new data that has been set to date, merged through all tables.
	 *
	 * @param string $tableName
	 */
	public function getMergedNewData($tableName = '')
	{
		$tables = $this->_getTableList($tableName);

		$output = [];

		// loop through all tables and use the first value that comes up for a field.
		// this assumes that the more "primary" tables come first
		foreach ($tables AS $tableName)
		{
			if (isset($this->_newData[$tableName]))
			{
				$output += $this->_newData[$tableName];
			}
		}

		return $output;
	}

	/**
	 * Updates the version ID field for the specified record. Th add-on ID
	 * is determined from a previously set value, so it should not be updated
	 * after this is called.
	 *
	 * @param string $versionIdField Name of the field the version ID will be written to
	 * @param string $versionStringField Name of the field where the version string will be written to
	 * @param string $addOnIdField Name of the field the add-on ID is kept in
	 *
	 * @return integer Version ID to use
	 */
	public function updateVersionId($versionIdField = 'version_id', $versionStringField = 'version_string', $addOnIdField = 'addon_id')
	{
		return 0;
	}

	/**
	* Determines if we have errors that would prevent a save. If the silent error
	* handler is used, errors simply trigger a return of true (yes, we have errors);
	* otherwise, errors trigger an exception. Generally, if errors are to be handled,
	* save shouldn't be called.
	*
	* @return boolean True if there are errors
	*/
	protected function _haveErrorsPreventSave()
	{
		if ($this->_errors)
		{
			if ($this->_errorHandler == self::ERROR_SILENT)
			{
				return true;
			}
			else
			{
				throw new \XF\PrintableException($this->_errors);
			}
		}

		return false;
	}

	/**
	* External handler to get the SQL update/delete condition for this data. If there is no
	* existing data, this always returns an empty string, otherwise it proxies
	* to {@link _getUpdateCondition()}, which is an abstract function.
	*
	* @param string $tableName Name of the table to fetch the condition for
	*
	* @return string
	*/
	public function getUpdateCondition($tableName)
	{
		if (!$this->_existingData || !isset($this->_existingData[$tableName]))
		{
			return '';
		}
		else
		{
			return $this->_getUpdateCondition($tableName);
		}
	}

	/**
	* Saves the changes to the data. This either updates an existing record or inserts
	* a new one, depending whether {@link setExistingData} was called.
	*
	* After a successful insert with an auto_increment field, the value will be stored
	* into the new data array with a field marked as autoIncrement. Use {@link get()}
	* to get access to it.
	*
	* @return boolean True on success
	*/
	public function save()
	{
		$this->preSave();

		if ($this->_haveErrorsPreventSave())
		{
			return false;
		}

		if (!$this->_newData)
		{
			// nothing to change; error if insert, act as if everything is ok on update
			if (!$this->_existingData)
			{
				throw new \LogicException('Cannot save item when no data has been set');
			}
		}

		$this->_beginDbTransaction();

		try
		{
			$this->_save();

			if (!$this->_importMode)
			{
				$this->_postSave();
				$this->_performLanguageRebuild();
			}
		}
		catch (\Exception $e)
		{
			$this->_rollbackDbTransaction();
			throw $e;
		}

		$this->_commitDbTransaction();

		if (!$this->_importMode)
		{
			$this->_postSaveAfterTransaction();
		}

		return true;
	}

	/**
	* Public facing handler for final verification before a save. Any verification
	* or complex defaults may be set by this.
	*
	* It is generally not advisable to override this function. If you just want to
	* add pre-save behaviors, override {@link _preSave()}.
	*/
	public function preSave()
	{
		if ($this->_preSaveCalled)
		{
			return;
		}

		$this->_preSaveDefaults();
		if (!$this->_importMode)
		{
			$this->_preSave();

			if (!$this->_existingData)
			{
				$this->_resolveDefaultsAndRequiredFieldsForInsert();
			}
			else
			{
				$this->_checkRequiredFieldsForUpdate();
			}
		}
		else if (!$this->_existingData)
		{
			$this->_resolveDefaultsAndRequiredFieldsForInsert(false);
		}

		$this->_preSaveCalled = true;
	}

	/**
	 * Method designed to be overridden by child classes to add pre-save behaviors that
	 * set dynamic defaults. This is still called in import mode.
	 */
	protected function _preSaveDefaults()
	{
	}

	/**
	* Method designed to be overridden by child classes to add pre-save behaviors. This
	* is not callbed in import mode.
	*/
	protected function _preSave()
	{
	}

	/**
	* This resolves unset fields to their defaults (if available) and the checks
	* for required fields that are unset or empty. If a required field is not
	* set properly, an error is thrown.
	*
	* @param boolean $checkRequired If true, checks required fields
	*/
	protected function _resolveDefaultsAndRequiredFieldsForInsert($checkRequired = true)
	{
		foreach ($this->_fields AS $tableName => $fields)
		{
		 	foreach ($fields AS $field => $fieldData)
			{
				// when default is an array it references another column in an earlier table
				if ((!isset($this->_newData[$tableName]) || !array_key_exists($field, $this->_newData[$tableName]))
					&& array_key_exists('default', $fieldData)
					&& !is_array($fieldData['default'])
				)
				{
					$this->_setInternal($tableName, $field, $fieldData['default']);
				}

				if ($checkRequired
					&& !empty($fieldData['required'])
					&& (!isset($this->_newData[$tableName])
						|| !array_key_exists($field, $this->_newData[$tableName])
						|| $this->_newData[$tableName][$field] === ''
					)
				)
				{
					// references an externalID, we can't resolve this
					if (isset($fieldData['default']) && is_array($fieldData['default']))
					{
						continue;
					}
					$this->_triggerRequiredFieldError($tableName, $field);
				}
			}
		}
	}

	/**
	 * Checks that required field values are still maintained on updates.
	 */
	protected function _checkRequiredFieldsForUpdate()
	{
		foreach ($this->_fields AS $tableName => $fields)
		{
		 	foreach ($fields AS $field => $fieldData)
			{
				if (!isset($this->_newData[$tableName]) || !array_key_exists($field, $this->_newData[$tableName]))
				{
					continue;
				}

				if (!empty($fieldData['required']) && $this->_newData[$tableName][$field] === '')
				{
					$this->_triggerRequiredFieldError($tableName, $field);
				}
			}
		}
	}

	/**
	 * Triggers the error for a required field not being specified.
	 *
	 * @param string $tableName
	 * @param string $field
	 */
	protected function _triggerRequiredFieldError($tableName, $field)
	{
		$errorText = $this->_getSpecificRequiredFieldErrorText($tableName, $field);
		if ($errorText)
		{
			$this->error($errorText, $field, false);
		}
		else if (isset($this->_fields[$tableName][$field]['required']) && is_string($this->_fields[$tableName][$field]['required']))
		{
			$this->error(\XF::phrase($this->_fields[$tableName][$field]['required']), $field, false);
		}
		else
		{

			$this->error(\XF::phrase('please_enter_value_for_required_field_x', ['field' => $field]), $field, false);
		}
	}

	/**
	 * Gets the error text (or phrase) for a specific required field. Concrete DWs
	 * may override this to get nicer error messages for specific fields.
	 *
	 * @param string $tableName
	 * @param string $field
	 *
	 * @return false|string|\XF\Phrase
	 */
	protected function _getSpecificRequiredFieldErrorText($tableName, $field)
	{
		return false;
	}

	/**
	 * Returns true if this DW is updating a record, rather than inserting one.
	 *
	 * @return boolean
	 */
	public function isUpdate()
	{
		return !empty($this->_existingData);
	}

	/**
	 * Returns true if this DW is inserting a record, rather than updating one.
	 *
	 * @return boolean
	 */
	public function isInsert()
	{
		return !$this->isUpdate();
	}

	/**
	* Internal save handler. Deals with both updates and inserts.
	*/
	protected function _save()
	{
		if ($this->isUpdate())
		{
			$this->_update();
		}
		else
		{
			$this->_insert();
		}
	}

	/**
	* Internal save handler.
	*/
	protected function _insert()
	{
		foreach ($this->_getTableList() AS $tableName)
		{
			$this->_db->insert($tableName, $this->_newData[$tableName]);
			$this->_setAutoIncrementValue($this->_db->lastInsertId(), $tableName, true);
		}
	}

	/**
	* Internal update handler.
	*/
	protected function _update()
	{
		foreach ($this->_getTableList() AS $tableName)
		{
			if (!($update = $this->getUpdateCondition($tableName)) || empty($this->_newData[$tableName]))
			{
				continue;
			}
			$this->_db->update($tableName, $this->_newData[$tableName], $update);
		}
	}

	/**
	* Sets the auto-increment value to the auto increment field, if there is one.
	* If the ID passed in is 0, nothing will be updated.
	*
	* @param integer $insertId Auto-increment value from 0.
	* @param string $tableName Name of the table set the auto increment field in
	* @param bool $updateAll Update all tables with cross referenced auto increment fields
	*
	* @return boolean True on update
	*/
	protected function _setAutoIncrementValue($insertId, $tableName, $updateAll = false)
	{
		if (!$insertId)
		{
			return false;
		}

		$field = $this->_getAutoIncrementField($tableName);
		if (!$field)
		{
			return false;
		}

		$insertId += 0;

		$this->_newData[$tableName][$field] = $insertId;
		if ($updateAll)
		{
			foreach ($this->_fields AS $table => $fieldData)
			{
				foreach ($fieldData AS $fieldType)
				{
					if (!isset($fieldType['default']) || !is_array($fieldType['default']))
					{
						continue;
					}

					if ($fieldType['default'][0] == $tableName && !$this->get($field, $table))
					{
						$this->_newData[$table][$field] = $insertId;
					}
				}
			}
		}
		return true;
	}

	/**
	* Finds the auto-increment field in the list of fields. This field is simply
	* the first field tagged with the autoIncrement flag.
	*
	* @param string $tableName Name of the table to obtain the field for
	*
	* @return string|false Name of the field if found or false
	*/
	protected function _getAutoIncrementField($tableName)
	{
		foreach ($this->_fields[$tableName] AS $field => $fieldData)
		{
			if (!empty($fieldData['autoIncrement']))
			{
				return $field;
			}
		}

		return false;
	}

	/**
	* Finds the first table in the _fields array
	*
	* @return string|false Name of the table or false
	*/
	protected function _getPrimaryTable()
	{
		$tables = array_keys($this->_fields);
		return (isset($tables[0]) ? $tables[0] : false);
	}

	/**
	 * Gets an array with a list of all table names or, if provided, only
	 * the specified table.
	 *
	 * @param string $tableName Optional table to limit results to.
	 *
	 * @return array
	 */
	protected function _getTableList($tableName = '')
	{
		return ($tableName ? [$tableName] : array_keys($this->_fields));
	}

	/**
	 * @return array
	 */
	public function getTables()
	{
		return array_keys($this->_fields);
	}

	/**
	* Method designed to be overridden by child classes to add post-save behaviors.
	* This is not called in import mode.
	*/
	protected function _postSave()
	{
	}

	/**
	 * Method designed to be overridden by child classes to add post-save behaviors
	 * that should be run after the transaction is committed. This is not called in
	 * import mode.
	 */
	protected function _postSaveAfterTransaction()
	{

	}

	/**
	* Deletes the record that was selected by a call to {@link setExistingData()}.
	*
	* @return boolean True on success
	*/
	public function delete()
	{
		$this->preDelete();

		if ($this->_haveErrorsPreventSave())
		{
			return false;
		}

		$this->_beginDbTransaction();

		$this->_delete();
		$this->_postDelete();
		$this->_performLanguageRebuild();

		$this->_commitDbTransaction();

		return true;
	}

	/**
	* Public facing handler for final verification before a delete. Any verification
	* or complex defaults may be set by this.
	*
	* It is generally not advisable to override this function. If you just want to
	* add pre-delete behaviors, override {@link _preDelete()}.
	*/
	public function preDelete()
	{
		if ($this->_preDeleteCalled)
		{
			return;
		}

		$this->_preDelete();

		$this->_preDeleteCalled = true;
	}

	/**
	* Method designed to be overridden by child classes to add pre-delete behaviors.
	*/
	protected function _preDelete()
	{
	}

	/**
	* Internal handler for a delete action. Actually does the delete.
	*/
	protected function _delete()
	{
		foreach ($this->_getTableList() AS $tableName)
		{
			$condition = $this->getUpdateCondition($tableName);
			if (!$condition)
			{
				throw new \LogicException('Cannot delete data without a condition');
			}
			$this->_db->delete($tableName, $condition);
		}
	}

	/**
	* Method designed to be overridden by child classes to add pre-delete behaviors.
	*/
	protected function _postDelete()
	{
	}

	/**
	 * Inserts or updates a master (language 0) phrase. Errors will be silently ignored.
	 *
	 * @param string $title
	 * @param string $text
	 * @param string $addOnId
	 * @param array $extra
	 */
	protected function _insertOrUpdateMasterPhrase($title, $text, $addOnId = '', array $extra = [])
	{
		$this->_getPhraseModel()->insertOrUpdateMasterPhrase($title, $text, $addOnId, $extra,  [
			XenForo_DataWriter_Phrase::OPTION_REBUILD_LANGUAGE_CACHE => false,
			XenForo_DataWriter_Phrase::OPTION_RECOMPILE_TEMPLATE => false
		]);
		$this->_triggerLanguageRebuild = true;
	}

	/**
	 * Deletes the named master phrase if it exists.
	 *
	 * @param string $title
	 */
	protected function _deleteMasterPhrase($title)
	{
		$this->_getPhraseModel()->deleteMasterPhrase($title,  [
			XenForo_DataWriter_Phrase::OPTION_REBUILD_LANGUAGE_CACHE => false,
			XenForo_DataWriter_Phrase::OPTION_RECOMPILE_TEMPLATE => false
		]);
		$this->_triggerLanguageRebuild = true;
	}

	/**
	 * Renames a master phrase. If you get a conflict, it will
	 * be silently ignored.
	 *
	 * @param string $oldName
	 * @param string $newName
	 */
	protected function _renameMasterPhrase($oldName, $newName)
	{
		$this->_getPhraseModel()->renameMasterPhrase($oldName, $newName, [
			//XenForo_DataWriter_Phrase::OPTION_REBUILD_LANGUAGE_CACHE => false,
			XenForo_DataWriter_Phrase::OPTION_RECOMPILE_TEMPLATE => false
		]);
		$this->_triggerLanguageRebuild = true;
	}

	/**
	 * Changes the add-on for a phrase. This should be called when you have phrases
	 * that are attached to a DW entity that itself is attached to an add-on.
	 *
	 * @param string $phraseName
	 * @param string $addOnId
	 */
	protected function _changePhraseAddOn($phraseName, $addOnId)
	{
		$this->_getPhraseModel()->changePhraseAddOn($phraseName, $addOnId);
	}

	/**
	* Starts a new database transaction.
	*/
	protected function _beginDbTransaction()
	{
		$this->_db->beginTransaction();
		return true;
	}

	/**
	* Commits a new database transaction.
	*/
	protected function _commitDbTransaction()
	{
		$this->_db->commit();
		return true;
	}

	/**
	* Rolls a database transaction back.
	*/
	protected function _rollbackDbTransaction()
	{
		$this->_db->rollback();
		return true;
	}

	/**
	 * Performs a language rebuild if needed.
	 */
	protected function _performLanguageRebuild()
	{
	}

	/**
	 * Gets the specified model object from the cache. If it does not exist,
	 * it will be instantiated.
	 *
	 * @param string $class Name of the class to load
	 *
	 * @return \XF\Legacy\Model
	 */
	public function getModelFromCache($class)
	{
		if (!isset(self::$_modelCache[$class]))
		{
			self::$_modelCache[$class] = \XF\Legacy\Model::create($class);
		}

		return self::$_modelCache[$class];
	}

	/**
	 * Returns the user model
	 *
	 * @return \XF\Legacy\Model\User
	 */
	protected function _getUserModel()
	{
		return $this->getModelFromCache('XenForo_Model_User');
	}

	/**
	 * @return \XF\Legacy\Model\UserIgnore
	 */
	protected function _getUserIgnoreModel()
	{
		return $this->getModelFromCache('XenForo_Model_UserIgnore');
	}

	/**
	 * Returns the phrase model
	 *
	 * @return \XF\Legacy\Model\Phrase
	 */
	protected function _getPhraseModel()
	{
		return $this->getModelFromCache('XenForo_Model_Phrase');
	}

	/**
	 * Returns the news feed model
	 *
	 * @return \XF\Legacy\Model\NewsFeed
	 */
	protected function _getNewsFeedModel()
	{
		return $this->getModelFromCache('XenForo_Model_NewsFeed');
	}

	/**
	 * Returns the alert model
	 *
	 * @return \XF\Legacy\Model\Alert
	 */
	protected function _getAlertModel()
	{
		return $this->getModelFromCache('XenForo_Model_Alert');
	}

	/**
	 * Returns the admin search model
	 *
	 * @return \XF\Legacy\Model\AdminSearch
	 */
	protected function _getAdminSearchModel()
	{
		return $this->getModelFromCache('XenForo_Model_AdminSearch');
	}

	/**
	* Factory method to get the named data writer. The class must exist or be autoloadable
	* or an exception will be thrown.
	*
	* @param string     $class Class to load
	* @param mixed      $errorHandler Error handler. See {@link ERROR_EXCEPTION} and related.
	* @param array|null $inject Dependencies to inject. See {@link __construct()}.
	*
	* @throws \InvalidArgumentException
	*
	* @return \XF\Legacy\DataWriter
	*/
	public static function create($class, $errorHandler = self::ERROR_ARRAY, array $inject = null)
	{
		if (strpos($class, ':'))
		{
			$createClass = \XF::stringToClass($class, '%s\Legacy\DataWriter\%s');
		}
		else if (strpos($class, '\\'))
		{
			$createClass = $class;
		}
		else
		{
			$createClass = $class;
			$createClass = preg_replace('/^XenForo_/', 'XF\\', $createClass);
			$createClass = preg_replace('/(_|\\\\)DataWriter_/', '\\Legacy\\DataWriter\\', $createClass);
			$createClass = str_replace('_', '\\', $createClass);
		}

		return new $createClass($errorHandler, $inject);
	}
}