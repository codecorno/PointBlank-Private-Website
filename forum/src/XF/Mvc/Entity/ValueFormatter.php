<?php

namespace XF\Mvc\Entity;

class ValueFormatter
{
	public function decodeValueFromSource($type, $value)
	{
		if ($value === null)
		{
			return $value;
		}

		switch ($type)
		{
			case Entity::SERIALIZED:
				return @unserialize($value);

			case Entity::SERIALIZED_ARRAY:
				$result = @unserialize($value);
				if (!is_array($result))
				{
					$result = [];
				}
				return $result;

			case Entity::JSON:
				return @json_decode($value, true);

			case Entity::JSON_ARRAY:
				$result = @json_decode($value, true);
				if (!is_array($result))
				{
					$result = [];
				}
				return $result;

			case Entity::LIST_LINES:
				return $value === '' ? [] : preg_split('/\r?\n/', $value);

			case Entity::LIST_COMMA:
				return $value === '' ? [] : explode(',', $value);

			case Entity::BOOL:
				return $value ? true : false;

			default:
				return $value;
		}
	}

	public function decodeValueFromSourceExtended($type, $value, array $columnOptions = [])
	{
		if ($value === null)
		{
			return $value;
		}

		$value = $this->decodeValueFromSource($type, $value);

		if (
			($type == Entity::LIST_COMMA || $type == Entity::LIST_LINES)
			&& !empty($columnOptions['list']['type'])
		)
		{
			switch ($columnOptions['list']['type'])
			{
				case 'int':
				case 'uint':
				case 'posint':
					$value = array_map('intval', $value);
			}
		}

		return $value;
	}

	public function encodeValueForSource($type, $value)
	{
		if ($value === null)
		{
			// null is always valid (if allowed by the column)
			return $value;
		}

		switch ($type)
		{
			case Entity::BOOL:
				return $value ? 1 : 0;

			case Entity::SERIALIZED:
			case Entity::SERIALIZED_ARRAY:
				return serialize($value);

			case Entity::JSON:
			case Entity::JSON_ARRAY:
				return json_encode($value, JSON_PARTIAL_OUTPUT_ON_ERROR);

			case Entity::LIST_LINES:
				return implode("\n", $value);

			case Entity::LIST_COMMA:
				return implode(',', $value);

			default:
				return $value;
		}
	}
	
	public function castValueToType($value, $type, array $columnOptions = [])
	{
		if ($value === null && !empty($columnOptions['nullable']))
		{
			return $value;
		}

		switch ($type)
		{
			case Entity::BINARY:
			case Entity::STR:
				if (is_scalar($value) || (is_object($value) && is_callable([$value, '__toString'])))
				{
					$value = strval($value);
					if ($type == Entity::STR && $value && !preg_match('/./su', $value))
					{
						throw new \InvalidArgumentException("Received invalid UTF-8 for string column");
					}
					return $value;
				}
				throw new \InvalidArgumentException("Attempted to convert " . gettype($value) . " to string/binary");

			case Entity::BOOL:
				return ($value ? true : false);

			case Entity::INT:
			case Entity::UINT:
				if (is_scalar($value))
				{
					return intval($value);
				}
				throw new \InvalidArgumentException("Attempted to convert " . gettype($value) . " to integer");

			case Entity::FLOAT:
				if (is_scalar($value))
				{
					return strval(floatval($value)) + 0;
				}
				throw new \InvalidArgumentException("Attempted to convert " . gettype($value) . " to float");

			case Entity::SERIALIZED:
				return $value;

			case Entity::JSON:
			case Entity::JSON_ARRAY:
				if ($type == Entity::JSON_ARRAY && !is_array($value))
				{
					throw new \InvalidArgumentException("Attempted to convert " . gettype($value) . " to JSON array");
				}

				$jsonOptions = !empty($columnOptions['forced']) ? JSON_PARTIAL_OUTPUT_ON_ERROR : 0;
				$testEncode = json_encode($value, $jsonOptions);
				if ($testEncode === false)
				{
					throw new \InvalidArgumentException("Failed to convert to JSON. Error: " . json_last_error_msg());
				}

				return $value;

			case Entity::SERIALIZED_ARRAY:
				if (is_array($value))
				{
					return $value;
				}
				throw new \InvalidArgumentException("Attempted to convert " . gettype($value) . " to serialized array");

			case Entity::LIST_LINES:
				if (is_array($value))
				{
					return array_values($value);
				}
				throw new \InvalidArgumentException("Attempted to convert " . gettype($value) . " to line-separated list");

			case Entity::LIST_COMMA:
				if (is_array($value))
				{
					return array_values($value);
				}
				throw new \InvalidArgumentException("Attempted to convert " . gettype($value) . " to comma-separated list");

			default:
				throw new \InvalidArgumentException("Unknown cast to type: $type");
		}
	}
	
	public function applyValueConstraints(
		&$value, $type, array $columnOptions, &$error = null, $forceToConstraint = false
	)
	{
		if ($value === null)
		{
			return true;
		}

		switch ($type)
		{
			case Entity::INT:
				if (!array_key_exists('min', $columnOptions))
				{
					$columnOptions['min'] = -2147483648;
				}
				if (!array_key_exists('max', $columnOptions))
				{
					$columnOptions['max'] = 2147483647;
				}
				break;

			case Entity::UINT:
				if (!array_key_exists('min', $columnOptions))
				{
					$columnOptions['min'] = 0;
				}
				if (!array_key_exists('max', $columnOptions))
				{
					$columnOptions['max'] = 4294967295;
				}
		}

		if (!empty($columnOptions['forced']))
		{
			$forceToConstraint = true;
		}

		switch ($type)
		{
			case Entity::BINARY:
			case Entity::STR:
				if (isset($columnOptions['maxLength']))
				{
					$strlen = ($type == Entity::STR ? 'utf8_strlen' : 'strlen');
					$substr = ($type == Entity::STR ? 'utf8_substr' : 'substr');
					if ($strlen($value) > $columnOptions['maxLength'])
					{
						if ($forceToConstraint)
						{
							$value = $substr($value, 0, $columnOptions['maxLength']);
						}
						else
						{
							$error = \XF::phrase('please_enter_value_using_x_characters_or_fewer',
								['count' => $columnOptions['maxLength']]
							);

							return false;
						}
					}
				}

				if (!empty($columnOptions['match']))
				{
					if (!$this->verifyMatch($value, $columnOptions['match'], $error))
					{
						if ($forceToConstraint)
						{
							$error = null;
							$value = '';
						}
						else
						{
							return false;
						}
					}
				}
				break;

			case Entity::INT:
			case Entity::UINT:
			case Entity::FLOAT:
				if (isset($columnOptions['min']) && $value < $columnOptions['min'])
				{
					if ($forceToConstraint)
					{
						$value = $columnOptions['min'];
					}
					else
					{
						$error = \XF::phrase('please_enter_number_that_is_at_least_x',
							['min' => $columnOptions['min']]
						);

						return false;
					}
				}
				if (isset($columnOptions['max']) && $value > $columnOptions['max'])
				{
					if ($forceToConstraint)
					{
						$value = $columnOptions['max'];
					}
					else
					{
						$error = \XF::phrase('please_enter_number_that_is_no_more_than_x',
							['max' => $columnOptions['max']]
						);

						return false;
					}
				}
				break;

			case Entity::LIST_COMMA:
			case Entity::LIST_LINES:
				if (!empty($columnOptions['list']))
				{
					$list = $columnOptions['list'];
					if (!empty($list['type']))
					{
						foreach ($value AS $k => &$v)
						{
							switch ($list['type'])
							{
								case 'int':
									$v = intval($v);
									break;

								case 'uint':
									$v = intval($v);
									if ($v < 0)
									{
										unset($value[$k]);
									}
									break;

								case 'posint':
									$v = intval($v);
									if ($v <= 0)
									{
										unset($value[$k]);
									}
									break;

								case 'str':
									$v = strval($v);
									if ($v === '')
									{
										unset($value[$k]);
									}
									break;
							}
						}
					}
					if (!empty($list['unique']))
					{
						$value = array_unique($value);
					}
					if (!empty($list['sort']))
					{
						sort($value, $list['sort'] === true ? SORT_REGULAR : $list['sort']);
					}
				}
				break;
		}

		if (isset($columnOptions['allowedValues']) && !in_array($value, $columnOptions['allowedValues']))
		{
			$error = \XF::phrase('please_enter_valid_value');

			return false;
		}

		return true;
	}

	protected function verifyMatch(&$value, $matchData, &$error = null)
	{
		$phraseKey = null;

		if (is_array($matchData))
		{
			$match = $matchData[0];
			if (isset($matchData[1]))
			{
				$phraseKey = $matchData[1];
			}

			list($match, $phraseKey) = $this->prepareMatch($match, $phraseKey);
		}
		else
		{
			list($match, $phraseKey) = $this->prepareMatch($matchData);
		}

		if ($match == 'url' || $match == 'url_empty')
		{
			/** @var \XF\Validator\Url $urlValidator */
			$urlValidator = \XF::app()->validator('Url');
			$value = $urlValidator->coerceValue($value);

			if ($match == 'url_empty')
			{
				$urlValidator->setOption('allow_empty', true);
			}

			if (!$urlValidator->isValid($value, $phraseKey))
			{
				$error = \XF::phrase('please_enter_valid_url');

				return false;
			}
		}
		else if ($match == 'email' || $match == 'email_empty')
		{
			/** \XF\Validator\Email $emailValidator */
			$emailValidator = \XF::app()->validator('Email');
			$value = $emailValidator->coerceValue($value);

			if ($match == 'email_empty')
			{
				$emailValidator->setOption('allow_empty', true);
			}

			if (!$emailValidator->isValid($value))
			{
				$error = \XF::phrase('please_enter_valid_email');

				return false;
			}
		}
		else if (!preg_match($match, $value))
		{
			if (is_string($phraseKey))
			{
				$error = \XF::phrase($phraseKey);
			}
			else if ($phraseKey === null)
			{
				$error = \XF::phrase('please_enter_value_that_matches_requirements_for_this_field');
			}
			else
			{
				$error = $phraseKey;
			}

			return false;
		}

		return true;
	}

	protected function prepareMatch($match, $phraseKey = null)
	{
		$phrase = null;

		switch ($match)
		{
			case 'alphanumeric':

				$match = '/^[a-z0-9_]*$/i';
				$phrase = \XF::phraseDeferred('please_enter_title_using_only_alphanumeric_underscore');
				break;

			case 'alphanumeric_hyphen':

				$match = '/^[a-z0-9_-]*$/i';
				$phrase = \XF::phraseDeferred('please_enter_title_using_only_alphanumeric_dash_underscore');
				break;

			case 'alphanumeric_dot':

				$match = '/^[a-z0-9_.]*$/i';
				$phrase = \XF::phraseDeferred('please_enter_title_using_only_alphanumeric_dot_underscore');
				break;

			default:
				break;
		}

		return [$match, ($phraseKey !== null) ? $phraseKey : $phrase];
	}
}