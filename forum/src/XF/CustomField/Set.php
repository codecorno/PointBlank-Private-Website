<?php

namespace XF\CustomField;

use XF\Mvc\Entity\Entity;

class Set implements \ArrayAccess, \IteratorAggregate, \Countable
{
	/**
	 * @var Entity
	 */
	protected $entity;

	/**
	 * @var DefinitionSet
	 */
	protected $definitionSet;

	/**
	 * @var string
	 */
	protected $cacheField;

	/**
	 * @var array
	 */
	protected $fieldsSet;

	public function __construct(DefinitionSet $definitionSet, Entity $entity, $cacheField = 'custom_fields')
	{
		$this->entity = $entity;
		$this->definitionSet = $definitionSet;
		$this->cacheField = $cacheField;
		$this->fieldsSet = $entity->getValue($cacheField);
	}

	public function getFormattedValue($key)
	{
		$field = $this->getField($key);

		return $field->getFormattedValue($this[$key]);
	}

	/**
	 * @param $key
	 *
	 * @return Definition
	 */
	public function getField($key)
	{
		return $this->getDefinitionSet()->{$key};
	}

	/**
	 * @return DefinitionSet
	 */
	public function getDefinitionSet()
	{
		return $this->definitionSet;
	}

	/**
	 * @param string $field
	 *
	 * @return Definition
	 */
	public function getDefinition($field)
	{
		return $this->getDefinitionSet()->get($field);
	}

	public function setDefinitionSet(DefinitionSet $definitionSet)
	{
		$this->definitionSet = $definitionSet;

		return $this->definitionSet;
	}

	public function __get($key)
	{
		return $this->offsetGet($key);
	}

	public function offsetGet($offset)
	{
		return $this->offsetExists($offset) ? $this->fieldsSet[$offset] : null;
	}

	public function getFieldValues()
	{
		return $this->fieldsSet;
	}

	public function bulkSet(array $fieldValues, array $fieldsShown = null, $editMode = 'user', $ignoreInvalid = false)
	{
		if ($fieldsShown === null)
		{
			// assume we have all keys
			$fieldsShown = array_keys($fieldValues);
		}

		foreach ($fieldsShown AS $fieldId)
		{
			$fieldValue = $this->prepareFieldValue($fieldValues, $fieldId);
			$this->set($fieldId, $fieldValue, $editMode, $ignoreInvalid);
		}
	}

	public function prepareFieldValue(array $fieldValues, $fieldId)
	{
		if (isset($fieldValues[$fieldId . '_html']))
		{
			// Value is likely HTML from rich text editor convert to BB code
			$html = $fieldValues[$fieldId . '_html'];
			$bbCode = \XF\Html\Renderer\BbCode::renderFromHtml($html);
			return \XF::cleanString($bbCode);
		}
		else if (!isset($fieldValues[$fieldId]))
		{
			// allow it to try and set a null value to fail required/valid checks
			return null;
		}
		else
		{
			return $fieldValues[$fieldId];
		}
	}

	public function set($fieldId, $newValue, $editMode = 'user', $ignoreInvalid = false)
	{
		$definitions = $this->getDefinitionSet();
		if (!isset($definitions[$fieldId]))
		{
			if ($ignoreInvalid)
			{
				return false;
			}

			throw new \LogicException("Unknown field '$fieldId'");
		}

		$existingValue = $this[$fieldId];

		/** @var Definition $field */
		$field = $definitions[$fieldId];

		if (!$field->isEditable($existingValue, $editMode))
		{
			if (!$ignoreInvalid)
			{
				$this->entity->error(\XF::phrase('field_x_is_not_editable', ['field' => $field->title]), "custom_field_$fieldId");
			}
			return false;
		}

		if ($field->type_group == 'multiple')
		{
			// checkboxes or multi-select, value is an array.
			$value = [];
			if (is_string($newValue))
			{
				$value = [$newValue];
			}
			else if (is_array($newValue))
			{
				$value = $newValue;
			}
		}
		else
		{
			if (is_array($newValue))
			{
				$value = count($newValue) ? strval(reset($newValue)) : '';
			}
			else
			{
				$value = strval($newValue);
			}

			if ($field->field_type == 'bbcode')
			{
				/** @var \XF\Service\Message\Preparer $messagePreparer */
				$messagePreparer = \XF::app()->service('XF:Message\Preparer', 'custom_field');
				$messagePreparer->setConstraint('allowEmpty', true);
				$value = $messagePreparer->prepare($value);
				if (($editMode == 'user' || $editMode == 'moderator_user') && !$messagePreparer->isValid())
				{
					if (!$ignoreInvalid)
					{
						$error = $messagePreparer->getFirstError();
						$this->entity->error("{$field->title}: $error", "custom_field_$fieldId");
					}
					return false;
				}
			}
		}

		// TODO: Considerations for import related value setting.

		$valid = $field->isValid($value, $error, $existingValue);
		if (!$valid)
		{
			if (!$ignoreInvalid)
			{
				$this->entity->error("{$field->title}: $error", "custom_field_$fieldId");
			}
			return false;
		}

		if ($field->isRequired($editMode) && ($value === '' || $value === []))
		{
			if (!$ignoreInvalid)
			{
				$this->entity->error(\XF::phraseDeferred('please_enter_value_for_all_required_fields'), "custom_field_$fieldId");
			}
			return false;
		}

		$this->fieldsSet[$fieldId] = $value;
		$this->entity->set($this->cacheField, $this->fieldsSet);

		return true;
	}

	public function removeFieldValue($fieldId)
	{
		if (array_key_exists($fieldId, $this->fieldsSet))
		{
			unset($this->fieldsSet[$fieldId]);
			$this->entity->set($this->cacheField, $this->fieldsSet);
		}
	}

	public function getFieldValue($fieldId)
	{
		return isset($this->fieldsSet[$fieldId]) ? $this->fieldsSet[$fieldId] : null;
	}

	public function getNamedFieldValues($fields)
	{
		$output = [];
		foreach ((array)$fields AS $fieldId)
		{
			if (isset($this->fieldsSet[$fieldId]))
			{
				$output[$fieldId] = $this->fieldsSet[$fieldId];
			}
		}

		return $output;
	}

	public function __set($key, $value)
	{
		$this->set($key, $value);
	}

	public function offsetSet($offset, $value)
	{
		$this->set($offset, $value);
	}

	public function __isset($key)
	{
		return $this->offsetExists($key);
	}

	public function offsetExists($offset)
	{
		return isset($this->fieldsSet[$offset]);
	}

	public function offsetUnset($offset)
	{
		throw new \BadMethodCallException("Cannot un-set offsets in field set.");
	}

	public function getIterator()
	{
		return new \ArrayIterator($this->fieldsSet);
	}

	public function count()
	{
		return count($this->fieldsSet);
	}
}