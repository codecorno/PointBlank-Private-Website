<?php

namespace XF\Behavior;

use XF\Mvc\Entity\Behavior;

class CustomFieldsHolder extends Behavior
{
	protected function getDefaultConfig()
	{
		return [
			'column' => 'custom_fields',
			'valueTable' => null,
			'keyColumn' => null,
			'checkForUpdates' => null,
			'getAllowedFields' => null
		];
	}

	protected function verifyConfig()
	{
		if (!$this->config['valueTable'])
		{
			throw new \LogicException("The valueTable configuration must be specified");
		}

		if (!$this->config['keyColumn'])
		{
			$key = $this->entity->structure()->primaryKey;
			if (is_string($key))
			{
				$this->config['keyColumn'] = $key;
			}
			else
			{
				throw new \LogicException("The keyColumn configuration could not be derived and must be specified");
			}
		}

		if ($this->config['checkForUpdates'] && !is_callable($this->config['getAllowedFields']))
		{
			throw new \LogicException("getAllowedFields must be specified to use checkForUpdates");
		}
	}

	public function preSave()
	{
		$entity = $this->entity;
		$checkForUpdates = $this->config['checkForUpdates'];

		if ($checkForUpdates)
		{
			if ($entity->isUpdate() && $entity->isChanged($checkForUpdates))
			{
				/** @var \XF\CustomField\Set $fieldSet */
				$fieldSet = $entity->get($this->config['column']);
				if (!($fieldSet instanceof \XF\CustomField\Set))
				{
					throw new \LogicException("Primary column must return a XF\CustomField\Set object (via a getter)");
				}

				$getAllowedFields = $this->config['getAllowedFields'];
				$allowedFields = $getAllowedFields($entity);

				foreach ($fieldSet->getFieldValues() AS $fieldId => $null)
				{
					if (!isset($allowedFields[$fieldId]))
					{
						$fieldSet->removeFieldValue($fieldId);
					}
				}
			}
		}
	}

	public function postSave()
	{
		$column = $this->config['column'];
		$entity = $this->entity;

		if ($entity->isChanged($column))
		{
			$newSet = $entity->getValue($column);
			$oldSet = $entity->isUpdate() ? $entity->getExistingValue($column) : [];

			if ($entity->isInsert() && !$newSet)
			{
				// nothing to do
				return;
			}

			$removedKeys = [];
			$replacements = [];

			foreach ($oldSet AS $key => $oldValue)
			{
				if (!isset($newSet[$key]))
				{
					$removedKeys[] = $key;
				}
				else
				{
					$newValue = $newSet[$key];
					if ($oldValue !== $newValue)
					{
						// updated value
						$replacements[$key] = $newValue;
					}
				}
			}

			foreach ($newSet AS $key => $newValue)
			{
				if (isset($oldSet[$key]))
				{
					// handled above
					continue;
				}

				// new value
				$replacements[$key] = $newValue;
			}

			$db = $entity->db();
			$keyColumn = $this->config['keyColumn'];
			$id = $entity->getEntityId();

			if ($removedKeys)
			{
				$db->delete(
					$this->config['valueTable'],
					"`$keyColumn` = ? AND field_id IN (" . $db->quote($removedKeys) . ")",
					$id
				);
			}

			if ($replacements)
			{
				$insert = [];
				foreach ($replacements AS $key => $value)
				{
					$insert[] = [
						$keyColumn => $id,
						'field_id' => $key,
						'field_value' => is_array($value) ? serialize($value) : $value
					];
				}

				$db->insertBulk($this->config['valueTable'], $insert, false, 'field_value = VALUES(field_value)');
			}
		}
	}

	public function postDelete()
	{
		$db = $this->entity->db();
		$keyColumn = $this->config['keyColumn'];

		$db->delete(
			$this->config['valueTable'],
			"`$keyColumn` = ?",
			$this->entity->getEntityId()
		);
	}
}