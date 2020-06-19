<?php

namespace XF\Behavior;

use XF\Mvc\Entity\Behavior;
use XF\Mvc\Entity\Entity;

class ChangeLoggable extends Behavior
{
	protected function getDefaultConfig()
	{
		return [
			'optIn' => false,
			'contentType' => null,
			'contentIdColumn' => null
		];
	}

	protected function getDefaultOptions()
	{
		return [
			'enabled' => true
		];
	}

	protected function verifyConfig()
	{
		if (!$this->config['contentType'])
		{
			$contentType = $this->contentType();
			if ($contentType)
			{
				$this->config['contentType'] = $contentType;
			}
			else
			{
				throw new \LogicException("Configuration must provide a contentType value");
			}
		}
	}

	public function postSave()
	{
		if (!$this->isLoggable())
		{
			$this->resetEnabled();
			return;
		}

		/** @var \XF\Repository\ChangeLog $changeLogRepo */
		$changeLogRepo = $this->repository('XF:ChangeLog');

		$handler = $changeLogRepo->getChangeLogHandler($this->config['contentType'], true);
		$changes = [];

		foreach ($this->entity->structure()->columns AS $column => $definition)
		{
			if ($this->config['optIn'])
			{
				// must explicitly list columns to log
				if (empty($definition['changeLog']))
				{
					continue;
				}
			}
			else
			{
				// log by default if not specified
				if (isset($definition['changeLog']) && !$definition['changeLog'])
				{
					continue;
				}
			}

			if (!$this->entity->isChanged($column))
			{
				continue;
			}

			$oldValue = $this->entity->getExistingValue($column);
			$newValue = $this->entity->getValue($column);

			if (isset($definition['changeLog']) && $definition['changeLog'] == 'customFields')
			{
				$changes += $this->getCustomFieldChanges($oldValue, $newValue, $column);
				continue;
			}

			$oldValue = $this->formatValueForLog($oldValue, $definition['type']);
			$newValue = $this->formatValueForLog($newValue, $definition['type']);
			if ($oldValue === $newValue)
			{
				continue;
			}

			$changes[$column] = [$oldValue, $newValue];
		}

		// make sure the special case changes take priority if there's a name conflict, though you should probably
		// opt out special case columns.
		$changes = $this->getSpecialChangesFromEntity() + $changes;

		if ($changes)
		{
			$editUserId = $handler->getDefaultEditUserId($this->entity);
			$changeLogRepo->logChanges($this->config['contentType'], $this->getContentId(), $changes, $editUserId);
		}

		$this->resetEnabled();
	}


	protected function resetEnabled()
	{
		$this->options['enabled'] = true;
	}

	public function getCustomFieldChanges($oldSet, $newSet, $keyPrefix = 'custom_fields')
	{
		if (!is_array($oldSet))
		{
			$oldSet = [];
		}
		if (!is_array($newSet))
		{
			$newSet = [];
		}

		$changes = [];

		foreach ($oldSet AS $key => $oldValue)
		{
			if (!isset($newSet[$key]))
			{
				// removal, handled below
				continue;
			}

			$newValue = $newSet[$key];

			if ($oldValue !== $newValue)
			{
				if (is_array($oldValue))
				{
					$oldValue = implode(',', $oldValue);
				}
				if (is_array($newValue))
				{
					$newValue = implode(',', $newValue);
				}

				$changes["$keyPrefix:$key"] = [$oldValue, $newValue];
			}
		}

		foreach ($newSet AS $key => $newValue)
		{
			if (isset($oldSet[$key]))
			{
				// handled above
				continue;
			}

			if (is_array($newValue))
			{
				$newValue = implode(',', $newValue);
			}

			$changes["$keyPrefix:$key"] = ['', $newValue];
		}

		return $changes;
	}

	protected function isLoggable()
	{
		if (!$this->entity->isUpdate())
		{
			return false;
		}

		if (!$this->options['enabled'])
		{
			return false;
		}

		return true;
	}

	protected function formatValueForLog($value, $type)
	{
		if ($value === null)
		{
			return '';
		}

		switch ($type)
		{
			case Entity::INT:
			case Entity::UINT:
			case Entity::FLOAT:
			case Entity::STR:
			case Entity::BINARY:
				return strval($value);

			case Entity::BOOL:
				return $value ? '1' : '0';

			case Entity::LIST_COMMA:
				return implode(',', $value);

			case Entity::LIST_LINES:
				return implode("\n", $value);

			default:
				return ''; // these other cases need to be handled as special cases
		}
	}

	protected function getSpecialChangesFromEntity()
	{
		if (!is_callable([$this->entity, 'getChangeLogEntries']))
		{
			return [];
		}

		$changes = $this->entity->getChangeLogEntries($this);
		if (!is_array($changes))
		{
			throw new \LogicException("Entity getChangeLogEntries must return an array of [field] => [old, new]");
		}

		return $changes;
	}

	protected function getContentId()
	{
		$col = $this->config['contentIdColumn'];
		if ($col)
		{
			return $this->entity->getValue($col);
		}
		else
		{
			return $this->entity->getEntityId();
		}
	}
}