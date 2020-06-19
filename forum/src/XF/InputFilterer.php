<?php

namespace XF;

class InputFilterer
{
	protected $stringCleaning = [
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
		"\x7F" => '', // delete

		// more UTF-8 control characters
		"\xC2\x80" => '',
		"\xC2\x81" => '',
		"\xC2\x82" => '',
		"\xC2\x83" => '',
		"\xC2\x84" => '',
		"\xC2\x85" => '',
		"\xC2\x86" => '',
		"\xC2\x87" => '',
		"\xC2\x88" => '',
		"\xC2\x89" => '',
		"\xC2\x8A" => '',
		"\xC2\x8B" => '',
		"\xC2\x8C" => '',
		"\xC2\x8D" => '',
		"\xC2\x8E" => '',
		"\xC2\x8F" => '',
		"\xC2\x90" => '',
		"\xC2\x91" => '',
		"\xC2\x92" => '',
		"\xC2\x93" => '',
		"\xC2\x94" => '',
		"\xC2\x95" => '',
		"\xC2\x96" => '',
		"\xC2\x97" => '',
		"\xC2\x98" => '',
		"\xC2\x99" => '',
		"\xC2\x9A" => '',
		"\xC2\x9B" => '',
		"\xC2\x9C" => '',
		"\xC2\x9D" => '',
		"\xC2\x9E" => '',
		"\xC2\x9F" => '',

		"\xC2\xA0" => ' ', // nbsp
		"\xC2\xAD" => '', // soft hyphen
		"\xE2\x80\x8B" => '', // zero width space
		"\xEF\xBB\xBF" => '' // zero width nbsp
	];

	protected $fullUnicode = false;

	public function __construct($fullUnicode = false)
	{
		$this->fullUnicode = $fullUnicode;
	}

	public function filterArray(array $array, array $filters)
	{
		$output = [];

		foreach ($filters AS $key => $type)
		{
			$value = array_key_exists($key, $array) ? $array[$key] : null;

			if (is_array($type))
			{
				if (!is_array($value))
				{
					$value = [];
				}
				$output[$key] = $this->filterArray($value, $type);
			}
			else
			{
				$output[$key] = $this->filter($value, $type);
			}
		}

		return $output;
	}

	public function filter($value, $type, array $options = null)
	{
		if (!is_array($options))
		{
			$optionParts = explode(',', $type);
			$type = array_shift($optionParts);
			$options = [];

			foreach ($optionParts AS $part)
			{
				$option = explode(':', trim($part), 2);
				if (!isset($option[1]))
				{
					$option[1] = true;
				}
				else
				{
					$option[1] = trim($option[1]);
				}
				$options[trim($option[0])] = $option[1];
			}
		}

		$type = trim(strtolower($type));

		if ($type && $type[0] === '?')
		{
			$nullable = true;
			$type = substr($type, 1);
		}
		else
		{
			$nullable = false;
		}

		if (!$type)
		{
			throw new \LogicException("No filter type provided");
		}

		if ($nullable && $value === null)
		{
			return null;
		}

		return $this->cleanInternal($value, $type, $options);
	}

	protected function cleanInternal($value, $type, array $options)
	{
		switch ($type)
		{
			case 'str':
			case 'string':
				if (is_scalar($value))
				{
					$value = str_replace("\r\n", "\n", strval($value));
					if (!preg_match('/^./us', $value))
					{
						$value = '';
					}
				}
				else
				{
					$value = '';
				}

				if (empty($options['no-clean']))
				{
					$value = $this->cleanString($value, false);
				}

				if (empty($options['no-trim']))
				{
					$value = trim($value);
				}
				break;

			case 'num':
				if (is_scalar($value))
				{
					$value = $this->normalizeDecimalSeparator($value);
					$value = strval(floatval($value)) + 0;
				}
				else
				{
					$value = 0;
				}
				break;

			case 'unum':
				if (is_scalar($value))
				{
					$value = $this->normalizeDecimalSeparator($value);
					$value = strval(floatval($value)) + 0;
					if ($value < 0)
					{
						$value = 0;
					}
				}
				else
				{
					$value = 0;
				}
				break;

			case 'int':
			case 'integer':
				if (is_scalar($value))
				{
					$value = intval($value);
				}
				else
				{
					$value = 0;
				}
				break;

			case 'uint':
			case 'unsigned':
				if (is_scalar($value))
				{
					$value = intval($value);
					if ($value < 0)
					{
						$value = 0;
					}
				}
				else
				{
					$value = 0;
				}
				break;

			case 'posint':
			case 'positive-integer':
				if (is_scalar($value))
				{
					$value = intval($value);
					if ($value < 1)
					{
						$value = 1;
					}
				}
				else
				{
					$value = 1;
				}
				break;

			case 'float':
				if (is_scalar($value))
				{
					$value = $this->normalizeDecimalSeparator($value);
					$value = floatval($value);
				}
				else
				{
					$value = 0;
				}
				break;

			case 'bool':
			case 'boolean':
				$value = (bool)$value;
				break;

			case 'array':
				if (!is_array($value))
				{
					$value = [];
				}

				if (empty($options['no-clean']))
				{
					$value = $this->cleanArrayStrings($value);
				}
				break;

			case 'json-array':
				if (is_string($value))
				{
					$value = json_decode($value, true);
					if (!is_array($value))
					{
						$value = [];
					}
				}
				else if (!is_array($value))
				{
					$value = [];
				}

				if (empty($options['no-clean']))
				{
					$value = $this->cleanArrayStrings($value);
				}
				break;

			case 'timeoffset':
				if (is_array($value) && isset($value['amount']) && isset($value['unit']))
				{
					$amount = is_scalar($value['amount']) ? intval($value['amount']) : 0;
					$unit = is_scalar($value['unit']) ? strtolower($value['unit']) : '';

					switch ($unit)
					{
						case 'seconds':
						case 'minutes':
						case 'hours':
						case 'days':
						case 'weeks':
						case 'months':
						case 'years':
							$valid = true;
							break;

						default:
							$valid = false;
					}

					if ($valid && $amount)
					{
						if ($amount > 0)
						{
							$amount = "+$amount";
						}
						$value = strtotime("$amount $unit", 0);
					}
					else
					{
						$value = 0;
					}
				}
				else
				{
					$value = 0;
				}
				break;

			case 'datetime':
				if (is_scalar($value) && $value)
				{
					$value = trim(strval($value));
					if (!$value || is_numeric($value))
					{
						$value = intval($value);
					}
					else
					{
						if (!empty($options['tz']))
						{
							$tz = new \DateTimeZone($options['tz']);
						}
						else
						{
							$tz = \XF::language()->getTimeZone();
						}

						try
						{
							$dt = new \DateTime($value, $tz);
							if (!empty($options['end']))
							{
								$dt->setTime(23, 59, 59);
							}

							$value = intval($dt->format('U'));
						}
						catch (\Exception $e)
						{
							// probably a formatting issue, ignore
							$value = 0;
						}
					}
				}
				else
				{
					$value = 0;
				}
				break;

			default:
				if (preg_match('/^array-(.*)$/', $type, $match))
				{
					if (!is_array($value))
					{
						$value = [];
					}
					else
					{
						foreach ($value AS &$innerValue)
						{
							$innerValue = $this->filter($innerValue, $match[1], $options);
						}
					}
				}
				else
				{
					throw new \InvalidArgumentException("Unknown filter type $type");
				}
		}

		return $value;
	}

	public function normalizeDecimalSeparator($value)
	{
		$decimalSep = \XF::language()['decimal_point'];

		if (strpos($value, $decimalSep) !== false && $decimalSep !== '.')
		{
			$value = str_replace($decimalSep, '.', $value);
		}

		return $value;
	}

	public function cleanString($string, $trim = true)
	{
		if (!$this->fullUnicode)
		{
			// only cover the BMP as MySQL only supports that unless opting into utf8mb4
			$string = preg_replace('/[\xF0-\xF7].../', '', $string);
		}

		$string = strtr(strval($string), $this->stringCleaning);
		if ($trim)
		{
			$string = trim($string);
		}

		return $string;
	}

	public function cleanArrayStrings(array $input, $trim = true)
	{
		foreach ($input AS &$v)
		{
			if (is_string($v))
			{
				$v = str_replace("\r\n", "\n", $v);
				if (!preg_match('/^./us', $v))
				{
					$v = '';
				}
				$v = $this->cleanString($v, $trim);
			}
			else if (is_array($v))
			{
				$v = $this->cleanArrayStrings($v, $trim);
			}
		}

		return $input;
	}

	public function getFullUnicode()
	{
		return $this->fullUnicode;
	}

	public function setFullUnicode($fullUnicode)
	{
		$this->fullUnicode = $fullUnicode;
	}

	public function getNewArrayFilterer(array $input = [])
	{
		return new InputFiltererArray($this, $input);
	}
}