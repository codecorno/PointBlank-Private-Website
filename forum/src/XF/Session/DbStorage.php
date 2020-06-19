<?php

namespace XF\Session;

use XF\Db\AbstractAdapter;

class DbStorage implements StorageInterface
{
	/**
	 * @var \XF\Db\AbstractAdapter
	 */
	protected $db;

	protected $table;

	public function __construct(AbstractAdapter $db, $table)
	{
		$this->db = $db;
		$this->table = $table;
	}

	public function getSession($sessionId)
	{
		$result = $this->db->fetchOne('
			-- XFDB=fromWrite
			SELECT session_data
			FROM ' . $this->table . '
			WHERE session_id = ?
				AND expiry_date >= ?
		', [$sessionId, \XF::$time]);
		if ($result)
		{
			return @unserialize($result);
		}
		else
		{
			return false;
		}
	}

	public function deleteSession($sessionId)
	{
		$this->db->delete($this->table, 'session_id = ?', $sessionId);
	}

	public function writeSession($sessionId, array $data, $lifetime, $existing)
	{
		$this->db->query('
			-- XFDB=noForceAllWrite
			INSERT INTO ' . $this->table . '
				(session_id, session_data, expiry_date)
			VALUES
				(?, ?, ?)
			ON DUPLICATE KEY UPDATE
				session_data = VALUES(session_data),
				expiry_date = VALUES(expiry_date)
		', [$sessionId, serialize($data), \XF::$time + $lifetime]);
	}

	public function deleteExpiredSessions()
	{
		$this->db->delete($this->table, 'expiry_date < ?', \XF::$time);
	}
}