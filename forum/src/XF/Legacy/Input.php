<?php

namespace XF\Legacy;

class Input
{
	const STRING     = 'string';
	const NUM        = 'num';
	const UNUM       = 'unum';
	const INT        = 'int';
	const UINT       = 'uint';
	const FLOAT      = 'float';
	const BOOLEAN    = 'boolean';
	const BINARY     = 'binary';
	const ARRAY_SIMPLE = 'array_simple';
	const JSON_ARRAY = 'json_array';
	const DATE_TIME       = 'dateTime';

	/**
	* Default values for the input types
	*
	* @var array
	*/
	protected static $_DEFAULTS = [
		self::STRING    => '',
		self::NUM       => 0,
		self::UNUM      => 0,
		self::INT       => 0,
		self::UINT      => 0,
		self::FLOAT     => 0.0,
		self::BOOLEAN   => false,
		self::BINARY    => '',
		self::ARRAY_SIMPLE => [],
		self::JSON_ARRAY => [],
		self::DATE_TIME => 0
	];

	/**
	 * Map of from-to pairs of things to manipulate in strings.
	 *
	 * @var array
	 */
	protected static $_strClean = [
		// strip a bunch of control characters
		"\x00" => '', // null
		"\x01" => '', // start of heading
		"\x02" => '', // start of text
		"\x03" => '', // end of text
		"\x04" => '', // end of transmission
		"\x05" => '', // enquiry
		"\x06" => '', // ack
		"\x07" => '', // bell
		"\x08" => '', // backspace
		"\x0B" => '', // vertical tab
		"\x0C" => '', // form feed
		"\x0D" => '', // carriage returns, because jQuery does so in .val()
		"\x0E" => '', // shift out
		"\x0F" => '', // shift in
		"\x10" => '', // data link escape
		"\x11" => '', // device ctrl 1
		"\x12" => '', // device ctrl 2
		"\x13" => '', // device ctrl 3
		"\x14" => '', // device ctrl 4
		"\x15" => '', // negative ack
		"\x16" => '', // sync idle
		"\x17" => '', // end of transmission block
		"\x18" => '', // cancel
		"\x19" => '', // end of medium
		"\x1A" => '', // substitute
		"\x1B" => '', // escape
		"\x1C" => '', // file sep
		"\x1D" => '', // group sep
		"\x1E" => '', // record sep
		"\x1F" => '', // unit sep

		"\xC2\xA0" => ' ', // nbsp
		"\xC2\xAD" => '', // soft hyphen
		"\xE2\x80\x8B" => '', // zero width space
		"\xEF\xBB\xBF" => '' // zero width nbsp
	];

	/**
	* Cached cleaned variables. Key is the variable name as it was pulled
	*
	* @var array
	*/
	protected $_cleanedVariables = [];

	/**
	* The request object that variables will be read from. May be null
	* if source data is populated instead
	*
	* @var \XF\Http\Request|null
	*/
	protected $_request = null;

	/**
	 * Alternative to the request, data can come from an array.
	 *
	 * @var array|null
	 */
	protected $_sourceData = null;

	/**
	* Constructor
	*
	* @param \XF\Http\Request|array $source Source of input
	*/
	public function __construct($source)
	{
		if ($source instanceof \XF\Http\Request)
		{
			$this->_request = $source;
		}
		else if (is_array($source))
		{
			$this->_sourceData = $source;
		}
		else
		{
			throw new \InvalidArgumentException('Must pass an array or Zend_Controller_Request_Http object to XenForo_Input');
		}
	}

	/**
	* Filter an individual item
	*
	* @param string $variableName Name of the input variable
	* @param mixed $filterData Filter information, can be a single constant or an array containing a filter and options
	* @param array $options Filtering options
	*
	* @return mixed Value after being cleaned
	*/
	public function filterSingle($variableName, $filterData, array $options = [])
	{
		if (is_string($filterData))
		{
			$filters = [$filterData];
		}
		else if (is_array($filterData) && isset($filterData[0]))
		{
			$filters = is_array($filterData[0]) ? $filterData[0] : [$filterData[0]];

			if (isset($filterData[1]) && is_array($filterData[1]))
			{
				$options = array_merge($options, $filterData[1]);
			}
			else
			{
				unset($filterData[0]);
				$options = array_merge($options, $filterData);
			}
		}
		else
		{
			throw new \InvalidArgumentException("Invalid data passed to " . __CLASS__ . "::" . __METHOD__);
		}

		$firstFilter = reset($filters);

		if (isset($options['default']))
		{
			$defaultData = $options['default'];
		}
		else if (array_key_exists($firstFilter, self::$_DEFAULTS))
		{
			$defaultData = self::$_DEFAULTS[$firstFilter];
		}
		else
		{
			$defaultData = null;
		}

		if ($this->_request)
		{
			$data = $this->_request->get($variableName, null);
		}
		else
		{
			$data = (isset($this->_sourceData[$variableName]) ? $this->_sourceData[$variableName] : null);
		}

		if ($data === null)
		{
			$data = $defaultData;
		}

		foreach ($filters AS $filterName)
		{
			if (isset($options['array']))
			{
				if (is_array($data))
				{
					foreach (array_keys($data) AS $key)
					{
						$data[$key] = self::_doClean($filterName, $options, $data[$key], $defaultData);
					}
				}
				else
				{
					$data = [];
					break;
				}
			}
			else
			{
				$data = self::_doClean($filterName, $options, $data, $defaultData);
			}
		}

		$this->_cleanedVariables[$variableName] = $data;
		return $data;
	}

	protected static function _doClean($filterName, array $filterOptions, $data, $defaultData)
	{
		switch ($filterName)
		{
			case self::STRING:
				$data = is_scalar($data) ? strval($data) : $defaultData;
				if (strlen($data) && !preg_match('/./su', $data))
				{
					$data = $defaultData;
				}

				$data = self::cleanString($data);

				if (empty($filterOptions['noTrim']))
				{
					$data = trim($data);
				}
			break;

			case self::NUM:
				$data = strval($data) + 0;
			break;

			case self::UNUM:
				$data = strval($data) + 0;
				$data = ($data < 0) ? $defaultData : $data;
			break;

			case self::INT:
				$data = intval($data);
			break;

			case self::UINT:
				$data = ($data = intval($data)) < 0 ? $defaultData : $data;
			break;

			case self::FLOAT:
				$data = floatval($data);
			break;

			case self::BOOLEAN:
				if ($data === 'n' || $data == 'no' || $data === 'N')
				{
					$data = false;
				}
				else
				{
					$data = (boolean)$data;
				}
				break;

			case self::BINARY:
				$data = strval($data);
			break;

			case self::ARRAY_SIMPLE:
				if (!is_array($data))
				{
					$data = $defaultData;
				}
				$data = self::cleanStringArray($data);
			break;

			case self::JSON_ARRAY:
				if (is_string($data))
				{
					$data = json_decode($data, true);
				}
				if (!is_array($data))
				{
					$data = $defaultData;
				}
				$data = self::cleanStringArray($data);
			break;

			case self::DATE_TIME:
				if (!$data)
				{
					$data = 0;
				}
				else if (is_string($data))
				{
					$data = trim($data);

					if ($data === strval(intval($data)))
					{
						// data looks like an int, treat as timestamp
						$data = intval($data);
					}
					else
					{
						$tz = (XenForo_Visitor::hasInstance() ? XenForo_Locale::getDefaultTimeZone() : null);

						try
						{
							$date = new \DateTime($data, $tz);
							if (!empty($filterOptions['dayEnd']))
							{
								$date->setTime(23, 59, 59);
							}

							$data = $date->format('U');
						}
						catch (\Exception $e)
						{
							$data = 0;
						}
					}
				}

				if (!is_int($data))
				{
					$data = intval($data);
				}
			break;

			default:
				throw new \InvalidArgumentException("Unknown input type in " . __CLASS__ . "::" . __METHOD__);
		}

		return $data;
	}

	/**
	 * Cleans invalid characters out of a string, such as nulls, nbsp, \r, etc.
	 * Characters may not strictly be invalid, but can cause confusion/bugs.
	 *
	 * @param string $string
	 *
	 * @return string
	 */
	public static function cleanString($string)
	{
		// only cover the BMP as MySQL only supports that
		$string = preg_replace('/[\xF0-\xF7].../', '', $string);
		return strtr(strval($string), self::$_strClean);
	}

	/**
	 * Recursively run clean string on all strings in an array
	 *
	 * @param array $array
	 *
	 * @return array
	 */
	public static function cleanStringArray(array $array)
	{
		foreach ($array AS &$v)
		{
			if (is_string($v))
			{
				$v = self::cleanString($v);
			}
			else if (is_array($v))
			{
				$v = self::cleanStringArray($v);
			}
		}

		return $array;
	}

	/**
	* Filter an array of items
	*
	* @param array	$filters Key-value pairs with the value being in the format expected by filterSingle. {@link XenForo_Input::filterSingle()}
	*
	* @return array key-value pairs with the cleaned value
	*/
	public function filter(array $filters)
	{
		$data = [];
		foreach ($filters AS $variableName => $filterData)
		{
			$data[$variableName] = $this->filterSingle($variableName, $filterData);
		}

		return $data;
	}

	/**
	 * Statically filters a piece of data as the requested type.
	 *
	 * @param mixed $data
	 * @param mixed $filterName
	 * @param array $options
	 *
	 * @return mixed
	 */
	public static function rawFilter($data, $filterName, array $options = [])
	{
		return self::_doClean($filterName, $options, $data, self::$_DEFAULTS[$filterName]);
	}

	/**
	 * Returns true if the given key was included in the request at all.
	 *
	 * @param string $key
	 *
	 * @return boolean
	 */
	public function inRequest($key)
	{
		if ($this->_request)
		{
			return isset($this->_request->$key);
		}
		else
		{
			return isset($this->_sourceData[$key]);
		}
	}

	public function __get($key)
	{
		if (array_key_exists($key, $this->_cleanedVariables))
		{
			return $this->_cleanedVariables[$key];
		}
		else
		{
			return null;
		}
	}

	public function __isset($key)
	{
		return array_key_exists($key, $this->_cleanedVariables);
	}
}