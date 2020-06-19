<?php

namespace XF\Import\Data;

use XF\Mvc\Entity\Entity;

class EntityEmulator
{
	/**
	 * @var AbstractData
	 */
	protected $handler;

	/**
	 * @var \XF\Mvc\Entity\Structure
	 */
	protected $structure;

	/**
	 * @var \XF\Mvc\Entity\ValueFormatter
	 */
	protected $valueFormatter;

	protected $primaryKey;

	protected $entityData = [];

	protected $oldId = null;

	/**
	 * Convert text to UTF8 on set()
	 */
	const CONVERT_UTF8 = 'convertUtf8';

	/**
	 * Force type constraints on set()
	 */
	const FORCE_CONSTRAINT = 'forceConstraint';

	/**
	 * Convert HTML entities to raw values on set()
	 */
	const UNHTML_ENTITIES = 'unHtmlEntities';

	public function __construct(
		AbstractData $handler, \XF\Mvc\Entity\Structure $structure, \XF\Mvc\Entity\ValueFormatter $valueFormatter
	)
	{
		$this->handler = $handler;
		$this->structure = $structure;
		$this->valueFormatter = $valueFormatter;

		$primaryKey = $structure->primaryKey;
		if (is_array($primaryKey) && count($primaryKey) == 1)
		{
			$primaryKey = reset($primaryKey);
		}
		else if (is_array($primaryKey))
		{
			throw new \LogicException("Compound primary keys are not supported by the entity data importer. A custom version must be implemented.");
		}
		$this->primaryKey = $primaryKey;
	}

	protected function isStringy($value)
	{
		return is_string($value) || (is_object($value) && is_callable([$value, '__toString']));
	}

	protected function isValidUtf8($string)
	{
		return preg_match('/./su', $string);
	}

	public function set($field, $value, array $options = [])
	{
		$options = array_replace([
			self::CONVERT_UTF8     => true,
			self::FORCE_CONSTRAINT => true,
			self::UNHTML_ENTITIES  => false
		], $options);

		$columns = $this->structure->columns;
		$column = $columns[$field];

		if (isset($columns[$field]))
		{
			if (is_null($value) && empty($column['nullable']))
			{
				$value = $this->getValidEmptyValue($column['type']);
			}
			else if ($column['type'] == Entity::STR && empty($column['nullable']))
			{
				// TODO: this does mean we can't have leading whitespace at all, but perhaps that's not a bad thing
				$value = ltrim(strval($value));
			}
		}
		else
		{
			throw new \InvalidArgumentException("Unknown column '$field'");
		}

		// Convert CRLF to LF
		if (is_string($value))
		{
			$value = str_replace("\r\n", "\n", $value);
		}

		$vf = $this->valueFormatter;
		$originalValue = $value;

		if ($options[self::CONVERT_UTF8])
		{
			if ($this->isStringy($value))
			{
				if (!$this->isValidUtf8($value))
				{
					$value = $this->handler->convertToUtf8(strval($value), null, $options[self::UNHTML_ENTITIES]);
				}
				else
				{
					if ($options[self::UNHTML_ENTITIES])
					{
						$value = html_entity_decode(strval($value), ENT_QUOTES, 'UTF-8');
					}
					$value = $this->handler->stripExtendedUtf8IfNeeded($value);
				}
			}

			try
			{
				$value = $vf->castValueToType($value, $column['type'], $column);
			}
			catch (\Exception $e)
			{
				if (is_string($originalValue) && !$this->isValidUtf8($originalValue))
				{
					$value = utf8_bad_replace($originalValue);
				}
				$value = $this->handler->stripExtendedUtf8IfNeeded($value);
			}
		}
		else if ($options[self::UNHTML_ENTITIES] && $this->isStringy($value))
		{
			$value = html_entity_decode(strval($value), ENT_QUOTES, 'UTF-8');
			$value = $this->handler->stripExtendedUtf8IfNeeded($value);
		}

		try
		{
			$value = $vf->castValueToType($value, $column['type'], $column);
		}
		catch (\Exception $e)
		{
			throw new \InvalidArgumentException($e->getMessage() . " [$field]", $e->getCode(), $e);
		}

		if (!$vf->applyValueConstraints($value, $column['type'], $column, $error, $options[self::FORCE_CONSTRAINT]))
		{
			throw new \InvalidArgumentException("Constraint error for $field: " . $error);
		}

		$this->entityData[$field] = $value;

		return true;
	}

	protected function getValidEmptyValue($columnType)
	{
		switch ($columnType)
		{
			case Entity::INT:
			case Entity::UINT:
			case Entity::FLOAT:
				return 0;

			case Entity::STR:
			case Entity::BINARY:
				return '';

			case Entity::SERIALIZED_ARRAY:
			case Entity::JSON:
			case Entity::JSON_ARRAY:
			case Entity::LIST_LINES:
			case Entity::LIST_COMMA:
				return [];

			case Entity::BOOL:
				return false;

			case Entity::SERIALIZED:
			default:
				return null;
		}
	}

	/**
	 * This method is primarily for avoiding double-UTF8 conversion.
	 *
	 * Only use this if you are certain that the field is valid, the value is valid UTF8 etc.
	 * If in doubt, use set().
	 *
	 * @param $field
	 * @param $value
	 */
	public function setDirect($field, $value)
	{
		$this->entityData[$field] = $value;
	}

	public function setPrimaryKey($value, array $options = [])
	{
		return $this->set($this->primaryKey, $value, $options);
	}

	public function get($field)
	{
		if (array_key_exists($field, $this->entityData))
		{
			return $this->entityData[$field];
		}

		$columns = $this->structure->columns;
		if (!isset($columns[$field]))
		{
			throw new \InvalidArgumentException("Unknown column '$field'");
		}

		$column = $columns[$field];
		if (array_key_exists('default', $column))
		{
			return $column['default'];
		}

		return null;
	}

	public function remove($field)
	{
		if (is_array($field))
		{
			foreach ($field AS $f)
			{
				unset($this->entityData[$f]);
			}
		}
		else
		{
			unset($this->entityData[$field]);
		}
	}

	public function exists($column)
	{
		return isset($this->structure->columns[$column]);
	}

	public function getEntityData()
	{
		return $this->entityData;
	}

	public function getPrimaryKey()
	{
		return $this->primaryKey;
	}

	public function getWriteData($forInsert = true)
	{
		$data = $this->entityData;
		$writeData = [];

		foreach ($this->structure->columns AS $id => $column)
		{
			if (array_key_exists($id, $data))
			{
				$value = $data[$id];

				if (\XF::$debugMode && !empty($column['required']) && ($value === '' || $value === []))
				{
					throw new \LogicException(sprintf(
						"Column '%s' is required and has an empty value while importing %s with id = %s",
						$id,
						$this->structure->shortName,
						$this->oldId
					));
				}
			}
			else if (!$forInsert)
			{
				// for an update, so don't use any default values
				continue;
			}
			else if (array_key_exists('default', $column))
			{
				$value = $column['default'];
			}
			else if (!empty($column['nullable']))
			{
				$value = null;
			}
			else if (!empty($column['required']))
			{
				throw new \LogicException("Column '$id' is required and does not have a value");
			}
			else
			{
				continue;
			}

			$writeData[$id] = $this->valueFormatter->encodeValueForSource($column['type'], $value);
		}

		return $writeData;
	}

	public function insert($oldId, \XF\Db\AbstractAdapter $db)
	{
		$structure = $this->structure;
		$primaryKeyStructure = $structure->columns[$this->primaryKey];

		$this->oldId = $oldId;
		$this->setupForInsert($oldId);

		$writeData = $this->getWriteData();
		$db->insert($structure->table, $writeData);

		if (!empty($primaryKeyStructure['autoIncrement']))
		{
			$autoInc = $db->lastInsertId();
			$this->entityData[$this->primaryKey] = $autoInc;

			return $autoInc;
		}
		else
		{
			return $this->entityData[$this->primaryKey];
		}
	}

	public function setupForInsert($oldId)
	{
		$structure = $this->structure;
		$primaryKey = $this->primaryKey;
		$primaryKeyStructure = $structure->columns[$primaryKey];
		$isAutoInc = !empty($primaryKeyStructure['autoIncrement']);

		if (!array_key_exists($primaryKey, $this->entityData))
		{
			if ($this->handler->retainIds() && $oldId !== false)
			{
				$this->entityData[$primaryKey] = $oldId;
			}
			else if ($isAutoInc)
			{
				$this->entityData[$primaryKey] = null;
			}
		}

		if (!array_key_exists($primaryKey, $this->entityData))
		{
			throw new \LogicException("Primary key '$primaryKey' is not auto-increment, value must be provided");
		}
	}

	public function update($primaryKeyValue, \XF\Db\AbstractAdapter $db)
	{
		$this->oldId = $primaryKeyValue;

		$writeData = $this->getWriteData(false);

		if (array_key_exists($this->primaryKey, $writeData) && $writeData[$this->primaryKey] === null)
		{
			unset($writeData[$this->primaryKey]);
		}

		if (!$writeData)
		{
			return;
		}

		$db->update($this->structure->table, $writeData, "`$this->primaryKey` = ?", $primaryKeyValue);
	}

	public function logIp(\XF\Db\AbstractAdapter $db, $ip, $date, array $options = [])
	{
		if (!$ip)
		{
			return null;
		}

		$options = array_replace([
			'user_id' => null,
			'content_type' => null,
			'action' => 'insert',
			'ip_column' => 'ip_id'
		], $options);

		if ($options['user_id'] === null)
		{
			if (!$this->exists('user_id'))
			{
				throw new \LogicException("No user_id column found, pass user_id directly");
			}
			if ($this->user_id === null)
			{
				throw new \LogicException("No user_id found but with a null value, pass user_id directly");
			}

			$options['user_id'] = $this->user_id;
		}

		if ($options['ip_column'] && !$this->exists($options['ip_column']))
		{
			throw new \LogicException("IP column '$options[ip_column]' not found in structure");
		}

		if (!$options['content_type'])
		{
			if (!$this->structure->contentType)
			{
				throw new \LogicException("Entity does not define content_type, pass directly");
			}

			$options['content_type'] = $this->structure->contentType;
		}

		if (empty($this->entityData[$this->primaryKey]))
		{
			throw new \LogicException("No primary key value");
		}

		$ip = \XF\Util\Ip::convertIpStringToBinary($ip);
		if (!$ip)
		{
			return null;
		}

		$contentId = $this->entityData[$this->primaryKey];

		$db->insert('xf_ip', [
			'user_id' => $options['user_id'],
			'content_type' => $options['content_type'],
			'content_id' => $contentId,
			'action' => $options['action'],
			'ip' => $ip,
			'log_date' => $date
		]);
		$ipId = $db->lastInsertId();

		if ($options['ip_column'])
		{
			$db->update(
				$this->structure->table,
				[$options['ip_column'] => $ipId],
				"`$this->primaryKey` = ?",
				$contentId
			);
		}

		return $ipId;
	}

	public function insertStateRecord(\XF\Db\AbstractAdapter $db, $state, $contentDate, array $options = [])
	{
		$options = array_replace_recursive([
			'content_type' => null,
			'delete' => [
				'date' => null,
				'user_id' => 0,
				'username' => '',
				'reason' => ''
			]
		], $options);

		if (!$options['content_type'])
		{
			if (!$this->structure->contentType)
			{
				throw new \LogicException("Entity does not define content_type, pass directly");
			}

			$options['content_type'] = $this->structure->contentType;
		}

		if (empty($this->entityData[$this->primaryKey]))
		{
			throw new \LogicException("No primary key value");
		}

		if ($state == 'visible')
		{
			return;
		}

		$contentId = $this->entityData[$this->primaryKey];

		if ($state == 'moderated')
		{
			$db->insert('xf_approval_queue', [
				'content_type' => $options['content_type'],
				'content_id' => $contentId,
				'content_date' => $contentDate
			], false, 'content_date = VALUES(content_date)');
		}
		else if ($state == 'deleted')
		{
			$delete = $options['delete'];

			$db->insert('xf_deletion_log', [
				'content_type' => $options['content_type'],
				'content_id' => $contentId,
				'delete_date' => $delete['date'] ?: $contentDate,
				'delete_user_id' => $delete['user_id'],
				'delete_username' => $this->handler->convertToUtf8($delete['username']),
				'delete_reason' => $this->handler->convertToUtf8($delete['reason'])
			], false, 'delete_date = LEAST(delete_date, VALUES(delete_date))');
		}
	}

	public function __get($field)
	{
		return $this->get($field);
	}

	public function __set($field, $value)
	{
		$this->set($field, $value);
	}

	public function __unset($field)
	{
		$this->remove($field);
	}
}