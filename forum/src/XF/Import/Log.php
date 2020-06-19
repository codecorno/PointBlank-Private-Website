<?php

namespace XF\Import;

class Log
{
	protected $db;

	protected $table;

	protected $mapCache = [];

	protected $mappedTypes = [];

	public function __construct(\XF\Db\AbstractAdapter $db, $table)
	{
		$this->db = $db;
		$this->table = $table;
	}

	public function getTable()
	{
		return $this->table;
	}

	public function lookup($type, $oldIds)
	{
		if (!is_array($oldIds))
		{
			if ($oldIds === null && is_bool($oldIds))
			{
				throw new \InvalidArgumentException("Can't pass null/bool as old IDs");
			}

			$single = strval($oldIds);
			$oldIds = [$oldIds];
		}
		else
		{
			$single = null;
			$oldIds = array_unique($oldIds);
		}

		$oldIds = array_map('strval', $oldIds);

		$newIds = [];
		foreach ($oldIds AS $k => $oldId)
		{
			if (isset($this->mapCache[$type][$oldId]))
			{
				$newIds[$oldId] = $this->mapCache[$type][$oldId];
				unset($oldIds[$k]);
			}
		}

		if ($oldIds)
		{
			$db = $this->db;
			$results = $db->fetchPairs("
				SELECT old_id, new_id
				FROM `" . $this->table . "`
				WHERE content_type = ?
					AND old_id IN (" . $db->quote($oldIds) . ")
			", $type);
			foreach ($oldIds AS $oldId)
			{
				$value = isset($results[$oldId]) ? $results[$oldId] : false;
				$this->mapCache[$type][$oldId] = $value;
				$newIds[$oldId] = $value;
			}
		}

		if ($single === null)
		{
			return $newIds;
		}
		else
		{
			return $newIds[$single];
		}
	}

	public function lookupId($type, $id, $default = false)
	{
		$result = $this->lookup($type, $id);
		return $result === false ? $default : $result;
	}

	public function typeMap($type)
	{
		if (empty($this->mappedTypes[$type]))
		{
			$results = $this->db->fetchPairs("
				SELECT old_id, new_id
				FROM `" . $this->table . "`
				WHERE content_type = ?
			", $type);

			$this->mapCache[$type] = $results;
			$this->mappedTypes[$type] = true;
		}

		return $this->mapCache[$type];
	}

	public function log($type, $oldId, $newId)
	{
		$oldId = strval($oldId);
		$newId = strval($newId);

		$data = [
			'content_type' => $type,
			'old_id' => $oldId,
			'new_id' => $newId
		];

		$this->db->insert($this->table, $data, false, 'new_id = VALUES(new_id)');

		$this->mapCache[$type][$oldId] = $newId;
	}

	public function clearCache()
	{
		$this->mapCache = [];
		$this->mappedTypes = [];
	}

	public function canInitialize(&$error = null, $ignoreExists = false)
	{
		$sm = $this->db->getSchemaManager();

		if (!$this->isValidLogName())
		{
			$error = \XF::phrase('please_enter_valid_import_log_table_name');
			return false;
		}

		if ($sm->tableExists($this->table))
		{
			$error = \XF::phrase('specified_table_already_exists_choose_another');
			return false;
		}

		return true;
	}

	public function isValidLogName()
	{
		if (!$this->table || preg_match('#[^a-z0-9_]#i', $this->table))
		{
			return false;
		}

		return true;
	}

	public function initialize()
	{
		$sm = $this->db->getSchemaManager();
		$sm->createTable($this->table, function(\XF\Db\Schema\Create $table)
		{
			$table->addColumn('import_log_id', 'int')->autoIncrement();
			$table->addColumn('content_type', 'varbinary', 25);
			$table->addColumn('old_id', 'varbinary', 50);
			$table->addColumn('new_id', 'varbinary', 50);
			$table->addUniqueKey(['content_type', 'old_id'], 'content_type_old_id');
		});
	}

	public function initializeIfNeeded()
	{
		$sm = $this->db->getSchemaManager();
		if (!$sm->tableExists($this->table))
		{
			$this->initialize();
		}
	}

	public function isValidTable()
	{
		$sm = $this->db->getSchemaManager();

		if (!$this->isValidLogName())
		{
			return false;
		}

		if (!$sm->tableExists($this->table))
		{
			return false;
		}

		$columns = $sm->getTableColumnDefinitions($this->table);
		if (empty($columns['content_type']) || empty($columns['old_id']) || empty($columns['new_id']))
		{
			return false;
		}

		return true;
	}

	public function isEmpty()
	{
		$result = $this->db->fetchOne("
			SELECT 1
			FROM `" . $this->table . "`
			LIMIT 1
		");
		return $result ? false : true;
	}
}