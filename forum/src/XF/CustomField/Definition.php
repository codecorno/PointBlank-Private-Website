<?php

namespace XF\CustomField;

class Definition implements \ArrayAccess
{
	protected $field;

	public function __construct(array $definition)
	{
		$this->field = $definition;
	}

	public function getFormattedValue($value)
	{
		if ($value === '' || $value === null)
		{
			return '';
		}

		$choice = null;

		switch ($this->type_group)
		{
			case 'single':
				$choice = $value;
				$value = isset($this->field_choices[$value]) ? $this->field_choices[$value] : '';
				break;

			case 'multiple':
				foreach ($value AS $key => &$phrase)
				{
					$phrase = $this->field_choices[$key];
				}
				break;

			case 'rich_text':
				$value = \XF::app()->bbCode()->render($value, 'html', 'custom_field:' . $this->field['field_id'], null);
				break;

			case 'text':
			default:
				if ($this->display_template)
				{
					$value = nl2br(htmlspecialchars(\XF::app()->stringFormatter()->censorText($value)));
				}
				else
				{
					$value = \XF::app()->stringFormatter()->convertStructuredTextToHtml($value);
				}
				break;
		}

		if ($this->display_template)
		{
			if (is_array($value))
			{
				foreach ($value AS $choice => &$thisValue)
				{
					$thisValue = $this->translateValue($thisValue, $choice);
				}
			}
			else
			{
				$value = $this->translateValue($value, $choice);
			}
		}

		if (is_array($value))
		{
			$value = implode(\XF::language()->comma_separator, $value);
		}

		return $value;
	}

	protected function translateValue($value, $choice = null)
	{
		$value = strtr($this->display_template, [
			'{$fieldId}' => $this->field_id,
			'{$value}' => $value,
			'{$valueUrl}' => urlencode($value),
			'{$choice}' => $choice
		]);

		return $value;
	}

	public function hasValue($value)
	{
		return ($value !== null && $value !== '' && $value !== []);
	}

	public function isRequired($editMode = 'user')
	{
		if ($this->required)
		{
			return ($editMode == 'user' || $editMode == 'moderator_user');
		}
		else
		{
			return false;
		}
	}

	public function isEditable($value, $editMode = 'user')
	{
		if ($editMode == 'admin')
		{
			// always editable
			return true;
		}

		if (isset($this->editable_user_group_ids))
		{
			$visitor = \XF::visitor();
			$canEdit = false;

			foreach ($this->editable_user_group_ids AS $userGroupId)
			{
				if ($userGroupId == -1 || $visitor->isMemberOf($userGroupId))
				{
					$canEdit = true;
					break;
				}
			}

			if (!$canEdit)
			{
				return false;
			}

			// fall through to other checks -- will normally return true
		}

		switch ($editMode)
		{
			case 'user':
				if (isset($this->user_editable))
				{
					if (
						$this->user_editable == 'never'
						|| ($this->user_editable == 'once' && $this->hasValue($value))
					)
					{
						return false;
					}
				}

				return true;

			case 'moderator':
				if (isset($this->moderator_editable) && !$this->moderator_editable)
				{
					return false;
				}

				return true;

			case 'moderator_user':
				if (isset($this->moderator_editable) && $this->moderator_editable)
				{
					return true;
				}

				if (isset($this->user_editable))
				{
					if (
						$this->user_editable == 'never'
						|| ($this->user_editable == 'once' && $this->hasValue($value))
					)
					{
						return false;
					}
				}

				return true;

			default:
				return true;
		}
	}

	public function isValid(&$value, &$error, $existingValue)
	{
		$fieldValidatorMethod = '_validateFieldType' . \XF\Util\Php::camelCase($this->field_type);
		if (method_exists($this, $fieldValidatorMethod))
		{
			if ($this->$fieldValidatorMethod($value, $error, $existingValue) === false)
			{
				return false;
			}
		}

		return true;
	}

	protected function _validateFieldTypeTextbox(&$value, &$error, $existingValue)
	{
		$value = preg_replace('/\r?\n/', ' ', strval($value));

		return $this->_validateFieldTypeTextarea($value, $error, $existingValue);
	}

	protected function _validateFieldTypeTextarea(&$value, &$error, $existingValue)
	{
		$value = trim(strval($value));

		// skip length checks for types that tend to have fixed lengths
		if (!in_array($this->field_type, ['date', 'stars', 'color']))
		{
			if ($this->max_length && utf8_strlen($value) > $this->max_length)
			{
				$error = \XF::phraseDeferred('please_enter_value_using_x_characters_or_fewer', ['count' => $this->max_length]);
				return false;
			}
		}

		$matched = true;

		if ($value !== '')
		{
			$matchValidatorMethod = '_validateMatchType' . \XF\Util\Php::camelCase($this->match_type);
			if (method_exists($this, $matchValidatorMethod))
			{
				$matched = $this->$matchValidatorMethod($value, $error, $existingValue);
			}
		}

		if (!$matched)
		{
			if (!$error)
			{
				$error = \XF::phraseDeferred('please_enter_value_that_matches_required_format');
			}
			else if (is_string($error))
			{
				$error = \XF::phraseDeferred($error);
			}
			return false;
		}

		return true;
	}

	protected function _validateFieldTypeRadio(&$value, &$error, $existingValue)
	{
		return $this->_validateFieldTypeSelect($value, $error, $existingValue);
	}

	protected function _validateFieldTypeSelect(&$value, &$error, $existingValue)
	{
		$value = strval($value);

		if (!isset($this->field_choices[$value]))
		{
			$value = '';
		}

		return true;
	}

	protected function _validateFieldTypeCheckbox(&$value, &$error, $existingValue)
	{
		return $this->_validateFieldTypeMultiselect($value, $error, $existingValue);
	}

	protected function _validateFieldTypeMultiselect(&$value, &$error, $existingValue)
	{
		if (!is_array($value))
		{
			$value = [];
		}

		$newValue = [];

		foreach ($value AS $key => $choice)
		{
			$choice = strval($choice);
			if (isset($this->field_choices[$choice]))
			{
				$newValue[$choice] = $choice;
			}
		}

		$value = $newValue;

		return true;
	}

	protected function _validateMatchTypeNumber(&$value, &$error, $existingValue)
	{
		if (!empty($this->match_params['number_integer']))
		{
			$matched = preg_match('/^-?\d+$/', $value);
		}
		else
		{
			$matched = preg_match('/^[0-9]+(\.[0-9]+)?$/', $value);
		}

		if (
			isset($this->match_params['number_min'])
			&& $this->match_params['number_min'] !== ''
			&& $value < $this->match_params['number_min']
		)
		{
			$error  = \XF::phrase('please_enter_number_that_is_at_least_x', ['min' => $this->match_params['number_min']]);
			$matched = false;
		}

		if (
			isset($this->match_params['number_max'])
			&& $this->match_params['number_max'] !== ''
			&& $value > $this->match_params['number_max']
		)
		{
			$error  = \XF::phrase('please_enter_number_that_is_no_more_than_x', ['max' => $this->match_params['number_max']]);
			$matched = false;
		}

		return $matched;
	}

	protected function _validateMatchTypeAlphanumeric(&$value, &$error, $existingValue)
	{
		return preg_match('/^[a-z0-9_]+$/i', $value);
	}

	protected function _validateMatchTypeEmail(&$value, &$error, $existingValue)
	{
		$emailValidator = \XF::app()->validator('Email');
		return $emailValidator->isValid($value, $error);
	}

	protected function _validateMatchTypeUrl(&$value, &$error, $existingValue)
	{
		$urlValidator = \XF::app()->validator('Url');
		$value = $urlValidator->coerceValue($value);
		return $urlValidator->isValid($value, $error);
	}

	protected function _validateMatchTypeDate(&$value, &$error, $existingValue)
	{
		if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $value))
		{
			if (!empty($this->match_params['date_constraint']) && $value !== $existingValue)
			{
				$valueTime = date_create_from_format('Y-m-d', $value);
				$todayTime = date_create_from_format('Y-m-d', date('Y-m-d')); // TODO: language/timezone concerns

				switch ($this->match_params['date_constraint'])
				{
					case 'past':
						if ($valueTime >= $todayTime)
						{
							$error = \XF::phrase('please_enter_a_date_in_the_past');
							return false;
						}
						break;

					case 'future':
						if ($valueTime <= $todayTime)
						{
							$error = \XF::phrase('please_enter_a_date_in_the_future');
							return false;
						}
						break;
				}
			}

			return true;
		}

		return false;
	}

	protected function _validateMatchTypeColor(&$value, &$error, $existingValue)
	{
		return \XF\Util\Color::isValidColor($value);
	}

	protected function _validateMatchTypeRegex(&$value, &$error, $existingValue)
	{
		return preg_match('#' . str_replace('#', '\#', $this->match_params['regex']) . '#sU', $value);
	}

	protected function _validateMatchTypeCallback(&$value, &$error, $existingValue)
	{
		$callback = $this->match_params['callback_class'];
		$method = $this->match_params['callback_method'];

		return call_user_func_array([$callback, $method], [$this, &$value, &$error]);
	}

	protected function _validateMatchTypeValidator(&$value, &$error, $existingValue)
	{
		try
		{
			$validator = \XF::app()->validator($this->match_params['validator']);
		}
		catch (\LogicException $e)
		{
			$error = $e->getMessage();
			return false;
		}

		if (!$error)
		{
			$value = $validator->coerceValue($value);
			return $validator->isValid($value, $error);
		}
		else
		{
			return false;
		}
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
		switch ($offset)
		{
			case 'title':
			case 'description':
				return \XF::phrase($this->field[$offset]);

			case 'field_choices':
				$choices = $this->field['field_choices'];
				if ($choices)
				{
					array_walk($choices, function(&$value) { $value = \XF::phrase($value); });
				}
				return $choices;

			default:
				return isset($this->field[$offset]) ? $this->field[$offset] : null;
		}
	}

	public function offsetSet($offset, $value)
	{
		throw new \BadMethodCallException("Cannot set offsets in definition.");
	}

	public function offsetExists($offset)
	{
		return array_key_exists($offset, $this->field);
	}

	public function offsetUnset($offset)
	{
		throw new \BadMethodCallException("Cannot un-set offsets in definition.");
	}
}