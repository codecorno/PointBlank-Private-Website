<?php

namespace XF\Session;

interface StorageInterface
{
	public function getSession($sessionId);

	public function deleteSession($sessionId);

	public function writeSession($sessionId, array $data, $lifetime, $existing);

	public function deleteExpiredSessions();
}