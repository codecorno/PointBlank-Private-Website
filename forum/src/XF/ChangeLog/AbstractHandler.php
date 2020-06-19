<?php

namespace XF\ChangeLog;

use http\Exception\InvalidArgumentException;
use XF\Entity\ChangeLog;
use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Finder;
use XF\Mvc\Entity\FinderExpression;

abstract class AbstractHandler
{
	protected $contentType;

	protected $labelMap;
	protected $formatterMap;
	protected $prefixHandlers;
	protected $customFieldData = [];

	abstract protected function getLabelMap();
	abstract protected function getFormatterMap();

	protected function getProtectedFields()
	{
		return [];
	}

	public function isFieldProtected($field)
	{
		$protectedFields = $this->getProtectedFields();
		return (isset($protectedFields[$field]) && $protectedFields[$field] === true);
	}

	public function __construct($contentType)
	{
		$this->contentType = $contentType;
	}

	protected function getPrefixHandlers()
	{
		return [];
	}

	public function getDefaultEditUserId(Entity $entity)
	{
		return \XF::visitor()->user_id;
	}

	public function getDisplayEntry(ChangeLog $log)
	{
		$label = $this->getFieldLabel($log->field);
		$old = $this->getFormattedValue($log->field, $log->old_value);
		$new = $this->getFormattedValue($log->field, $log->new_value);

		$displayEntry = new DisplayEntry($label, $old, $new);

		if ($this->isFieldProtected($log->field))
		{
			$displayEntry->setIsProtected(true);
		}

		return $displayEntry;
	}

	public function getFieldLabel($field)
	{
		if (!is_array($this->labelMap))
		{
			$this->labelMap = $this->getLabelMap();
		}

		if (isset($this->labelMap[$field]))
		{
			return \XF::phrase($this->labelMap[$field]);
		}

		if (preg_match('/^([^:]+):(.+)$/', $field, $match))
		{
			if (!is_array($this->prefixHandlers))
			{
				$this->prefixHandlers = $this->getPrefixHandlers();
			}

			if (isset($this->prefixHandlers[$match[1]]))
			{
				$prefixLabelHandler = $this->prefixHandlers[$match[1]][0];
				$value = $this->$prefixLabelHandler($match[2]);
				return $value === null ? $field : $value;
			}
		}

		return \XF::phrase($field);
	}

	public function getFormattedValue($field, $value)
	{
		if (!is_array($this->formatterMap))
		{
			$this->formatterMap = $this->getFormatterMap();
		}

		if (isset($this->formatterMap[$field]))
		{
			$formatter = $this->formatterMap[$field];
			return $this->$formatter($value);
		}

		if (preg_match('/^([^:]+):(.+)$/', $field, $match))
		{
			if (!is_array($this->prefixHandlers))
			{
				$this->prefixHandlers = $this->getPrefixHandlers();
			}

			if (isset($this->prefixHandlers[$match[1]]))
			{
				$prefixValueHandler = $this->prefixHandlers[$match[1]][1];
				$formattedValue = $this->$prefixValueHandler($match[2], $value);
				return $formattedValue === null ? $value : $formattedValue;
			}
		}

		return $value;
	}

	protected function formatDate($value)
	{
		return $value ? \XF::language()->date($value) : '';
	}

	protected function formatDateTime($value)
	{
		return $value ? \XF::language()->dateTime($value) : '';
	}

	protected function formatYesNo($value)
	{
		return \XF::phrase($value ? 'yes' : 'no');
	}

	/**
	 * @param string $shortName
	 *
	 * @return \XF\Entity\AbstractField[]
	 */
	protected function getCustomFieldData($shortName)
	{
		if (!isset($this->customFieldData[$shortName]))
		{
			/** @var \XF\Repository\AbstractField $fieldRepo */
			$fieldRepo = \XF::repository($shortName);
			$this->customFieldData[$shortName] = $fieldRepo->findFieldsForList()->fetch()->toArray();
		}

		return $this->customFieldData[$shortName];
	}

	protected function labelCustomFieldGeneric($shortName, $field)
	{
		$customFields = $this->getCustomFieldData($shortName);
		return isset($customFields[$field]) ? $customFields[$field]->getTitle() : null;
	}

	protected function formatCustomFieldGeneric($shortName, $field, $value)
	{
		$customFields = $this->getCustomFieldData($shortName);
		if (!isset($customFields[$field]))
		{
			return null;
		}

		$fieldEnt = $customFields[$field];

		if ($fieldEnt->isChoiceField())
		{
			if (substr($value, 0, 2) == 'a:')
			{
				// pre-2.0 version stored serialized
				$choices = \XF\Util\Php::safeUnserialize($value);
				if (is_array($choices))
				{
					$choices = array_keys($choices);
				}
				else
				{
					$choices = [];
				}
			}
			else
			{
				$choices = explode(',', $value);
			}

			$output = [];
			foreach ($choices AS $choice)
			{
				$output[] = $fieldEnt->getChoiceLabel($choice);
			}

			return implode(', ', $output);
		}

		return $value;
	}

	public function getEntityWith()
	{
		return [];
	}

	public function getContent($id)
	{
		return \XF::app()->findByContentType($this->contentType, $id, $this->getEntityWith());
	}

	public function getContentType()
	{
		return $this->contentType;
	}

	const OLD_VALUE = 'old_value';
	const NEW_VALUE = 'new_value';

	public function getFinderCondition($field, $value, $column)
	{
		switch ($column)
		{
			case self::OLD_VALUE:
			case self::NEW_VALUE:
				break;

			default:
				throw new InvalidArgumentException("Column must be one of 'old_value' or 'new_value'.");
		}

		return [['field', '=', $field], $this->getFieldFinderCondition($field, $value, $column)];
	}

	/**
	 * Returns a Finder condition to search the changelog for a particular $value of $field in $column
	 * You should adapt this for special cases where stored data may not reflect thec search parameter
	 * (like storing a user_id when the search is for a username)
	 *
	 * @param        $field
	 * @param        $value
	 * @param        $column
	 *
	 * @return array
	 */
	protected function getFieldFinderCondition($field, $value, $column)
	{
		return [$column, '=', $value];
	}
}