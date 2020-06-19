<?php

namespace XF\Import\Data;

trait HasDeletionLogTrait
{
	protected $_deletionLogData = [
		'date' => null,
		'user_id' => 0,
		'username' => '',
		'reason' => ''
	];

	public function setDeletionLogData(array $data)
	{
		$this->_deletionLogData = array_replace($this->_deletionLogData, $data);
	}

	public function getDeletionLogData()
	{
		return $this->_deletionLogData;
	}
}