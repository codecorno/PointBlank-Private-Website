<?php

namespace XF\Session;

class NullStorage implements StorageInterface
{
	public function getSession($sessionId)
	{
		return false;
	}

	public function deleteSession($sessionId)
	{
	}

	public function writeSession($sessionId, array $data, $lifetime, $existing)
	{
	}

	public function deleteExpiredSessions()
	{
	}
}